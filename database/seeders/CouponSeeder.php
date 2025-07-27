<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'type' => 'percent',
                'value' => 10.00,
                'min_order_amount' => 50.00,
                'usage_limit' => 100,
                'used' => 0,
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
            ],
            [
                'code' => 'SAVE20',
                'type' => 'fixed',
                'value' => 20.00,
                'min_order_amount' => 100.00,
                'usage_limit' => 50,
                'used' => 0,
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonths(2),
            ],
            [
                'code' => 'SUMMER25',
                'type' => 'percent',
                'value' => 25.00,
                'min_order_amount' => 75.00,
                'usage_limit' => 200,
                'used' => 0,
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonths(4),
            ],
            [
                'code' => 'FREESHIP',
                'type' => 'fixed',
                'value' => 15.00,
                'min_order_amount' => 25.00,
                'usage_limit' => null,
                'used' => 0,
                'is_active' => true,
                'start_date' => now(),
                'end_date' => null,
            ],
            [
                'code' => 'NEWUSER50',
                'type' => 'fixed',
                'value' => 50.00,
                'min_order_amount' => 200.00,
                'usage_limit' => 20,
                'used' => 0,
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
