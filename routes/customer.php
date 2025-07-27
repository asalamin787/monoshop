<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Customer Routes
 * 
 * These routes are loaded by the RouteServiceProvider and are assigned 
 * the "web" and "customer" middleware groups.
 */

// Customer Dashboard
Route::get('/', function (Request $request) {
    $user = $request->user();
    
    $stats = [
        'total_orders' => $user->orders()->count(),
        'completed_orders' => $user->orders()->where('status', 'completed')->count(),
        'pending_orders' => $user->orders()->where('status', 'pending')->count(),
        'total_spent' => $user->orders()->where('payment_status', 'paid')->sum('total_amount'),
        'saved_addresses' => $user->addresses()->count(),
        'recent_orders' => $user->orders()->with(['items.product'])->latest()->take(5)->get(),
    ];

    return response()->json($stats);
})->name('dashboard');

// Profile Management
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', function (Request $request) {
        return $request->user()->load(['role', 'addresses']);
    })->name('show');

    Route::put('/', function (Request $request) {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()
        ]);
    })->name('update');

    Route::put('/password', function (Request $request) {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    })->name('password.update');

    Route::delete('/', function (Request $request) {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = $request->user();
        
        // Cancel all pending orders
        $user->orders()->where('status', 'pending')->update(['status' => 'cancelled']);
        
        // Delete user account
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    })->name('delete');
});

// Address Management
Route::prefix('addresses')->name('addresses.')->group(function () {
    Route::get('/', function (Request $request) {
        return $request->user()->addresses()->orderBy('is_default', 'desc')->get();
    })->name('index');

    Route::post('/', function (Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, remove default from other addresses
        if ($validated['is_default'] ?? false) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = $request->user()->addresses()->create($validated);

        return response()->json([
            'message' => 'Address created successfully',
            'address' => $address
        ], 201);
    })->name('store');

    Route::get('/{address}', function (Request $request, \App\Models\Address $address) {
        // Ensure user can only access their own addresses
        if ($address->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to address');
        }

        return $address;
    })->name('show');

    Route::put('/{address}', function (Request $request, \App\Models\Address $address) {
        // Ensure user can only update their own addresses
        if ($address->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to address');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, remove default from other addresses
        if ($validated['is_default'] ?? false) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'message' => 'Address updated successfully',
            'address' => $address->fresh()
        ]);
    })->name('update');

    Route::delete('/{address}', function (Request $request, \App\Models\Address $address) {
        // Ensure user can only delete their own addresses
        if ($address->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to address');
        }

        // Prevent deletion if this address is used in any orders
        if ($address->orders()->exists()) {
            return response()->json(['message' => 'Cannot delete address used in orders'], 422);
        }

        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    })->name('destroy');

    Route::post('/{address}/set-default', function (Request $request, \App\Models\Address $address) {
        // Ensure user can only modify their own addresses
        if ($address->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to address');
        }

        // Remove default from all other addresses
        $request->user()->addresses()->update(['is_default' => false]);
        
        // Set this address as default
        $address->update(['is_default' => true]);

        return response()->json([
            'message' => 'Default address updated successfully',
            'address' => $address->fresh()
        ]);
    })->name('set-default');
});

// Order Management
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', function (Request $request) {
        return $request->user()
            ->orders()
            ->with(['items.product', 'address', 'coupon'])
            ->latest()
            ->paginate(10);
    })->name('index');

    Route::get('/{order}', function (Request $request, \App\Models\Order $order) {
        // Ensure user can only access their own orders
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to order');
        }

        return $order->load(['items.product', 'address', 'coupon']);
    })->name('show');

    Route::post('/{order}/cancel', function (Request $request, \App\Models\Order $order) {
        // Ensure user can only cancel their own orders
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to order');
        }

        // Only allow cancellation of pending/processing orders
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json(['message' => 'Order cannot be cancelled'], 422);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason,
        ]);

        // Restore product stock
        foreach ($order->items as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }

        return response()->json([
            'message' => 'Order cancelled successfully',
            'order' => $order->fresh()
        ]);
    })->name('cancel');

    Route::post('/{order}/reorder', function (Request $request, \App\Models\Order $order) {
        // Ensure user can only reorder their own orders
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to order');
        }

        // Check if all products are still available
        foreach ($order->items as $item) {
            if (!$item->product->is_active || $item->product->stock_quantity < $item->quantity) {
                return response()->json([
                    'message' => "Product '{$item->product->name}' is no longer available in the required quantity"
                ], 422);
            }
        }

        // Create new order
        $newOrder = \App\Models\Order::create([
            'user_id' => $request->user()->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $order->payment_method,
            'subtotal' => $order->subtotal,
            'tax_amount' => $order->tax_amount,
            'shipping_amount' => $order->shipping_amount,
            'discount_amount' => 0, // Reset discount
            'total_amount' => $order->subtotal + $order->tax_amount + $order->shipping_amount,
            'currency' => $order->currency,
            'address_id' => $order->address_id,
            'notes' => 'Reorder from order #' . $order->order_number,
        ]);

        // Create order items
        foreach ($order->items as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $newOrder->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price, // Use current price
                'total' => $item->product->price * $item->quantity,
            ]);

            // Update product stock
            $item->product->decrement('stock_quantity', $item->quantity);
        }

        return response()->json([
            'message' => 'Order reordered successfully',
            'order' => $newOrder->load(['items.product', 'address'])
        ], 201);
    })->name('reorder');

    Route::get('/{order}/invoice', function (Request $request, \App\Models\Order $order) {
        // Ensure user can only access their own order invoices
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to order');
        }

        // Only show invoice for paid orders
        if ($order->payment_status !== 'paid') {
            return response()->json(['message' => 'Invoice not available for unpaid orders'], 422);
        }

        // Return invoice data (in a real app, you might generate a PDF)
        return response()->json([
            'invoice_number' => 'INV-' . $order->order_number,
            'order' => $order->load(['items.product', 'address', 'user']),
            'generated_at' => now(),
        ]);
    })->name('invoice');
});

// Wishlist Management
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', function (Request $request) {
        // For simplicity, using a session-based wishlist
        // In a real app, you might have a dedicated wishlist table
        $wishlistIds = session('wishlist', []);
        $products = \App\Models\Product::whereIn('id', $wishlistIds)
            ->where('is_active', true)
            ->with('category')
            ->get();

        return response()->json($products);
    })->name('index');

    Route::post('/add/{product}', function (Request $request, \App\Models\Product $product) {
        $wishlist = session('wishlist', []);
        
        if (!in_array($product->id, $wishlist)) {
            $wishlist[] = $product->id;
            session(['wishlist' => $wishlist]);
        }

        return response()->json([
            'message' => 'Product added to wishlist',
            'wishlist_count' => count($wishlist)
        ]);
    })->name('add');

    Route::delete('/remove/{product}', function (Request $request, \App\Models\Product $product) {
        $wishlist = session('wishlist', []);
        $wishlist = array_diff($wishlist, [$product->id]);
        session(['wishlist' => array_values($wishlist)]);

        return response()->json([
            'message' => 'Product removed from wishlist',
            'wishlist_count' => count($wishlist)
        ]);
    })->name('remove');

    Route::delete('/clear', function (Request $request) {
        session()->forget('wishlist');

        return response()->json(['message' => 'Wishlist cleared']);
    })->name('clear');
});

// Order History & Tracking
Route::prefix('order-history')->name('order-history.')->group(function () {
    Route::get('/', function (Request $request) {
        $query = $request->user()->orders()->with(['items.product', 'address']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search by order number
        if ($request->has('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        return $query->latest()->paginate(10);
    })->name('index');

    Route::get('/summary', function (Request $request) {
        $user = $request->user();
        
        return response()->json([
            'total_orders' => $user->orders()->count(),
            'total_spent' => $user->orders()->where('payment_status', 'paid')->sum('total_amount'),
            'average_order_value' => $user->orders()->where('payment_status', 'paid')->avg('total_amount') ?? 0,
            'orders_by_status' => $user->orders()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'monthly_spending' => $user->orders()
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as total')
                ->where('payment_status', 'paid')
                ->whereBetween('created_at', [now()->subMonths(12), now()])
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray(),
        ]);
    })->name('summary');
});

// Notifications
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', function (Request $request) {
        // Simple notification system using order updates
        $notifications = [];
        
        $recentOrders = $request->user()
            ->orders()
            ->where('updated_at', '>', now()->subDays(7))
            ->latest('updated_at')
            ->get();

        foreach ($recentOrders as $order) {
            $notifications[] = [
                'id' => $order->id,
                'type' => 'order_update',
                'title' => 'Order Update',
                'message' => "Your order #{$order->order_number} is now {$order->status}",
                'created_at' => $order->updated_at,
                'read' => false, // In a real app, you'd track this
            ];
        }

        return response()->json($notifications);
    })->name('index');

    Route::post('/{id}/mark-read', function (Request $request, int $id) {
        // In a real app, you'd update the notification read status
        return response()->json(['message' => 'Notification marked as read']);
    })->name('mark-read');

    Route::post('/mark-all-read', function (Request $request) {
        // In a real app, you'd update all notifications for the user
        return response()->json(['message' => 'All notifications marked as read']);
    })->name('mark-all-read');
});
