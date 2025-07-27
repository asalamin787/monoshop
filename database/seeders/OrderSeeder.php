<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\User;
use App\Models\Product;
use App\Models\Coupon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        $coupons = Coupon::all();

        for ($i = 1; $i <= 15; $i++) {
            $user = $users->random();
            $coupon = $i % 3 == 0 ? $coupons->random() : null;
            
            $subtotal = fake()->randomFloat(2, 50, 500);
            $discount = $coupon ? ($coupon->type === 'percent' ? $subtotal * ($coupon->value / 100) : $coupon->value) : 0;
            $shippingCost = $subtotal >= 75 ? 0 : 9.99;
            $total = $subtotal - $discount + $shippingCost;

            $order = Order::create([
                'user_id' => $user->id,
                'coupon_id' => $coupon?->id,
                'order_number' => 'ORD-' . time() . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'stripe', 'bank_transfer']),
                'payment_status' => fake()->randomElement(['paid', 'unpaid', 'refunded']),
                'notes' => fake()->optional()->sentence(),
            ]);

            // Create 1-5 order items for each order
            $itemCount = fake()->numberBetween(1, 5);
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = fake()->numberBetween(1, 3);
                $price = $product->sale_price ?: $product->price;
                $itemTotal = $price * $quantity;

                $productColors = is_array($product->colors) ? $product->colors : json_decode($product->colors ?: '[]', true);
                $productSizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes ?: '[]', true);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $itemTotal,
                    'selected_variants' => json_encode([
                        'color' => fake()->optional()->randomElement($productColors ?: ['Default']),
                        'size' => fake()->optional()->randomElement($productSizes ?: ['Default']),
                    ]),
                ]);
            }

            // Create billing address
            Address::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'billing',
                'full_name' => $user->name,
                'email' => $user->email,
                'phone' => fake()->phoneNumber(),
                'address_line_1' => fake()->streetAddress(),
                'address_line_2' => fake()->optional()->secondaryAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country' => 'United States',
            ]);

            // Create shipping address (sometimes same as billing)
            if (fake()->boolean(70)) {
                Address::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'type' => 'shipping',
                    'full_name' => fake()->boolean(80) ? $user->name : fake()->name(),
                    'email' => fake()->boolean(80) ? $user->email : fake()->email(),
                    'phone' => fake()->phoneNumber(),
                    'address_line_1' => fake()->streetAddress(),
                    'address_line_2' => fake()->optional()->secondaryAddress(),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'postal_code' => fake()->postcode(),
                    'country' => 'United States',
                ]);
            }
        }
    }
}
