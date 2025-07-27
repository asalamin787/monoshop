<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Electronics
            [
                'category_id' => 1,
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'description' => 'Latest iPhone with advanced camera system and titanium design.',
                'price' => 999.99,
                'sale_price' => 899.99,
                'sku' => 'IP15P-001',
                'quantity' => 50,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'image' => 'products/iphone-15-pro.jpg',
                'images' => json_encode(['products/iphone-15-pro-1.jpg', 'products/iphone-15-pro-2.jpg']),
                'sizes' => json_encode(['128GB', '256GB', '512GB', '1TB']),
                'colors' => json_encode(['Natural Titanium', 'Blue Titanium', 'White Titanium', 'Black Titanium']),
                'material' => 'Titanium',
                'brand' => 'Apple',
            ],
            [
                'category_id' => 1,
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'description' => 'Premium Android smartphone with S Pen and incredible camera.',
                'price' => 1199.99,
                'sale_price' => null,
                'sku' => 'SGS24U-001',
                'quantity' => 30,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'image' => 'products/samsung-s24-ultra.jpg',
                'images' => json_encode(['products/samsung-s24-ultra-1.jpg', 'products/samsung-s24-ultra-2.jpg']),
                'sizes' => json_encode(['256GB', '512GB', '1TB']),
                'colors' => json_encode(['Titanium Gray', 'Titanium Black', 'Titanium Violet', 'Titanium Yellow']),
                'material' => 'Glass/Aluminum',
                'brand' => 'Samsung',
            ],
            
            // Clothing
            [
                'category_id' => 2,
                'name' => 'Nike Air Max 270',
                'slug' => 'nike-air-max-270',
                'description' => 'Comfortable running shoes with Max Air cushioning.',
                'price' => 150.00,
                'sale_price' => 129.99,
                'sku' => 'NAM270-001',
                'quantity' => 100,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'products/nike-air-max-270.jpg',
                'images' => json_encode(['products/nike-air-max-270-1.jpg', 'products/nike-air-max-270-2.jpg']),
                'sizes' => json_encode(['7', '8', '9', '10', '11', '12']),
                'colors' => json_encode(['Black/White', 'Blue/White', 'Red/Black']),
                'material' => 'Synthetic/Mesh',
                'brand' => 'Nike',
            ],
            [
                'category_id' => 2,
                'name' => 'Levi\'s 501 Original Jeans',
                'slug' => 'levis-501-original-jeans',
                'description' => 'Classic straight-leg jeans in original fit.',
                'price' => 89.99,
                'sale_price' => null,
                'sku' => 'L501-001',
                'quantity' => 75,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'products/levis-501.jpg',
                'images' => json_encode(['products/levis-501-1.jpg', 'products/levis-501-2.jpg']),
                'sizes' => json_encode(['28', '30', '32', '34', '36', '38']),
                'colors' => json_encode(['Dark Blue', 'Light Blue', 'Black']),
                'material' => '100% Cotton',
                'brand' => 'Levi\'s',
            ],

            // Home & Garden
            [
                'category_id' => 3,
                'name' => 'KitchenAid Stand Mixer',
                'slug' => 'kitchenaid-stand-mixer',
                'description' => 'Professional 5-quart stand mixer for all your baking needs.',
                'price' => 379.99,
                'sale_price' => 299.99,
                'sku' => 'KA-SM-001',
                'quantity' => 25,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
                'image' => 'products/kitchenaid-mixer.jpg',
                'images' => json_encode(['products/kitchenaid-mixer-1.jpg', 'products/kitchenaid-mixer-2.jpg']),
                'sizes' => json_encode(['5-Quart']),
                'colors' => json_encode(['Empire Red', 'Onyx Black', 'White', 'Silver']),
                'material' => 'Metal',
                'brand' => 'KitchenAid',
            ],

            // Sports & Outdoors
            [
                'category_id' => 4,
                'name' => 'Wilson Tennis Racket Pro Staff',
                'slug' => 'wilson-tennis-racket-pro-staff',
                'description' => 'Professional tennis racket used by champions.',
                'price' => 249.99,
                'sale_price' => null,
                'sku' => 'WTR-PS-001',
                'quantity' => 40,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'products/wilson-racket.jpg',
                'images' => json_encode(['products/wilson-racket-1.jpg', 'products/wilson-racket-2.jpg']),
                'sizes' => json_encode(['4 1/8', '4 1/4', '4 3/8', '4 1/2']),
                'colors' => json_encode(['Black/White']),
                'material' => 'Carbon Fiber',
                'brand' => 'Wilson',
            ],

            // Books
            [
                'category_id' => 5,
                'name' => 'The Complete Works of Shakespeare',
                'slug' => 'complete-works-shakespeare',
                'description' => 'Complete collection of Shakespeare\'s plays and sonnets.',
                'price' => 29.99,
                'sale_price' => 24.99,
                'sku' => 'BOOK-SHAK-001',
                'quantity' => 60,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'products/shakespeare-complete.jpg',
                'images' => json_encode(['products/shakespeare-complete-1.jpg']),
                'sizes' => json_encode(['Hardcover', 'Paperback']),
                'colors' => json_encode(['Default']),
                'material' => 'Paper',
                'brand' => 'Penguin Classics',
            ],

            // Beauty & Health
            [
                'category_id' => 6,
                'name' => 'Neutrogena Ultra Sheer Sunscreen',
                'slug' => 'neutrogena-ultra-sheer-sunscreen',
                'description' => 'Broad spectrum SPF 100+ sunscreen for daily protection.',
                'price' => 12.99,
                'sale_price' => null,
                'sku' => 'NEUT-SUN-001',
                'quantity' => 200,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
                'image' => 'products/neutrogena-sunscreen.jpg',
                'images' => json_encode(['products/neutrogena-sunscreen-1.jpg']),
                'sizes' => json_encode(['3 fl oz', '5 fl oz']),
                'colors' => json_encode(['Default']),
                'material' => 'Cream',
                'brand' => 'Neutrogena',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
