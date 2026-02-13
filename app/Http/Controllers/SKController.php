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

    /**
     * Admin-only: Test sending SKCreated mail to any email address to diagnose mail issues.
     * Use: GET /mail/test?to=you@example.com  (must be logged in as admin)
     */
    public function testMail(Request $request)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'to' => 'required|email'
        ]);

        $to = $data['to'];
        $dummy = new User([ 'name' => 'SK Test', 'email' => $to, 'barangay' => 'Test Barangay' ]);
        $password = 'test-password';

        try {
            Mail::to($to)->send(new SKCreated($dummy, $password));
            return response()->json(['status' => 'ok', 'message' => 'Mail sent (check your inbox or maildev).']);
        } catch (\Exception $e) {
            logger()->error('Mail test failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
