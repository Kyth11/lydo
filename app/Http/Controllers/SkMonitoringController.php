<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Category;

class SkMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // =========================
        // ADMIN VIEW
        // =========================
        if ($user->isAdmin()) {

            $query = Report::with('category');

            // SEARCH
            if ($request->search) {
                $query->where('description', 'like', '%' . $request->search . '%');
            }

            // FILTERS
            if ($request->barangay) {
                $query->where('barangay', $request->barangay);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->category) {
                $query->where('category_id', $request->category);
            }

            if ($request->date) {
                $query->whereDate('created_at', $request->date);
            }

            // ONLY LATEST REPORT PER CATEGORY + BARANGAY
            $reports = $query->latest()
                ->get()
                ->groupBy(fn($r) => $r->barangay . '-' . $r->category_id)
                ->map(fn($group) => $group->sortByDesc('version')->first());

            $barangays = Report::select('barangay')->distinct()->pluck('barangay');

            $categories = Category::all();

            // =========================
            // DURATIONS (ONLY UNIQUE + VALID)
            // =========================
            $categories = Category::all();

            // Build category durations map
            $categoryDurations = $categories->mapWithKeys(function ($cat) {

                if (!$cat->start_date || !$cat->end_date) {
                    return [$cat->id => null];
                }

                $start = \Carbon\Carbon::parse($cat->start_date);
                $end = \Carbon\Carbon::parse($cat->end_date);

                if ($start->year === $end->year) {
                    if ($start->month === $end->month) {
                        $label = $start->format('F Y');
                    } else {
                        $label = $start->format('F Y') . ' - ' . $end->format('F Y');
                    }
                } else {
                    $label = $start->format('Y') . ' - ' . $end->format('Y');
                }

                return [$cat->id => $label];
            });

            // Count how many categories share each duration
            $durationCounts = $categoryDurations
                ->filter()
                ->countBy()
                ->filter(fn($count) => $count > 1);

            // Only keep durations that appear more than once
            $durations = $categoryDurations
                ->filter()
                ->filter(fn($label) => $durationCounts[$label] ?? false)
                ->unique()
                ->values();

            return view('admin.monitoring', compact(
                'reports',
                'barangays',
                'categories',
                'durations'
            ));
        }

        // =========================
        // SK VIEW
        // =========================
        if ($user->role === 'sk') {

            $reports = Report::with('category')
                ->where('barangay', $user->barangay)
                ->latest()
                ->get()
                ->groupBy(fn($report) => $report->category->name ?? 'No Category');

            $categories = Category::all();

            return view('sk.monitoring', compact('reports', 'categories'));
        }

        abort(403);
    }

    // =========================
    // STORE REPORT
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
            'attachment' => 'nullable|array',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120'
        ]);

        $paths = [];

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('reports', $filename, 'public');
                    $paths[] = $path;
                }
            }
        }

        $user = auth()->user();

        // 🔥 CHECK EXISTING REPORT (same barangay + category)
        $existingReport = Report::where('barangay', $user->barangay)
            ->where('category_id', $request->category_id)
            ->latest()
            ->first();

        // 🔥 VERSIONING LOGIC
        $version = 1;

        if ($existingReport) {
            $version = $existingReport->version + 1;

            // OPTIONAL: store previous version as history (if you add history table later)
        }

        // 🔥 DEADLINE CHECK
        $category = Category::find($request->category_id);
        $isLate = false;

        if ($category && $category->deadline && now()->gt($category->deadline)) {
            $isLate = true;
        }

        // 🔥 CREATE NEW VERSION
        Report::create([
            'user_id' => $user->id,
            'barangay' => $user->barangay,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'files' => $paths,
            'version' => $version,      // ✅ VERSION COLUMN
            'is_late' => $isLate        // ✅ LATE FLAG
        ]);

        return back()->with(
            $isLate ? 'warning' : 'success',
            $isLate
            ? "Deadline is passed, it will be marked late (v{$version})"
            : "Report submitted successfully (v{$version})"
        );
    }

    // =========================
    // ADMIN REVIEW
    // =========================
    public function updateStatus(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'status' => 'required',
            'admin_comment' => 'nullable'
        ]);

        $report = Report::findOrFail($request->report_id);

        $report->update([
            'status' => $request->status,
            'admin_comment' => $request->admin_comment
        ]);

        return back()->with('success', 'Report updated successfully');
    }

    // =========================
    // DELETE REPORT
    // =========================
    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        $report->delete();

        return back()->with('success', 'Report deleted');
    }

    // =========================
    // UPDATE REPORT
    // =========================
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id', // ✅ FIXED
            'description' => 'nullable',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);

        $existing = json_decode($request->existing_files ?? '[]', true);
        $paths = $existing;

        $hasNewFiles = false;

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $paths[] = $file->store('reports', 'public');
                $hasNewFiles = true;
            }
        }

        // ✅ Detect edits
        $report->is_edited = false;

        if (
            $report->description !== $request->description ||
            $report->category_id !== $request->category_id ||
            $hasNewFiles
        ) {
            $report->is_edited = true;
        }

        $report->update([
            'category_id' => $request->category_id, // ✅ FIXED
            'description' => $request->description,
            'files' => $paths,
            'is_edited' => $report->is_edited
        ]);

        return back()->with('success', 'Report updated successfully');
    }

    public function versions(Request $request)
    {
        $reports = Report::where('category_id', $request->category)
            ->where('barangay', $request->barangay)
            ->orderBy('version')
            ->get();

        return view('admin.report-versions', compact('reports'));
    }
}
