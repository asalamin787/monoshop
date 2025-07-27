# MonoShop Authentication Middleware Guide

This document explains how to use the custom authentication middleware created for the MonoShop e-commerce application.

## Available Middleware

### 1. AdminMiddleware (`admin`)
**Purpose:** Restricts access to admin-only areas
**Usage:** `Route::middleware(['admin'])->group(...)`

**Features:**
- Checks if user is authenticated
- Verifies admin privileges (by email or role)
- Redirects non-admin users with error message

**Example:**
```php
Route::middleware(['admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/users', [AdminController::class, 'users']);
});
```

### 2. CustomerMiddleware (`customer`)
**Purpose:** Ensures user is authenticated (basic auth check)
**Usage:** `Route::middleware(['customer'])->group(...)`

**Features:**
- Checks if user is authenticated
- Optional email verification check
- Redirects to login if not authenticated

**Example:**
```php
Route::middleware(['customer'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/orders', [OrderController::class, 'index']);
});
```

### 3. RoleMiddleware (`role`)
**Purpose:** Flexible role-based access control
**Usage:** `Route::middleware(['role:admin,manager'])->group(...)`

**Features:**
- Accepts multiple roles as parameters
- Checks if user has any of the specified roles
- Flexible role checking system

**Example:**
```php
// Single role
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin-only', [AdminController::class, 'index']);
});

// Multiple roles (user needs ANY of these roles)
Route::middleware(['role:admin,manager,supervisor'])->group(function () {
    Route::get('/management', [ManagementController::class, 'index']);
});
```

### 4. GuestOnlyMiddleware (`guest.only`)
**Purpose:** Redirects authenticated users away from guest-only pages
**Usage:** `Route::middleware(['guest.only'])->group(...)`

**Features:**
- Redirects authenticated users to appropriate dashboard
- Differentiates between admin and regular users
- Useful for login/register pages

**Example:**
```php
Route::middleware(['guest.only'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword']);
});
```

### 5. ApiAuthMiddleware (`api.auth`)
**Purpose:** Authenticates API requests using Bearer tokens
**Usage:** `Route::middleware(['api.auth'])->group(...)`

**Features:**
- Validates Bearer tokens
- Returns JSON error responses
- Configurable token validation

**Example:**
```php
Route::prefix('api')->middleware(['api.auth'])->group(function () {
    Route::get('/products', [ApiController::class, 'products']);
    Route::post('/orders', [ApiController::class, 'createOrder']);
});
```

### 6. MaintenanceMiddleware (`maintenance`)
**Purpose:** Shows maintenance page when site is under maintenance
**Usage:** Automatically applied globally

**Features:**
- Checks database setting for maintenance mode
- Exempts admin users and specific IPs
- Shows custom maintenance page
- Returns JSON response for API requests

**To enable maintenance mode:**
```php
// In database or via Filament admin panel
Setting::where('key', 'maintenance_mode')->update(['value' => 'true']);
```

## Middleware Registration

All middleware are registered in `bootstrap/app.php`:

```php
$middleware->alias([
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'customer' => \App\Http\Middleware\CustomerMiddleware::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'guest.only' => \App\Http\Middleware\GuestOnlyMiddleware::class,
    'api.auth' => \App\Http\Middleware\ApiAuthMiddleware::class,
    'maintenance' => \App\Http\Middleware\MaintenanceMiddleware::class,
]);
```

## Usage Examples

### Route Groups
```php
// Admin routes
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::resource('/products', AdminProductController::class);
});

// Customer routes
Route::middleware(['customer'])->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard']);
    Route::post('/orders', [OrderController::class, 'store']);
});

// Multiple middleware
Route::middleware(['customer', 'role:premium'])->group(function () {
    Route::get('/premium-content', [PremiumController::class, 'index']);
});
```

### Individual Routes
```php
Route::get('/admin/settings', [SettingsController::class, 'index'])->middleware('admin');
Route::get('/profile', [ProfileController::class, 'show'])->middleware('customer');
Route::get('/api/data', [ApiController::class, 'data'])->middleware('api.auth');
```

### Controller Middleware
```php
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }
}

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('customer');
        $this->middleware('role:premium')->only(['premiumFeature']);
    }
}
```

## Customization

### Adding Custom Admin Emails
Edit the admin email arrays in each middleware:

```php
$adminEmails = [
    'admin@monoshop.com',
    'admin@admin.com',
    'superadmin@monoshop.com',
    'your-admin@example.com', // Add your email here
];
```

### Adding Role Relationships
To use database roles instead of email checking, uncomment the role relationship code in the middleware and add the relationship to your User model:

```php
// In User model
public function roles()
{
    return $this->belongsToMany(Role::class);
}
```

### Custom API Token Validation
Modify the `validateToken` method in `ApiAuthMiddleware` to implement your preferred token validation logic.

## Security Notes

1. Always use HTTPS in production when dealing with authentication
2. Regularly rotate API tokens
3. Consider implementing token expiration
4. Use strong, unique tokens for API authentication
5. Monitor failed authentication attempts
6. Keep admin email lists secure and up-to-date

## Testing Middleware

You can test middleware functionality using Laravel's built-in testing tools:

```php
// Test admin middleware
$this->actingAs($adminUser)
     ->get('/admin/dashboard')
     ->assertStatus(200);

// Test guest middleware
$this->actingAs($user)
     ->get('/login')
     ->assertRedirect('/dashboard');
```
