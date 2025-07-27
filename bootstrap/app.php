<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Additional route files
            Route::middleware(['web', 'admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware(['web', 'customer'])
                ->prefix('customer')
                ->name('customer.')
                ->group(base_path('routes/customer.php'));

            Route::middleware('web')
                ->prefix('auth')
                ->name('auth.')
                ->group(base_path('routes/auth.php'));

            Route::middleware('api')
                ->prefix('api/public')
                ->name('api.public.')
                ->group(base_path('routes/public-api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'guest.only' => \App\Http\Middleware\GuestOnlyMiddleware::class,
            'api.auth' => \App\Http\Middleware\ApiAuthMiddleware::class,
            'maintenance' => \App\Http\Middleware\MaintenanceMiddleware::class,
        ]);

        // Add global middleware (runs on every request)
        $middleware->append(\App\Http\Middleware\MaintenanceMiddleware::class);

        // Configure API middleware groups
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
