<?php

namespace App\Http\Controllers;

use App\Models\Youth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class YouthController extends Controller
{
    /**
     * Display youth list with filters
     */
    public function index(Request $request)
    {
        $query = Youth::query();
        $user = Auth::user();

        // SK sees only their barangay
        if ($user && $user->role === 'sk') {
            $query->where('barangay', $user->barangay);
        }

        // Admin barangay filter
        if ($request->filled('barangay') && (!$user || $user->role !== 'sk')) {
            $query->where('barangay', $request->barangay);
        }

        // Sex filter
        if ($request->filled('sex')) {
            $query->where('sex', $request->sex);
        }

        // Archived filter
        if ($request->filled('archived')) {
            $query->where('is_archived', 1);
        } else {
            $query->where('is_archived', 0);
        }

        $youths = $query->orderBy('created_at', 'desc')->get();

        return view('youth.index', compact('youths'));
    }

    public function create()
    {
        return view('youth.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'sex' => 'required|in:Male,Female',
            'birthday' => 'required|date|before_or_equal:today',
            'home_address' => 'required',
            'religion' => 'nullable',
            'education' => 'required',
            'skills' => 'nullable',
            'source_of_income' => 'nullable',
            'contact_number' => 'nullable',
            'region' => 'required',
            'province' => 'required',
            'municipality' => 'required',
            'barangay' => 'required',
        ]);

        if ($user && $user->role === 'sk') {
            $data['barangay'] = $user->barangay;
        }

        $birthday = Carbon::parse($data['birthday']);
        $data['age'] = $birthday->diffInYears(now());

        $data['is_osy'] = $request->boolean('is_osy');
        $data['is_isy'] = $request->boolean('is_isy');
        $data['is_working_youth'] = $request->boolean('is_working_youth');
        $data['family_members'] = $request->family_members;

        Youth::create($data);

        return back()->with('success', 'Profile saved.');
    }

    /**
     * Update youth
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $youth = Youth::findOrFail($id);

        if ($user && $user->role === 'sk' && $youth->barangay !== $user->barangay) {
            abort(403);
        }

        $data = $request->validate([
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'sex' => 'required|in:Male,Female',
            'birthday' => 'required|date|before_or_equal:today',
            'home_address' => 'required',
            'religion' => 'nullable',
            'contact_number' => 'nullable',
            'region' => 'required',
            'province' => 'required',
            'municipality' => 'required',
            'barangay' => 'required',
        ]);

        if ($user && $user->role === 'sk') {
            $data['barangay'] = $user->barangay;
        }

        $birthday = Carbon::parse($data['birthday']);
        $data['age'] = $birthday->diffInYears(now());

        $youth->update($data);

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

        // 🔥 Get admin protection setting
        $adminProtection = \App\Models\User::where('role', 'admin')
            ->value('action_protection');

        // 🔥 If SK and protection enabled → require admin password
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

        // SK can only restore within their barangay
        if ($user && $user->role === 'sk' && $youth->barangay !== $user->barangay) {
            abort(403);
        }

        // 🔥 Get admin protection setting
        $adminProtection = \App\Models\User::where('role', 'admin')
            ->value('action_protection');

        // 🔥 If SK and protection enabled → require admin password
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

        // Optional: restrict delete to Admin only
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

        $youth->delete(); // REAL permanent delete

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

        return $pdf->stream('KK_Profile_' . $youth->last_name . '.pdf');
    }
}
