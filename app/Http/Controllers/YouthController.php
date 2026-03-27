<?php

namespace App\Http\Controllers;

use App\Models\Youth;
use App\Models\YouthAttachment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
// use Illuminate\Mail\Attachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class YouthController extends Controller
{
    /**
     * Display youth list with filters
     */
    public function index(Request $request)
    {

        // AUTO ARCHIVE AGE 31+
        Youth::whereDate('birthday', '<=', now()->subYears(31))
            ->update(['is_archived' => 1]);
        DB::table('youths')
            ->where('age', '>=', 31)
            ->update(['is_archived' => 1]);
        $query = Youth::query();
        $user = Auth::user();



        // ===============================
        // SK USERS SEE ONLY THEIR BARANGAY
        // ===============================
        if ($user && $user->role === 'sk') {

            if ($request->filled('transferred')) {

                // SK sees transferred FROM their barangay
                $query->where('previous_barangay', $user->barangay);

            } else {

                // SK sees active youth in their barangay
                $query->where('barangay', $user->barangay);

            }

        }
        // ===============================
        // ADMIN BARANGAY FILTER
        // ===============================
        if ($request->filled('barangay') && (!$user || $user->role !== 'sk')) {
            $query->where('barangay', $request->barangay);
        }

        // ===============================
        // SEX FILTER
        // ===============================
        if ($request->filled('sex')) {
            $query->where('sex', $request->sex);
        }

        // ===============================
        // MODE FILTER (ACTIVE / ARCHIVED / TRANSFERRED)
        // ===============================
        if ($request->filled('transferred')) {

            // TRANSFERRED PAGE
            $query->whereNotNull('previous_barangay')
                ->where('is_archived', 0);

        } elseif ($request->filled('archived')) {

            // ARCHIVED PAGE
            $query->where('is_archived', 1);

        } else {

            // ACTIVE PAGE
            $query->where('is_archived', 0)
                ->whereDate('birthday', '>', now()->subYears(31));

        }
        // ===============================
        // GET RESULTS
        // ===============================
        $youths = $query->with('attachments')
            ->orderBy('created_at', 'desc')
            ->get();

        // ===============================
        // BARANGAY POPULATION
        // ===============================
        $barangayPopulation = DB::table('barangay_populations')
            ->pluck('population', 'barangay')
            ->toArray();

        $allBarangays = DB::table('barangay_populations')
            ->pluck('barangay')
            ->toArray();
        // ===============================
        // PROFILES PER BARANGAY
        // ===============================
        $barangayProfiles = Youth::where('is_archived', 0)
            ->select('barangay', DB::raw('COUNT(*) as total'))
            ->groupBy('barangay')
            ->pluck('total', 'barangay')
            ->toArray();

        return view('youth.index', compact(
            'youths',
            'barangayProfiles',
            'barangayPopulation',
    'allBarangays'
        ));
    }

    public function create()
    {
        return view('youth.create');
    }

    /**
     * Store youth
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'sex' => 'required|in:Male,Female',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gender' => 'required|in:LGBTQAI+,Prefer not to say',
            'birthday' => 'required|date|before_or_equal:today',
            'civil_status' => 'required',
            'home_address' => 'required',
            'religion' => 'required',
            'religion_other' => 'required_if:religion,Others',
            'education' => 'required',
            'is_sk_voter' => 'required|in:Yes,No',
            'skills' => 'nullable',
            'preferred_skills' => 'nullable',
            'source_of_income' => 'nullable',
            'contact_number' => 'nullable',
            'region' => 'required',
            'province' => 'required',
            'municipality' => 'required',
            'barangay' => 'required',
            'purok_zone' => 'nullable|string',
            'attachments.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // 🔒 Force SK barangay
        if ($user && $user->role === 'sk') {
            $data['barangay'] = $user->barangay;
        }
        // 📸 Handle Profile Photo
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')
                ->store('profile_photos', 'public');
        }
        // 🔥 Calculate age + enforce 15–30 rule
        $birthday = Carbon::parse($data['birthday']);
        $age = $birthday->diffInYears(now());

        if ($age < 15 || $age > 30) {
            return back()->withErrors([
                'birthday' => 'Only ages 15 to 30 are allowed.'
            ])->withInput();
        }

        $data['age'] = $age;

        // 🔥 Handle "Others" religion
        if ($data['religion'] === 'Others') {
            $data['religion'] = $request->religion_other;
        }

        // 🔥 Boolean checkboxes
        $data['is_osy'] = $request->boolean('is_osy');
        $data['is_isy'] = $request->boolean('is_isy');
        $data['is_4ps'] = $request->boolean('is_4ps');
        $data['is_ip'] = $request->boolean('is_ip');
        $data['is_pwd'] = $request->boolean('is_pwd');

        $data['is_unemployed'] = $request->boolean('is_unemployed');
        $data['is_employed'] = $request->boolean('is_employed');
        $data['is_self_employed'] = $request->boolean('is_self_employed');

        $data['family_members'] = $request->family_members;


        $youth = Youth::create($data);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('youth_attachments', 'public');

                $youth->attachments()->create([
                    'file_path' => $path
                ]);
            }
        }

        return back()->with('success', 'Profile saved.');
    }

    /**
     * Update youth
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $youth = Youth::findOrFail($id);
        // Detect barangay transfer
        if ($youth->barangay !== $request->barangay) {
            $youth->previous_barangay = $youth->barangay;
        }
        if ($user && $user->role === 'sk' && $youth->barangay !== $user->barangay) {
            abort(403);
        }

        $data = $request->validate([
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sex' => 'required|in:Male,Female',
            'gender' => 'required|in:LGBTQAI+,Prefer not to say',
            'birthday' => 'required|date|before_or_equal:today',
            'civil_status' => 'required',
            'home_address' => 'required',
            'religion' => 'required',
            'education' => 'required',
            'is_sk_voter' => 'required|in:Yes,No',
            'skills' => 'nullable',
            'preferred_skills' => 'nullable',
            'source_of_income' => 'nullable',
            'contact_number' => 'nullable',
            'region' => 'required',
            'province' => 'required',
            'municipality' => 'required',
            'barangay' => 'required',
            'purok_zone' => 'nullable|string',
            'attachments.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // Calculate age temporarily for validation
        $birthday = Carbon::parse($data['birthday']);
        $age = $birthday->age;

        // Minimum age validation
        if ($age < 15) {
            return back()->withErrors([
                'birthday' => 'Youth must be at least 15 years old.'
            ]);
        }

        // Auto archive if 31+
        if ($age >= 31) {
            $data['is_archived'] = 1;
        }



        // Religion "Others"
        if ($data['religion'] === 'Others') {
            $data['religion'] = $request->religion_other;
        }

        // Preferred Skills "Others"
        if ($data['preferred_skills'] === 'Others') {
            $data['preferred_skills'] = $request->preferred_skills_other;
        }

        // Boolean fields
        foreach ([
            'is_osy',
            'is_isy',
            'is_4ps',
            'is_ip',
            'is_pwd',
            'is_unemployed',
            'is_employed',
            'is_self_employed'
        ] as $field) {
            $data[$field] = $request->boolean($field);
        }

        $data['family_members'] = $request->family_members;

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')
                ->store('profile_photos', 'public');
        }

        $youth->update($data);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('youth_attachments', 'public');

                $youth->attachments()->create([
                    'file_path' => $path
                ]);
            }
        }

        return back()->with('success', 'Profile updated.');
    }
    /**
     * Archive youth
     */
    public function archive(Request $request, $id)
    {
        $user = Auth::user();
        $youth = Youth::findOrFail($id);

        if ($user && $user->role === 'sk' && $youth->barangay !== $user->barangay) {
            abort(403);
        }

        $adminProtection = \App\Models\User::where('role', 'admin')
            ->value('action_protection');

        if ($user->role === 'sk' && $adminProtection) {

            $admin = \App\Models\User::where('role', 'admin')->first();

            if (
                !$request->filled('password') ||
                !Hash::check($request->password, $admin->password)
            ) {
                return back()->with('error', 'Incorrect admin password.');
            }
        }

        $youth->update(['is_archived' => 1]);

        return back()->with('success', 'Profile archived.');
    }

    /**
     * Restore youth
     */
    public function restore(Request $request, $id)
    {
        $user = Auth::user();
        $youth = Youth::findOrFail($id);

        if ($user && $user->role === 'sk' && $youth->barangay !== $user->barangay) {
            abort(403);
        }

        $adminProtection = \App\Models\User::where('role', 'admin')
            ->value('action_protection');

        if ($user->role === 'sk' && $adminProtection) {

            $admin = \App\Models\User::where('role', 'admin')->first();

            if (
                !$request->filled('password') ||
                !Hash::check($request->password, $admin->password)
            ) {
                return back()->with('error', 'Incorrect admin password.');
            }
        }

        $youth->update(['is_archived' => 0]);

        return back()->with('success', 'Profile restored.');
    }

    /**
     * Permanent delete
     */
    public function delete(Request $request, $id)
    {
        $user = Auth::user();
        $youth = Youth::findOrFail($id);

        if ($user->role !== 'admin') {
            abort(403);
        }

        if ($user->action_protection) {
            if (
                !$request->filled('password') ||
                !Hash::check($request->password, $user->password)
            ) {
                return back()->with('error', 'Incorrect admin password.');
            }
        }

        $youth->delete();

        return back()->with('success', 'Profile permanently deleted.');
    }

    /**
     * Export PDF
     */
    public function exportPDF($id)
    {
        $user = Auth::user();
        $youth = Youth::findOrFail($id);

        if ($user && $user->role === 'sk' && $youth->barangay !== $user->barangay) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.kk-form', compact('youth'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('KK_Profile_' . $youth->last_name . '.pdf');
    }

    /**
     * Direct print view
     */
    public function printView($id)
    {
        $user = Auth::user();
        $youth = Youth::findOrFail($id);

        if ($user && $user->role === 'sk' && $youth->barangay !== $user->barangay) {
            abort(403);
        }

        return view('youth.print', compact('youth'));
    }
    public function deleteAttachment($id)
    {
        $attachment = YouthAttachment::findOrFail($id);

        if (Storage::exists('public/' . $attachment->file_path)) {
            Storage::delete('public/' . $attachment->file_path);
        }

        $attachment->delete();

        return response()->json(['success' => true]);
    }
}
