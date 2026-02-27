<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Step 1: Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Step 2: Create user with hashed password
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Step 3: Log the user in (create session)
        Auth::login($user);

        // Step 4: Redirect to dashboard
        return redirect('/dashboard')->with('Success', 'Registration Successful!');
    }

    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login form
    public function login(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // Regenerate session ID (prevent session fixation attack)
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        // Login failed
        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');

    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate csrf token
        $request->session()->regenerate();

        return redirect('/login');
    }
}
