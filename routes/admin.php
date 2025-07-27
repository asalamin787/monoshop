<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Admin Routes
 * 
 * These routes are loaded by the RouteServiceProvider and are assigned 
 * the "web" and "admin" middleware groups.
 */

// Admin Dashboard
Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Analytics Routes
Route::prefix('analytics')->name('analytics.')->group(function () {
    Route::get('/', function () {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_orders' => \App\Models\Order::count(),
            'total_products' => \App\Models\Product::count(),
            'total_revenue' => \App\Models\Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => \App\Models\Order::where('status', 'pending')->count(),
            'completed_orders' => \App\Models\Order::where('status', 'completed')->count(),
            'low_stock_products' => \App\Models\Product::where('stock_quantity', '<', 10)->count(),
            'active_coupons' => \App\Models\Coupon::where('is_active', true)->where('expires_at', '>', now())->count(),
        ];

        return response()->json($stats);
    })->name('dashboard');

    Route::get('/sales', function () {
        $salesData = \App\Models\Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($salesData);
    })->name('sales');

    Route::get('/products', function () {
        $productStats = \App\Models\Product::withCount(['orderItems'])
            ->orderBy('order_items_count', 'desc')
            ->take(10)
            ->get();

        return response()->json($productStats);
    })->name('products');

    Route::get('/users', function () {
        $userStats = \App\Models\User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($userStats);
    })->name('users');
});

// Order Management Routes
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', function () {
        return \App\Models\Order::with(['user', 'items.product', 'address'])
            ->latest()
            ->paginate(20);
    })->name('index');

    Route::get('/{order}', function (\App\Models\Order $order) {
        return $order->load(['user', 'items.product', 'address', 'coupon']);
    })->name('show');

    Route::put('/{order}/status', function (Request $request, \App\Models\Order $order) {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order->fresh()
        ]);
    })->name('update-status');

    Route::put('/{order}/payment-status', function (Request $request, \App\Models\Order $order) {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return response()->json([
            'message' => 'Payment status updated successfully',
            'order' => $order->fresh()
        ]);
    })->name('update-payment-status');

    Route::post('/{order}/notes', function (Request $request, \App\Models\Order $order) {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $order->update(['admin_notes' => $request->notes]);

        return response()->json([
            'message' => 'Order notes updated successfully',
            'order' => $order->fresh()
        ]);
    })->name('add-notes');
});

// Product Management Routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', function () {
        return \App\Models\Product::with(['category'])
            ->latest()
            ->paginate(20);
    })->name('index');

    Route::post('/', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_per_item' => 'nullable|numeric|min:0',
            'track_quantity' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $product = \App\Models\Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load('category')
        ], 201);
    })->name('store');

    Route::get('/{product}', function (\App\Models\Product $product) {
        return $product->load(['category']);
    })->name('show');

    Route::put('/{product}', function (Request $request, \App\Models\Product $product) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_per_item' => 'nullable|numeric|min:0',
            'track_quantity' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->fresh(['category'])
        ]);
    })->name('update');

    Route::delete('/{product}', function (\App\Models\Product $product) {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    })->name('destroy');

    Route::post('/{product}/toggle-status', function (\App\Models\Product $product) {
        $product->update(['is_active' => !$product->is_active]);

        return response()->json([
            'message' => 'Product status updated successfully',
            'product' => $product->fresh()
        ]);
    })->name('toggle-status');

    Route::post('/{product}/toggle-featured', function (\App\Models\Product $product) {
        $product->update(['is_featured' => !$product->is_featured]);

        return response()->json([
            'message' => 'Product featured status updated successfully',
            'product' => $product->fresh()
        ]);
    })->name('toggle-featured');
});

// User Management Routes
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', function () {
        return \App\Models\User::with(['role', 'addresses'])
            ->latest()
            ->paginate(20);
    })->name('index');

    Route::get('/{user}', function (\App\Models\User $user) {
        return $user->load(['role', 'addresses', 'orders']);
    })->name('show');

    Route::put('/{user}/role', function (Request $request, \App\Models\User $user) {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update(['role_id' => $request->role_id]);

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user->fresh(['role'])
        ]);
    })->name('update-role');

    Route::post('/{user}/toggle-status', function (\App\Models\User $user) {
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'message' => 'User status updated successfully',
            'user' => $user->fresh()
        ]);
    })->name('toggle-status');

    Route::delete('/{user}', function (\App\Models\User $user) {
        // Prevent deletion of admin users
        if ($user->role && $user->role->name === 'admin') {
            return response()->json(['message' => 'Cannot delete admin users'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    })->name('destroy');
});

// Category Management Routes
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', function () {
        return \App\Models\Category::withCount('products')
            ->latest()
            ->paginate(20);
    })->name('index');

    Route::post('/', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $category = \App\Models\Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    })->name('store');

    Route::put('/{category}', function (Request $request, \App\Models\Category $category) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category->fresh()
        ]);
    })->name('update');

    Route::delete('/{category}', function (\App\Models\Category $category) {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json(['message' => 'Cannot delete category with products'], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    })->name('destroy');
});

// Coupon Management Routes
Route::prefix('coupons')->name('coupons.')->group(function () {
    Route::get('/', function () {
        return \App\Models\Coupon::latest()->paginate(20);
    })->name('index');

    Route::post('/', function (Request $request) {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'used_count' => 'integer|min:0',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'boolean',
        ]);

        $coupon = \App\Models\Coupon::create($validated);

        return response()->json([
            'message' => 'Coupon created successfully',
            'coupon' => $coupon
        ], 201);
    })->name('store');

    Route::put('/{coupon}', function (Request $request, \App\Models\Coupon $coupon) {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'used_count' => 'integer|min:0',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $coupon->update($validated);

        return response()->json([
            'message' => 'Coupon updated successfully',
            'coupon' => $coupon->fresh()
        ]);
    })->name('update');

    Route::post('/{coupon}/toggle-status', function (\App\Models\Coupon $coupon) {
        $coupon->update(['is_active' => !$coupon->is_active]);

        return response()->json([
            'message' => 'Coupon status updated successfully',
            'coupon' => $coupon->fresh()
        ]);
    })->name('toggle-status');

    Route::delete('/{coupon}', function (\App\Models\Coupon $coupon) {
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted successfully']);
    })->name('destroy');
});

// Settings Management Routes
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', function () {
        return \App\Models\Setting::all();
    })->name('index');

    Route::put('/', function (Request $request) {
        $settings = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        foreach ($settings['settings'] as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        return response()->json(['message' => 'Settings updated successfully']);
    })->name('update');

    Route::get('/{key}', function (string $key) {
        $setting = \App\Models\Setting::where('key', $key)->first();
        
        if (!$setting) {
            return response()->json(['message' => 'Setting not found'], 404);
        }

        return response()->json($setting);
    })->name('show');

    Route::put('/{key}', function (Request $request, string $key) {
        $request->validate([
            'value' => 'required|string',
        ]);

        $setting = \App\Models\Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $request->value]
        );

        return response()->json([
            'message' => 'Setting updated successfully',
            'setting' => $setting
        ]);
    })->name('update-single');
});

// Backup and Maintenance Routes
Route::prefix('system')->name('system.')->group(function () {
    Route::post('/cache/clear', function () {
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return response()->json(['message' => 'Cache cleared successfully']);
    })->name('cache.clear');

    Route::post('/maintenance/enable', function () {
        \Illuminate\Support\Facades\Artisan::call('down');
        return response()->json(['message' => 'Maintenance mode enabled']);
    })->name('maintenance.enable');

    Route::post('/maintenance/disable', function () {
        \Illuminate\Support\Facades\Artisan::call('up');
        return response()->json(['message' => 'Maintenance mode disabled']);
    })->name('maintenance.disable');

    Route::get('/info', function () {
        return response()->json([
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'database' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
        ]);
    })->name('info');
});
