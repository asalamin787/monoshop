<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // API Routes
            Route::middleware('api')
                ->prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));

            // Web Routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Admin Routes
            Route::middleware(['web', 'admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            // Customer Routes
            Route::middleware(['web', 'customer'])
                ->prefix('customer')
                ->name('customer.')
                ->group(base_path('routes/customer.php'));

            // Auth Routes
            Route::middleware('web')
                ->prefix('auth')
                ->name('auth.')
                ->group(base_path('routes/auth.php'));

            // Public API Routes (no auth required)
            Route::middleware('api')
                ->prefix('api/public')
                ->name('api.public.')
                ->group(base_path('routes/public-api.php'));
        });

        $this->configureRouteModelBinding();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // General API rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Auth rate limiting (for login, register, etc.)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Admin rate limiting (more lenient for admin users)
        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // Public API rate limiting (more restrictive)
        RateLimiter::for('public-api', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Global rate limiting
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000)->by($request->ip());
        });

        // Heavy operations rate limiting
        RateLimiter::for('heavy', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Configure route model binding.
     */
    protected function configureRouteModelBinding(): void
    {
        // Custom route model bindings
        Route::bind('user', function (string $value) {
            return \App\Models\User::where('id', $value)
                ->orWhere('email', $value)
                ->firstOrFail();
        });

        Route::bind('product', function (string $value) {
            return \App\Models\Product::where('id', $value)
                ->orWhere('slug', $value)
                ->firstOrFail();
        });

        Route::bind('category', function (string $value) {
            return \App\Models\Category::where('id', $value)
                ->orWhere('slug', $value)
                ->firstOrFail();
        });

        Route::bind('order', function (string $value) {
            return \App\Models\Order::where('id', $value)
                ->orWhere('order_number', $value)
                ->firstOrFail();
        });

        Route::bind('coupon', function (string $value) {
            return \App\Models\Coupon::where('id', $value)
                ->orWhere('code', $value)
                ->firstOrFail();
        });

        Route::bind('offer', function (string $value) {
            return \App\Models\Offer::where('id', $value)
                ->where('is_active', true)
                ->firstOrFail();
        });
    }

    /**
     * Define the "web" routes for the application.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "admin" routes for the application.
     */
    protected function mapAdminRoutes(): void
    {
        Route::prefix('admin')
            ->middleware(['web', 'admin'])
            ->name('admin.')
            ->group(base_path('routes/admin.php'));
    }

    /**
     * Define the "customer" routes for the application.
     */
    protected function mapCustomerRoutes(): void
    {
        Route::prefix('customer')
            ->middleware(['web', 'customer'])
            ->name('customer.')
            ->group(base_path('routes/customer.php'));
    }
}
