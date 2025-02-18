<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('layouts.register');
    }

    public function register(Request $request)
    {
        // Validate the form input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'user_type' => 'required|string|in:employer,applicant'
        ]);

        // Create and store the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Encrypt password
            'user_type' => $request['user_type'],
        ]);

        // Log in the user
        Auth::login($user);

        // Redirect to dashboard or home
        return redirect()->route('profile')->with('success', 'Registration successful!');
    }
}
