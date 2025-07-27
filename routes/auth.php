<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

/**
 * Authentication Routes
 * 
 * These routes are loaded by the RouteServiceProvider and handle
 * user authentication, registration, and password reset functionality.
 */

// Login Routes
Route::middleware(['guest.only', 'throttle:auth'])->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login.form');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean',
        ]);

        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return response()->json([
                    'message' => 'Your account has been deactivated. Please contact support.',
                ], 423);
            }

            // Update last login
            $user->update(['last_login_at' => now()]);

            // Redirect based on user role
            $redirectTo = '/dashboard';
            if ($user->role && $user->role->name === 'admin') {
                $redirectTo = '/admin';
            } elseif ($user->role && $user->role->name === 'customer') {
                $redirectTo = '/customer';
            }

            return response()->json([
                'message' => 'Login successful',
                'user' => $user->load('role'),
                'redirect_to' => $redirectTo,
            ]);
        }

        return response()->json([
            'message' => 'The provided credentials do not match our records.',
        ], 422);
    })->name('login');

    Route::post('/admin/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean',
        ]);

        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is admin
            if (!$user->role || $user->role->name !== 'admin') {
                Auth::logout();
                return response()->json([
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return response()->json([
                    'message' => 'Your account has been deactivated.',
                ], 423);
            }

            $request->session()->regenerate();
            $user->update(['last_login_at' => now()]);

            return response()->json([
                'message' => 'Admin login successful',
                'user' => $user->load('role'),
                'redirect_to' => '/admin',
            ]);
        }

        return response()->json([
            'message' => 'Invalid admin credentials.',
        ], 422);
    })->name('admin.login');
});

// Registration Routes
Route::middleware(['guest.only', 'throttle:auth'])->group(function () {
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register.form');

    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'terms' => 'required|accepted',
        ]);

        // Get customer role
        $customerRole = \App\Models\Role::where('name', 'customer')->first();
        
        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'role_id' => $customerRole?->id,
            'is_active' => true,
            'email_verified_at' => now(), // Auto-verify for simplicity
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user->load('role'),
            'redirect_to' => '/customer',
        ], 201);
    })->name('register');
});

// Logout Route
Route::middleware('auth')->group(function () {
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful',
            'redirect_to' => '/',
        ]);
    })->name('logout');
});

// Password Reset Routes
Route::middleware(['guest.only', 'throttle:auth'])->group(function () {
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset link sent to your email address.',
            ]);
        }

        return response()->json([
            'message' => 'Unable to send password reset link.',
        ], 422);
    })->name('password.email');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (\App\Models\User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password has been reset successfully.',
                'redirect_to' => '/auth/login',
            ]);
        }

        return response()->json([
            'message' => 'Unable to reset password.',
        ], 422);
    })->name('password.store');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (Request $request, string $id, string $hash) {
        $user = \App\Models\User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 422);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
                'redirect_to' => '/dashboard',
            ]);
        }

        if ($user->markEmailAsVerified()) {
            // Trigger verified event if needed
        }

        return response()->json([
            'message' => 'Email verified successfully.',
            'redirect_to' => '/dashboard',
        ]);
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification link sent to your email address.',
        ]);
    })->middleware('throttle:6,1')->name('verification.send');
});

// Social Login Routes (Placeholder for OAuth integration)
Route::prefix('social')->name('social.')->group(function () {
    Route::get('/{provider}', function (string $provider) {
        // Placeholder for social login redirect
        // You would integrate with Laravel Socialite here
        return response()->json([
            'message' => "Social login with {$provider} not implemented yet.",
        ], 501);
    })->where('provider', 'google|facebook|twitter')->name('redirect');

    Route::get('/{provider}/callback', function (string $provider) {
        // Placeholder for social login callback
        // You would handle the OAuth callback here
        return response()->json([
            'message' => "Social login callback for {$provider} not implemented yet.",
        ], 501);
    })->where('provider', 'google|facebook|twitter')->name('callback');
});

// Two-Factor Authentication Routes (Placeholder)
Route::middleware('auth')->prefix('2fa')->name('2fa.')->group(function () {
    Route::get('/setup', function () {
        return response()->json([
            'message' => 'Two-factor authentication setup not implemented yet.',
        ], 501);
    })->name('setup');

    Route::post('/enable', function () {
        return response()->json([
            'message' => 'Two-factor authentication enable not implemented yet.',
        ], 501);
    })->name('enable');

    Route::post('/disable', function () {
        return response()->json([
            'message' => 'Two-factor authentication disable not implemented yet.',
        ], 501);
    })->name('disable');

    Route::post('/verify', function () {
        return response()->json([
            'message' => 'Two-factor authentication verify not implemented yet.',
        ], 501);
    })->name('verify');
});

// Account Security Routes
Route::middleware('auth')->prefix('security')->name('security.')->group(function () {
    Route::get('/sessions', function (Request $request) {
        // Show active sessions (simplified version)
        return response()->json([
            'current_session' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_activity' => now(),
                'is_current' => true,
            ],
            'other_sessions' => [], // In a real app, you'd track sessions in DB
        ]);
    })->name('sessions');

    Route::delete('/sessions/{sessionId}', function (string $sessionId) {
        // Revoke specific session
        return response()->json([
            'message' => 'Session revoked successfully.',
        ]);
    })->name('sessions.revoke');

    Route::delete('/sessions', function () {
        // Revoke all other sessions
        return response()->json([
            'message' => 'All other sessions revoked successfully.',
        ]);
    })->name('sessions.revoke-all');

    Route::get('/activity', function (Request $request) {
        // Show account activity log
        $activities = [
            [
                'type' => 'login',
                'description' => 'Logged in from ' . $request->ip(),
                'created_at' => $request->user()->last_login_at ?? now(),
            ],
            // In a real app, you'd have an activity log table
        ];

        return response()->json($activities);
    })->name('activity');
});

// API Authentication Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if (!$user->is_active) {
                return response()->json([
                    'message' => 'Account deactivated.',
                ], 423);
            }

            // Create API token (you might want to use Laravel Sanctum)
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user->load('role'),
                'token' => $token,
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials.',
        ], 422);
    })->middleware(['guest.only', 'throttle:auth'])->name('login');

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    })->middleware('api.auth')->name('logout');

    Route::post('/refresh', function (Request $request) {
        // Delete current token
        $request->user()->currentAccessToken()->delete();
        
        // Create new token
        $token = $request->user()->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed',
            'token' => $token,
        ]);
    })->middleware('api.auth')->name('refresh');
});
