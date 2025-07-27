<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Public API Routes
 * 
 * These routes are loaded by the RouteServiceProvider and are assigned 
 * the "api" middleware group. These routes do NOT require authentication.
 */

// Public Product Catalog
Route::get('/products', function (Request $request) {
    $query = \App\Models\Product::with(['category'])
        ->where('is_active', true)
        ->where('stock_quantity', '>', 0);

    // Search functionality
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%");
        });
    }

    // Category filter
    if ($request->has('category')) {
        if (is_numeric($request->category)) {
            $query->where('category_id', $request->category);
        } else {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
    }

    // Price range filter
    if ($request->has('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }
    if ($request->has('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    // Featured products filter
    if ($request->has('featured') && $request->featured === 'true') {
        $query->where('is_featured', true);
    }

    // Sort options
    $sortBy = $request->get('sort', 'created_at');
    $sortDirection = $request->get('direction', 'desc');

    switch ($sortBy) {
        case 'name':
            $query->orderBy('name', $sortDirection);
            break;
        case 'price':
            $query->orderBy('price', $sortDirection);
            break;
        case 'popularity':
            $query->withCount('orderItems')->orderBy('order_items_count', $sortDirection);
            break;
        default:
            $query->orderBy('created_at', $sortDirection);
    }

    return $query->paginate(20);
})->middleware('throttle:public-api');

// Single Product Details
Route::get('/products/{product}', function (\App\Models\Product $product) {
    if (!$product->is_active) {
        abort(404, 'Product not found');
    }

    return $product->load(['category']);
})->middleware('throttle:public-api');

// Product Categories
Route::get('/categories', function () {
    return \App\Models\Category::where('is_active', true)
        ->withCount(['products' => function ($query) {
            $query->where('is_active', true)->where('stock_quantity', '>', 0);
        }])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();
})->middleware('throttle:public-api');

// Category Products
Route::get('/categories/{category}/products', function (\App\Models\Category $category, Request $request) {
    if (!$category->is_active) {
        abort(404, 'Category not found');
    }

    $query = $category->products()
        ->where('is_active', true)
        ->where('stock_quantity', '>', 0);

    // Apply same filters as products endpoint
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    if ($request->has('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }
    if ($request->has('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    // Sort options
    $sortBy = $request->get('sort', 'created_at');
    $sortDirection = $request->get('direction', 'desc');

    switch ($sortBy) {
        case 'name':
            $query->orderBy('name', $sortDirection);
            break;
        case 'price':
            $query->orderBy('price', $sortDirection);
            break;
        default:
            $query->orderBy('created_at', $sortDirection);
    }

    return $query->paginate(20);
})->middleware('throttle:public-api');

// Featured Products
Route::get('/featured-products', function () {
    return \App\Models\Product::with(['category'])
        ->where('is_active', true)
        ->where('is_featured', true)
        ->where('stock_quantity', '>', 0)
        ->orderBy('created_at', 'desc')
        ->take(12)
        ->get();
})->middleware('throttle:public-api');

// Latest Products
Route::get('/latest-products', function (Request $request) {
    $limit = min($request->get('limit', 12), 50); // Max 50 products

    return \App\Models\Product::with(['category'])
        ->where('is_active', true)
        ->where('stock_quantity', '>', 0)
        ->orderBy('created_at', 'desc')
        ->take($limit)
        ->get();
})->middleware('throttle:public-api');

// Popular Products
Route::get('/popular-products', function (Request $request) {
    $limit = min($request->get('limit', 12), 50); // Max 50 products

    return \App\Models\Product::with(['category'])
        ->withCount('orderItems')
        ->where('is_active', true)
        ->where('stock_quantity', '>', 0)
        ->orderBy('order_items_count', 'desc')
        ->take($limit)
        ->get();
})->middleware('throttle:public-api');

// Product Search Suggestions
Route::get('/search/suggestions', function (Request $request) {
    if (!$request->has('q') || strlen($request->q) < 2) {
        return response()->json([]);
    }

    $query = $request->q;
    
    $suggestions = \App\Models\Product::where('is_active', true)
        ->where('stock_quantity', '>', 0)
        ->where('name', 'like', "%{$query}%")
        ->select('id', 'name', 'slug', 'price')
        ->orderBy('name')
        ->take(10)
        ->get();

    return $suggestions;
})->middleware('throttle:public-api');

// Active Offers
Route::get('/offers', function () {
    return \App\Models\Offer::where('is_active', true)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->with(['products' => function ($query) {
            $query->where('is_active', true)->where('stock_quantity', '>', 0);
        }])
        ->orderBy('created_at', 'desc')
        ->get();
})->middleware('throttle:public-api');

// Active Sliders
Route::get('/sliders', function () {
    return \App\Models\Slider::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('created_at', 'desc')
        ->get();
})->middleware('throttle:public-api');

// Store Settings (Public)
Route::get('/settings', function () {
    $publicSettings = [
        'store_name',
        'store_description',
        'contact_email',
        'contact_phone',
        'store_address',
        'business_hours',
        'shipping_info',
        'return_policy',
        'privacy_policy',
        'terms_of_service',
        'social_links',
        'currency',
        'tax_rate',
        'free_shipping_threshold',
    ];

    $settings = \App\Models\Setting::whereIn('key', $publicSettings)
        ->pluck('value', 'key');

    return response()->json($settings);
})->middleware('throttle:public-api');

// Store Statistics (Public)
Route::get('/stats', function () {
    return response()->json([
        'total_products' => \App\Models\Product::where('is_active', true)->count(),
        'total_categories' => \App\Models\Category::where('is_active', true)->count(),
        'active_offers' => \App\Models\Offer::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count(),
        'featured_products' => \App\Models\Product::where('is_active', true)
            ->where('is_featured', true)
            ->count(),
    ]);
})->middleware('throttle:public-api');

// Contact Form Submission
Route::post('/contact', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:2000',
        'phone' => 'nullable|string|max:20',
    ]);

    // In a real application, you would:
    // 1. Store the message in a database
    // 2. Send an email notification to admin
    // 3. Maybe send an auto-reply to the customer

    // For now, just return success
    return response()->json([
        'message' => 'Thank you for your message. We will get back to you soon!',
        'contact_id' => 'CONTACT-' . strtoupper(uniqid()),
    ], 201);
})->middleware('throttle:public-api');

// Newsletter Subscription
Route::post('/newsletter/subscribe', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required|email|max:255',
        'name' => 'nullable|string|max:255',
    ]);

    // Check if already subscribed (using settings table for simplicity)
    $existingSubscription = \App\Models\Setting::where('key', 'newsletter_subscriber_' . md5($validated['email']))->first();
    
    if ($existingSubscription) {
        return response()->json([
            'message' => 'You are already subscribed to our newsletter.',
        ], 409);
    }

    // Store subscription
    \App\Models\Setting::create([
        'key' => 'newsletter_subscriber_' . md5($validated['email']),
        'value' => json_encode([
            'email' => $validated['email'],
            'name' => $validated['name'] ?? null,
            'subscribed_at' => now(),
            'ip_address' => $request->ip(),
        ]),
    ]);

    return response()->json([
        'message' => 'Thank you for subscribing to our newsletter!',
    ], 201);
})->middleware('throttle:public-api');

// Newsletter Unsubscription
Route::post('/newsletter/unsubscribe', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required|email|max:255',
    ]);

    $subscription = \App\Models\Setting::where('key', 'newsletter_subscriber_' . md5($validated['email']))->first();
    
    if (!$subscription) {
        return response()->json([
            'message' => 'Email address not found in our newsletter list.',
        ], 404);
    }

    $subscription->delete();

    return response()->json([
        'message' => 'You have been successfully unsubscribed from our newsletter.',
    ]);
})->middleware('throttle:public-api');

// Check Product Availability
Route::get('/products/{product}/availability', function (\App\Models\Product $product) {
    if (!$product->is_active) {
        return response()->json([
            'available' => false,
            'message' => 'Product is not available',
        ]);
    }

    return response()->json([
        'available' => $product->stock_quantity > 0,
        'stock_quantity' => $product->track_quantity ? $product->stock_quantity : null,
        'message' => $product->stock_quantity > 0 ? 'In stock' : 'Out of stock',
    ]);
})->middleware('throttle:public-api');

// Price Check for Multiple Products
Route::post('/products/price-check', function (Request $request) {
    $validated = $request->validate([
        'product_ids' => 'required|array|max:50',
        'product_ids.*' => 'required|integer|exists:products,id',
    ]);

    $products = \App\Models\Product::whereIn('id', $validated['product_ids'])
        ->where('is_active', true)
        ->select('id', 'name', 'price', 'compare_price', 'stock_quantity', 'is_active')
        ->get();

    return response()->json([
        'products' => $products,
        'total_products' => $products->count(),
        'updated_at' => now(),
    ]);
})->middleware('throttle:public-api');
