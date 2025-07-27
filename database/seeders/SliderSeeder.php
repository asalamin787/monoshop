<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Slider;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Summer Sale 2025',
                'subtitle' => 'Up to 50% off on selected items',
                'image' => 'sliders/summer-sale-2025.jpg',
                'button_text' => 'Shop Now',
                'button_link' => '/categories/clothing',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'New Electronics Collection',
                'subtitle' => 'Latest gadgets and devices',
                'image' => 'sliders/electronics-collection.jpg',
                'button_text' => 'Explore',
                'button_link' => '/categories/electronics',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Home & Garden Essentials',
                'subtitle' => 'Transform your living space',
                'image' => 'sliders/home-garden-essentials.jpg',
                'button_text' => 'Discover',
                'button_link' => '/categories/home-garden',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Sports & Fitness Gear',
                'subtitle' => 'Get ready for your next adventure',
                'image' => 'sliders/sports-fitness.jpg',
                'button_text' => 'View Collection',
                'button_link' => '/categories/sports-outdoors',
                'order' => 4,
                'is_active' => false,
            ],
            [
                'title' => 'Beauty & Wellness',
                'subtitle' => 'Take care of yourself',
                'image' => 'sliders/beauty-wellness.jpg',
                'button_text' => 'Shop Beauty',
                'button_link' => '/categories/beauty-health',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }
}
