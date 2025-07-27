<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for Bearer token
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Bearer token is required'
            ], 401);
        }

        // Try to authenticate using Sanctum (if you're using it)
        // Uncomment this if you're using Laravel Sanctum
        /*
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or expired token'
            ], 401);
        }
        */

        // Basic token validation (you can customize this)
        if (!$this->validateToken($token)) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid token'
            ], 401);
        }

        return $next($request);
    }

    /**
     * Validate the API token
     */
    private function validateToken(string $token): bool
    {
        // Method 1: Simple token validation
        $validTokens = [
            'your-api-token-here',
            'monoshop-api-token-2025'
        ];

        if (in_array($token, $validTokens)) {
            return true;
        }

        // Method 2: Database token validation
        // You can check against a tokens table or user api_token field
        /*
        $user = \App\Models\User::where('api_token', $token)->first();
        if ($user) {
            Auth::setUser($user);
            return true;
        }
        */

        return false;
    }
}
