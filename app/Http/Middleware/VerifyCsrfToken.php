<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // OTP and forgot password routes are part of the web middleware group
        // and should have CSRF protection, but we can exclude them if needed
        // However, it's better to ensure CSRF tokens are properly sent from frontend
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // For axios requests, ensure we check both header and input
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            // Check if token is in header (X-CSRF-TOKEN) or input (_token)
            $token = $request->header('X-CSRF-TOKEN') 
                ?: $request->header('X-XSRF-TOKEN')
                ?: $request->input('_token');
            
            // If no token found but session exists, regenerate token
            if (!$token && $request->hasSession()) {
                $request->session()->regenerateToken();
            }
        }

        try {
            return parent::handle($request, $next);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            // If CSRF token mismatch, return a JSON response instead of redirecting
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'CSRF token mismatch. Please refresh the page and try again.',
                    'error' => 'CSRF token mismatch'
                ], 419);
            }
            throw $e;
        }
    }
}

