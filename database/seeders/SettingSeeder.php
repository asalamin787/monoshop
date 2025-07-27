<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'MonoShop',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'site_description',
                'value' => 'Your one-stop shop for everything you need',
                'type' => 'textarea',
                'group' => 'general',
            ],
            [
                'key' => 'site_logo',
                'value' => 'logos/monoshop-logo.png',
                'type' => 'image',
                'group' => 'general',
            ],
            [
                'key' => 'site_favicon',
                'value' => 'logos/favicon.ico',
                'type' => 'image',
                'group' => 'general',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'general',
            ],

            // Contact Settings
            [
                'key' => 'contact_email',
                'value' => 'contact@monoshop.com',
                'type' => 'email',
                'group' => 'contact',
            ],
            [
                'key' => 'contact_phone',
                'value' => '+1 (555) 123-4567',
                'type' => 'text',
                'group' => 'contact',
            ],
            [
                'key' => 'contact_address',
                'value' => '123 Commerce Street, Business District, NY 10001',
                'type' => 'textarea',
                'group' => 'contact',
            ],
            [
                'key' => 'support_email',
                'value' => 'support@monoshop.com',
                'type' => 'email',
                'group' => 'contact',
            ],

            // Social Media Settings
            [
                'key' => 'facebook_url',
                'value' => 'https://facebook.com/monoshop',
                'type' => 'url',
                'group' => 'social',
            ],
            [
                'key' => 'twitter_url',
                'value' => 'https://twitter.com/monoshop',
                'type' => 'url',
                'group' => 'social',
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://instagram.com/monoshop',
                'type' => 'url',
                'group' => 'social',
            ],
            [
                'key' => 'linkedin_url',
                'value' => 'https://linkedin.com/company/monoshop',
                'type' => 'url',
                'group' => 'social',
            ],

            // E-commerce Settings
            [
                'key' => 'currency',
                'value' => 'USD',
                'type' => 'text',
                'group' => 'ecommerce',
            ],
            [
                'key' => 'currency_symbol',
                'value' => '$',
                'type' => 'text',
                'group' => 'ecommerce',
            ],
            [
                'key' => 'tax_rate',
                'value' => '8.25',
                'type' => 'number',
                'group' => 'ecommerce',
            ],
            [
                'key' => 'shipping_cost',
                'value' => '9.99',
                'type' => 'number',
                'group' => 'ecommerce',
            ],
            [
                'key' => 'free_shipping_threshold',
                'value' => '75.00',
                'type' => 'number',
                'group' => 'ecommerce',
            ],

            // Email Settings
            [
                'key' => 'smtp_host',
                'value' => 'smtp.gmail.com',
                'type' => 'text',
                'group' => 'email',
            ],
            [
                'key' => 'smtp_port',
                'value' => '587',
                'type' => 'number',
                'group' => 'email',
            ],
            [
                'key' => 'smtp_username',
                'value' => 'noreply@monoshop.com',
                'type' => 'email',
                'group' => 'email',
            ],
            [
                'key' => 'smtp_encryption',
                'value' => 'tls',
                'type' => 'text',
                'group' => 'email',
            ],

            // SEO Settings
            [
                'key' => 'meta_title',
                'value' => 'MonoShop - Your Ultimate Shopping Destination',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'meta_description',
                'value' => 'Discover amazing products at unbeatable prices. Shop electronics, clothing, home goods and more at MonoShop.',
                'type' => 'textarea',
                'group' => 'seo',
            ],
            [
                'key' => 'meta_keywords',
                'value' => 'online shopping, ecommerce, electronics, clothing, home goods, deals',
                'type' => 'textarea',
                'group' => 'seo',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
