<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function loginPage() {
        return view('auth.login');
    }

    public function login(Request $request) {
        if ($request->email === 'admin@opol.gov.ph' && $request->password === 'admin123') {
            Session::put('admin', true);
            return redirect('/dashboard');
        }

        return back()->with('error','Invalid credentials');
    }

    public function logout() {
        Session::flush();
        return redirect('/login');
    }
}
