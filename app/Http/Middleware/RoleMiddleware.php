<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this area.');
        }

        $user = Auth::user();

        // If no roles specified, just check if user is authenticated
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if ($this->hasAnyRole($user, $roles)) {
            return $next($request);
        }

        // User doesn't have required role
        return redirect('/')->with('error', 'Access denied. Insufficient privileges.');
    }

    /**
     * Check if user has any of the specified roles
     */
    private function hasAnyRole($user, array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($user, $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has a specific role
     */
    private function hasRole($user, string $role): bool
    {
        // Method 1: Check by email for admin role
        if (strtolower($role) === 'admin') {
            $adminEmails = [
                'admin@monoshop.com',
                'admin@admin.com',
                'superadmin@monoshop.com'
            ];
            if (in_array($user->email, $adminEmails)) {
                return true;
            }
        }

        // Method 2: Check by role relationship (if implemented)
        // Uncomment this if you add role relationship to User model
        /*
        if ($user->roles()->where('name', ucfirst($role))->exists()) {
            return true;
        }
        */

        // Method 3: Check by user attribute (if you add role column)
        // if (isset($user->role) && strtolower($user->role) === strtolower($role)) {
        //     return true;
        // }

        // Default: all authenticated users are customers
        if (strtolower($role) === 'customer') {
            return true;
        }

        return false;
    }
}
