<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GuestOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = null): Response
    {
        // If user is authenticated, redirect them away from guest-only pages
        if (Auth::guard($guard)->check()) {
            // Determine redirect location based on user type
            $user = Auth::user();
            
            // If admin, redirect to admin dashboard
            if ($this->isAdmin($user)) {
                return redirect('/admin');
            }
            
            // For regular users, redirect to home or dashboard
            return redirect('/dashboard')->with('info', 'You are already logged in.');
        }

        return $next($request);
    }

    /**
     * Check if user is admin
     */
    private function isAdmin($user): bool
    {
        $adminEmails = [
            'admin@monoshop.com',
            'admin@admin.com',
            'superadmin@monoshop.com'
        ];

        return in_array($user->email, $adminEmails);
    }
}
