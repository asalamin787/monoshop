<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Offer;
use App\Models\Product;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offers = [
            [
                'title' => 'Electronics Flash Sale',
                'description' => 'Get amazing discounts on the latest electronics. Limited time offer!',
                'type' => 'percent',
                'image' => 'offers/electronics-flash-sale.jpg',
                'value' => 20.00,
                'start_date' => now(),
                'end_date' => now()->addDays(7),
                'is_active' => true,
            ],
            [
                'title' => 'Clothing Clearance',
                'description' => 'Clear out our clothing inventory with massive savings.',
                'type' => 'percent',
                'image' => 'offers/clothing-clearance.jpg',
                'value' => 30.00,
                'start_date' => now(),
                'end_date' => now()->addDays(14),
                'is_active' => true,
            ],
            [
                'title' => 'Home Decor Special',
                'description' => 'Redecorate your home with our special offer on home and garden items.',
                'type' => 'fixed',
                'image' => 'offers/home-decor-special.jpg',
                'value' => 50.00,
                'start_date' => now(),
                'end_date' => now()->addDays(21),
                'is_active' => true,
            ],
            [
                'title' => 'Sports Equipment Deal',
                'description' => 'Get fit with our sports equipment at discounted prices.',
                'type' => 'percent',
                'image' => 'offers/sports-equipment-deal.jpg',
                'value' => 15.00,
                'start_date' => now(),
                'end_date' => now()->addDays(10),
                'is_active' => true,
            ],
            [
                'title' => 'Book Lovers Paradise',
                'description' => 'Expand your library with our book collection offer.',
                'type' => 'percent',
                'image' => 'offers/book-lovers-paradise.jpg',
                'value' => 25.00,
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(30),
                'is_active' => false,
            ],
        ];

        foreach ($offers as $offerData) {
            $offer = Offer::create($offerData);
            
            // Attach some products to each offer
            $products = Product::inRandomOrder()->limit(3)->get();
            $offer->products()->attach($products->pluck('id'));
        }
    }
}
