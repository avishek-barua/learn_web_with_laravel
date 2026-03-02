<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ValidateSessionFingerprint
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (!Auth::check()) {
            return $next($request);
        }
        // Get stored fingerprint from session
        $storedFingerprint = $request->session()->get('session_fingerprint');

        // If no fingerprint stored yet. create one (first request after login)
        if (!$storedFingerprint) {
            $this->storeFingerprint($request);
            return $next($request);
        }

        //Generate current fingerprint
        $currentFingerprint = $this->generateFingerprint($request);

        // Compare fingerprints
        if ($storedFingerprint !== $currentFingerprint) {
            //Log the suspicious activity
            Log::warning('Session hijacking attept detected', [
                'user_id' => Auth::id(),
                'stored_fingerpring' => $storedFingerprint,
                'current_fingerprint' => $currentFingerprint,
                'ip' => $request->ip()
            ]);

            //Force logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            //Redirect with error
            return redirect()->route('login')->withErrors(['session' => 'Your session was terminated due to suspicious activity.']);
        }

        return $next($request);
    }


    private function generateFingerprint(Request $request): string
    {

        // Combine multiple factors
        $factors = [
            # Refined way
            'user_agent' => $this->normalizeUserAgent($request->userAgent()),

            # crude way
            // 'user_agent' => $request->userAgent(),
            // 'accept_language' => $request->header('Accept-Language'),
            // 'accept_encoding' => $request->header('Accept-Encoding')
        ];

        // Create a hash of combination factors
        return hash('sha256', json_encode($factors));
    }

    private function storeFingerprint(Request $request): void
    {
        $fingerprint = $this->generateFingerprint($request);
        $request->session()->put('session_fingerprint', $fingerprint);
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
