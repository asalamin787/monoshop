<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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
            return redirect()->route('login')->with('error', 'Please login to access this area.');
        }

        // Check if user is admin (you can customize this logic based on your needs)
        $user = Auth::user();
        
        // Option 1: Check by email (simple approach)
        if (!$this->isAdmin($user)) {
            return redirect('/')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }

    /**
     * Check if user is admin
     */
    private function isAdmin($user): bool
    {
        // Method 1: Check by email domain or specific emails
        $adminEmails = [
            'admin@monoshop.com',
            'admin@admin.com',
            'superadmin@monoshop.com'
        ];

        if (in_array($user->email, $adminEmails)) {
            return true;
        }

        // Method 2: Check if user has admin role (if you implement roles relationship)
        // Uncomment this if you add role relationship to User model
        // if ($user->roles()->where('name', 'Admin')->exists()) {
        //     return true;
        // }

        // Method 3: Check by user attribute (if you add is_admin column)
        // if (isset($user->is_admin) && $user->is_admin) {
        //     return true;
        // }

        return false;
    }
}
