<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        // Check if user is verified (optional)
        $user = Auth::user();
        
        if (!$user->email_verified_at && config('auth.verify_email', false)) {
            return redirect()->route('verification.notice')
                ->with('warning', 'Please verify your email address to continue.');
        }

        // Additional customer-specific checks can be added here
        // For example, check if account is active, not banned, etc.
        
        return $next($request);
    }
}
