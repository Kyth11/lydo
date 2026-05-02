<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Report;
use App\Models\Category;
use App\Models\NudgeNotification;

class SkMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // =========================
        // ADMIN VIEW
        // =========================
        if ($user->isAdmin()) {

            $query = Report::with('category');

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

            if ($request->month) {
                $query->whereMonth('created_at', $request->month);
            }

            if ($request->year) {
                $query->whereYear('created_at', $request->year);
            }

            // ONLY LATEST REPORT PER CATEGORY + BARANGAY
            $reports = $query->latest()
                ->get()
                ->groupBy(fn($r) => $r->barangay . '-' . $r->category_id)
                ->map(fn($group) => $group->first());

            $barangays = DB::table('barangay_populations')->orderBy('barangay')->pluck('barangay');

            $categories = Category::where('is_active', true)->get();

            // =========================
            // DURATIONS (ONLY UNIQUE + VALID)
            // =========================

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

            $query = Report::with('category')
                ->where('barangay', $user->barangay);

            // STATUS FILTER
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // MONTH FILTER
            if ($request->month) {
                $query->whereMonth('created_at', $request->month);
            }

            // YEAR FILTER
            if ($request->year) {
                $query->whereYear('created_at', $request->year);
            }

            $reports = $query->latest()
                ->get()
                ->groupBy(fn($report) => $report->category->name ?? 'No Category');

            $categories = Category::where('is_active', true)->get();

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

        $user = Auth::user();

        // 🔥 DEADLINE CHECK
        $category = Category::find($request->category_id);
        $isLate = false;

        if ($category && $category->deadline && now()->gt($category->deadline)) {
            $isLate = true;
        }

        // 🔥 CREATE REPORT
        $user = Auth::user();
        Report::create([
            'user_id' => $user->id,
            'barangay' => $user->barangay,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'files' => $paths,
            'is_late' => $isLate        // ✅ LATE FLAG
        ]);

        return back()->with(
            $isLate ? 'warning' : 'success',
            $isLate
            ? "Deadline is passed, it will be marked late"
            : "Report submitted successfully"
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

        if ($report->user_id !== Auth::user()->id) {
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

        if ($report->user_id !== Auth::user()->id) {
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

    // =========================
    // SEND NUDGE
    // =========================
    public function sendNudge(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'barangays' => 'required|array',
            'barangays.*' => 'string',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'message' => 'nullable|string',
        ]);

        foreach ($request->barangays as $barangay) {
            NudgeNotification::create([
                'admin_id' => Auth::id(),
                'barangay' => $barangay,
                'category_ids' => $request->category_ids,
                'message' => $request->message,
            ]);
        }

        return back()->with('success', 'Nudge sent successfully');
    }

    // =========================
    // GET NUDGES
    // =========================
    public function getNudges()
    {
        $user = Auth::user();

        if ($user->role !== 'sk') {
            return response()->json([]);
        }

        $nudges = NudgeNotification::where('barangay', $user->barangay)
            ->where('created_at', '>=', now()->subDay())
            ->where('is_cleared', false)
            ->latest()
            ->limit(10)
            ->get();

        // Split categories into separate notifications
        $flattenedNudges = [];
        foreach ($nudges as $nudge) {
            foreach ($nudge->category_ids as $categoryId) {
                $category = Category::find($categoryId);
                
                // Calculate duration label
                $durationLabel = '';
                if ($category && $category->start_date && $category->end_date) {
                    $start = \Carbon\Carbon::parse($category->start_date);
                    $end = \Carbon\Carbon::parse($category->end_date);
                    
                    if ($start->year === $end->year) {
                        if ($start->month === $end->month) {
                            $durationLabel = $start->format('F');
                        } else {
                            $durationLabel = $start->format('F') . '-' . $end->format('F');
                        }
                    } else {
                        $durationLabel = $start->format('Y') . '-' . $end->format('Y');
                    }
                }
                
                $flattenedNudges[] = [
                    'id' => $nudge->id,
                    'category_id' => $categoryId,
                    'category_name' => $category ? $category->name : 'Unknown Category',
                    'duration' => $durationLabel,
                    'message' => $nudge->message,
                    'created_at' => $nudge->created_at,
                ];
            }
        }

        return response()->json($flattenedNudges);
    }

    // =========================
    // CLEAR NUDGE
    // =========================
    public function clearNudge($id)
    {
        $user = Auth::user();

        if ($user->role !== 'sk') {
            abort(403);
        }

        $nudge = NudgeNotification::where('id', $id)
            ->where('barangay', $user->barangay)
            ->firstOrFail();

        $nudge->update(['is_cleared' => true]);

        return response()->json(['success' => true]);
    }

    // =========================
    // CLEAR ALL NUDGES
    // =========================
    public function clearAllNudges()
    {
        $user = Auth::user();

        if ($user->role !== 'sk') {
            abort(403);
        }

        NudgeNotification::where('barangay', $user->barangay)
            ->where('is_cleared', false)
            ->update(['is_cleared' => true]);

        return response()->json(['success' => true]);
    }

    // =========================
    // CHECK REPORT EXISTS
    // =========================
    public function checkReport($categoryId)
    {
        $user = Auth::user();

        if ($user->role !== 'sk') {
            abort(403);
        }

        $exists = Report::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    }
