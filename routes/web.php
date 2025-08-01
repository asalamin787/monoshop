<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('pages.home');
// });

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Guest-only routes (redirects authenticated users)
Route::middleware(['guest.only'])->group(function () {
    Route::get('/',[PageController::class, 'home'])->name('home');
    Route::get('/shop',[PageController::class, 'shop'])->name('shop');
    Route::get('/product-detail',[PageController::class, 'product_detail'])->name('product_detail');
    Route::get('/about-us',[PageController::class, 'about_us'])->name('about_us');
    Route::get('/404',[PageController::class, 'notFound'])->name('404');
    Route::get('/faq',[PageController::class, 'faq'])->name('faq');
    Route::get('/track-order',[PageController::class, 'track_order'])->name('track_order');
    Route::get('/account-page',[PageController::class, 'account_page'])->name('account_page');






    
});

// Customer-only routes (requires authentication)
Route::middleware(['customer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
    
    Route::get('/orders', function () {
        return view('orders.index');
    })->name('orders.index');
    
    Route::get('/cart', function () {
        return view('cart');
    })->name('cart');
});

// Admin-only routes
Route::middleware(['admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/admin/users', function () {
        return view('admin.users');
    })->name('admin.users');
    
    Route::get('/admin/settings', function () {
        return view('admin.settings');
    })->name('admin.settings');
});

// Role-based routes (multiple roles allowed)
Route::middleware(['role:admin,manager'])->group(function () {
    Route::get('/management', function () {
        return view('management.index');
    })->name('management.index');
    
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');
});

// API routes with custom auth
Route::prefix('api')->middleware(['api.auth'])->group(function () {
    Route::get('/products', function () {
        return response()->json(['products' => []]);
    });
    
    Route::get('/orders', function () {
        return response()->json(['orders' => []]);
    });
});

// Public routes (no middleware needed)
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Test routes for middleware functionality
Route::prefix('test')->group(function () {
    Route::get('/admin', [App\Http\Controllers\TestController::class, 'adminTest'])->middleware('admin');
    Route::get('/customer', [App\Http\Controllers\TestController::class, 'customerTest'])->middleware('customer');
    Route::get('/role', [App\Http\Controllers\TestController::class, 'roleTest'])->middleware('role:admin,manager');
    Route::get('/api', [App\Http\Controllers\TestController::class, 'apiTest'])->middleware('api.auth');
    Route::get('/guest', [App\Http\Controllers\TestController::class, 'guestTest'])->middleware('guest.only');
});
