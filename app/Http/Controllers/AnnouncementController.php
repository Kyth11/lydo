<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        $barangays = [
            'AWANG',
            'BAGOCBOC',
            'BARRA',
            'BONBON',
            'CAUYUNAN',
            'IGPIT',
            'LIMUNDA',
            'LUYONG BONBON',
            'MALANANG',
            'NANGCAON',
            'PATAG',
            'POBLACION',
            'TABOC',
            'TINGALAN'
        ];

        return view('announcements.create', compact('barangays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'scope' => 'required'
        ]);

        Announcement::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'for_all_barangays' => $request->scope === 'all',
            'barangay' => $request->scope === 'all'
                ? null
                : $request->barangays // stored as JSON array
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    // ✅ THIS WAS MISSING
    public function edit(Announcement $announcement)
    {
        $barangays = [
            'AWANG',
            'BAGOCBOC',
            'BARRA',
            'BONBON',
            'CAUYUNAN',
            'IGPIT',
            'LIMUNDA',
            'LUYONG BONBON',
            'MALANANG',
            'NANGCAON',
            'PATAG',
            'POBLACION',
            'TABOC',
            'TINGALAN'
        ];

        return view('announcements.edit', compact('announcement', 'barangays'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'scope' => 'required'
        ]);

        $announcement->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'for_all_barangays' => $request->scope === 'all',
            'barangay' => $request->scope === 'all'
                ? null
                : $request->barangays
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted.');
    }

    public function welcome()
{
    $barangays = [
        'AWANG','BAGOCBOC','BARRA','BONBON','CAUYUNAN','IGPIT',
        'LIMUNDA','LUYONG BONBON','MALANANG','NANGCAON',
        'PATAG','POBLACION','TABOC','TINGALAN'
    ];

    $selectedBarangay = request('barangay');

    $query = Announcement::active();

    if ($selectedBarangay) {
        $query->where(function ($q) use ($selectedBarangay) {
            $q->where('for_all_barangays', true)
              ->orWhereJsonContains('barangay', strtoupper($selectedBarangay));
        });
    }

    $announcements = $query->get();

    return view('welcome', compact('barangays','announcements','selectedBarangay'));
}

}
