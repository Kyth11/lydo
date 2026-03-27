<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class SkMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Report::query();

        // FILTERS (ADMIN ONLY)
        if ($user->isAdmin()) {

            if ($request->search) {
                $query->where('description', 'like', '%' . $request->search . '%');
            }
            if ($request->barangay) {
                $query->where('barangay', $request->barangay);
            }

            if ($request->category) {
                $query->where('category', $request->category);
            }

            if ($request->date) {
                $query->whereDate('created_at', $request->date);
            }

            $reports = $query->latest()
                ->get()
                ->groupBy(['barangay', 'category']);

            $barangays = Report::select('barangay')->distinct()->pluck('barangay');

            return view('admin.monitoring', compact('reports', 'barangays'));
        }

        // SK VIEW
        if ($user->role === 'sk') {
            $reports = $query
                ->where('barangay', $user->barangay)
                ->latest()
                ->get()
                ->groupBy('category');

            return view('sk.monitoring', compact('reports'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'description' => 'nullable',
            'attachment' => 'nullable|array',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120'
        ]);

        $paths = [];

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {

                if ($file->isValid()) {

                    // ✅ ORIGINAL NAME
                    $originalName = $file->getClientOriginalName();

                    // 🔥 OPTIONAL: prevent duplicate overwrite
                    $filename = time() . '_' . $originalName;

                    // STORE
                    $path = $file->storeAs('reports', $filename, 'public');

                    $paths[] = $path;
                }
            }
        }

        Report::create([
            'user_id' => auth()->id(),
            'barangay' => auth()->user()->barangay,
            'category' => $request->category,
            'description' => $request->description,
            'files' => $paths
        ]);

        return back()->with('success', 'Report submitted successfully');
    }

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

    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        $report->delete();

        return back()->with('success', 'Report deleted');
    }
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        // Only the owner SK can update
        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'category' => 'required',
            'description' => 'nullable',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);

        // Get existing files from hidden input
        $existing = json_decode($request->existing_files ?? '[]', true);
        $paths = $existing;

        $hasNewFiles = false;

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $paths[] = $file->store('reports', 'public');
                $hasNewFiles = true;
            }
        }

        // ✅ Determine if report is actually edited
        $report->is_edited = false; // reset

        if (
            $report->description !== $request->description ||
            $report->category !== $request->category ||
            $hasNewFiles
        ) {
            $report->is_edited = true;
        }

        // Update the report
        $report->update([
            'category' => $request->category,
            'description' => $request->description,
            'files' => $paths,
            'is_edited' => $report->is_edited
        ]);

        return back()->with('success', 'Report updated successfully');
    }
}
