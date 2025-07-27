<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        // Create some standalone user addresses (not tied to orders)
        foreach ($users->take(5) as $user) {
            // Home address
            Address::create([
                'user_id' => $user->id,
                'order_id' => null,
                'type' => 'shipping',
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

            // Work address
            Address::create([
                'user_id' => $user->id,
                'order_id' => null,
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
        }
    }
}
