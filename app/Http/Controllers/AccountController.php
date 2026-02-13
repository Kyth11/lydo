<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('auth.edit-account', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ];

        // Only SK users may set or change their barangay
        if ($user->isSK()) {
            $rules['barangay'] = 'required|string|max:255';
        }

        $data = $request->validate($rules);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if ($user->isSK() && isset($data['barangay'])) {
            $user->barangay = $data['barangay'];
        }

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('dashboard')->with('success', 'Account updated.');
    }
}
