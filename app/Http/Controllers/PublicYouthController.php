<?php

namespace App\Http\Controllers;

use App\Models\Youth;
use Illuminate\Http\Request;

class PublicYouthController extends Controller
{
    public function create()
    {
        return view('kk-register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'sex' => 'required|in:Male,Female',
            'birthday' => 'required|date',
            'home_address' => 'required',
            'religion' => 'nullable',
            'education' => 'required',
            'region' => 'required',
            'province' => 'required',
            'municipality' => 'required',
            'barangay' => 'required',
            'purok_zone' => 'nullable',
            'family_members' => 'nullable|array',
        ]);

        Youth::create($validated);

        return redirect()->route('kk.register')
            ->with('success', 'Registration submitted successfully.');
    }
}
