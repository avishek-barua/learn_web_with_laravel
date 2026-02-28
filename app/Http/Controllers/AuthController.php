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

            // Store session fingerprint
            $fingerprint = $this->generateFingerprint($request);
            $request->session()->put('session_fingerprint', $fingerprint);

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

    // Crude way to generate fingerprint
    // private function generateFingerprint(Request $request): string
    // {
    //     $factors = [
    //         'user_agent' => $request->userAgent(),
    //         'accept_language' => $request->header('Accept-Language'),
    //         'accept_encoding' => $request->header('Accept-Encoding'),
    //     ];
    //     return hash('sha256', json_encode($factors));
    // }

    // More refined way to generate fingerprint
    private function generateFingerprint(Request $request): string
    {
        // Only use STABLE factors that don't change during normal use
        $factors = [
            'user_agent' => $this->normalizeUserAgent($request->userAgent()),
            // Don't use IP (changes too often)
            // Don't use Accept-Language (can change)
        ];

        return hash('sha256', json_encode($factors));
    }

    private function normalizeUserAgent(string $userAgent): string
    {
        // Extract only major browser and OS
        // Ignore minor version changes

        // Example: 
        // Chrome/120.0.0.0 → Chrome/120
        // Firefox/122.0 → Firefox/122

        if (preg_match('/Chrome\/(\d+)/', $userAgent, $matches)) {
            return "Chrome/{$matches[1]}";
        }

        if (preg_match('/Firefox\/(\d+)/', $userAgent, $matches)) {
            return "Firefox/{$matches[1]}";
        }

        if (preg_match('/Safari\/(\d+)/', $userAgent, $matches)) {
            return "Safari/{$matches[1]}";
        }

        // Fallback: use full user agent
        return $userAgent;
    }
}
