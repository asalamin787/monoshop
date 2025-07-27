<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled
        $maintenanceMode = $this->isMaintenanceModeEnabled();
        
        if ($maintenanceMode && !$this->isExemptUser()) {
            // Check if this is an API request
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Service Unavailable',
                    'message' => 'The application is currently under maintenance. Please try again later.'
                ], 503);
            }

            // Return maintenance view for web requests
            return response()->view('maintenance', [
                'title' => 'Site Under Maintenance',
                'message' => 'We are currently performing scheduled maintenance. Please check back soon.'
            ], 503);
        }

        return $next($request);
    }

    /**
     * Check if maintenance mode is enabled
     */
    private function isMaintenanceModeEnabled(): bool
    {
        try {
            $setting = Setting::where('key', 'maintenance_mode')->first();
            return $setting && $setting->value === 'true';
        } catch (\Exception $e) {
            // If we can't check the database, assume not in maintenance mode
            return false;
        }
    }

    /**
     * Check if current user is exempt from maintenance mode
     */
    private function isExemptUser(): bool
    {
        // Allow admins to bypass maintenance mode
        if (Auth::check()) {
            $user = Auth::user();
            $adminEmails = [
                'admin@monoshop.com',
                'admin@admin.com',
                'superadmin@monoshop.com'
            ];
            
            if (in_array($user->email, $adminEmails)) {
                return true;
            }
        }

        // Allow specific IP addresses to bypass
        $exemptIPs = [
            '127.0.0.1',
            '::1',
            // Add your development IP addresses here
        ];

        if (in_array(request()->ip(), $exemptIPs)) {
            return true;
        }

        return false;
    }
}
