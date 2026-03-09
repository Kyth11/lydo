<?php

namespace App\Http\Controllers;

use App\Models\Youth;
use Illuminate\Http\Request;

class PublicYouthController extends Controller
{


    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'sex' => 'required|in:Male,Female',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gender' => 'required|in:LGBTQAI+,Prefer not to say',
            'birthday' => 'required|date|before_or_equal:today',
            // 'age' => 'nullable|integer',
            'civil_status' => 'required',
            'home_address' => 'required',
            'religion' => 'required',
            'religion_other' => 'nullable',
            'education' => 'required',
            'is_sk_voter' => 'required|in:Yes,No',
            'skills' => 'nullable',
            'preferred_skills' => 'nullable',
            'preferred_skills_other' => 'nullable',
            'source_of_income' => 'nullable',
            'contact_number' => 'nullable',
            'region' => 'required',
            'province' => 'required',
            'municipality' => 'required',
            'barangay' => 'required',
            'purok_zone' => 'nullable|string',
        ]);
        $validated['age'] = \Carbon\Carbon::parse($request->birthday)->age;
        // Boolean fields
        $validated['is_osy'] = $request->has('is_osy');
        $validated['is_isy'] = $request->has('is_isy');
        $validated['is_4ps'] = $request->has('is_4ps');
        $validated['is_ip'] = $request->has('is_ip');
        $validated['is_pwd'] = $request->has('is_pwd');
        $validated['is_unemployed'] = $request->has('is_unemployed');
        $validated['is_employed'] = $request->has('is_employed');
        $validated['is_self_employed'] = $request->has('is_self_employed');

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] =
                $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $youth = Youth::create($validated);

// Save Family (same format as YouthController)
        $family = [];

        if ($request->has('family_members')) {
            foreach ($request->family_members as $member) {
                if (!empty($member['name'])) {
                    $family[] = $member;
                }
            }
        }

        $youth->family_members = $family;
        $youth->save();

        // Save Attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $youth->attachments()->create(['file_path' => $path]);
            }
        }

        return redirect()->route('kk.register')
            ->with('success', 'Registration submitted successfully.');
    }
}
