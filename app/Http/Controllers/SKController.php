<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\SKCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SKController extends Controller
{
    public function create()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('auth.create-sk');
    }

    public function store(Request $request)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'barangay' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);

        $password = $data['password'] ?? Str::random(10);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'role' => 'sk',
            'barangay' => $data['barangay'],
        ]);

        // Send email with credentials and report status
        $mailSent = true;
        try {
            Mail::to($user->email)->send(new SKCreated($user, $password));
        } catch (\Exception $e) {
            // Log and mark as not sent
            logger()->error('Failed to send SK creation email: ' . $e->getMessage());
            $mailSent = false;
        }

        if ($mailSent) {
            return redirect()->route('dashboard')->with('success', 'SK account created and notified.');
        }

        return redirect()->route('dashboard')->with('warning', 'SK account created but email was not sent. Please check mail configuration or logs.');
    }


          public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $sks = User::where('role', 'sk')->get();

        return view('sk.manage', compact('sks'));
    }

    public function toggle(Request $request, $id)
    {
        $admin = Auth::user();

        if (!$admin->isAdmin()) {
            abort(403);
        }

        if (!Hash::check($request->password, $admin->password)) {
            return back()->with('error', 'Incorrect admin password.');
        }

        $sk = User::where('role', 'sk')->findOrFail($id);

        $sk->is_disabled = !$sk->is_disabled;
        $sk->save();

        return back()->with('success', 'SK account status updated.');
    }

    public function testMail(Request $request)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            Mail::raw('This is a test email from your Laravel application on Hostinger. If you receive this, your email configuration is working correctly!', function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Test Email - Hostinger Mail Configuration')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            return back()->with('success', 'Test email sent successfully to ' . $request->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}


