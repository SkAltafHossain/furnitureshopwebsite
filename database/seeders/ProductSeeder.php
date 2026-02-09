<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::pluck('id')->toArray();

        $products = [
            // Electronics
            [
                'category_id' => 1,
                'name' => 'Smartphone Pro Max',
                'slug' => Str::slug('Smartphone Pro Max'),
                'description' => 'Latest flagship smartphone with advanced camera system, 5G connectivity, and all-day battery life. Features include 6.7-inch display, 256GB storage, and premium build quality.',
                'price' => 999.99,
                'discount_price' => 899.99,
                'stock' => 50,
                'image' => 'products/smartphone.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 1,
                'name' => 'Laptop Ultra Book',
                'slug' => Str::slug('Laptop Ultra Book'),
                'description' => 'Powerful ultrabook with Intel Core i7 processor, 16GB RAM, 512GB SSD, and 14-inch 4K display. Perfect for professionals and content creators.',
                'price' => 1499.99,
                'discount_price' => 1299.99,
                'stock' => 30,
                'image' => 'products/laptop.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 1,
                'name' => 'Wireless Headphones',
                'slug' => Str::slug('Wireless Headphones'),
                'description' => 'Premium noise-cancelling wireless headphones with 30-hour battery life, superior sound quality, and comfortable fit for all-day wear.',
                'price' => 299.99,
                'stock' => 75,
                'image' => 'products/headphones.jpg',
                'status' => 'active',
            ],

            // Clothing & Fashion
            [
                'category_id' => 2,
                'name' => 'Designer T-Shirt',
                'slug' => Str::slug('Designer T-Shirt'),
                'description' => 'Premium cotton t-shirt with modern design, comfortable fit, and durable fabric. Available in multiple colors and sizes.',
                'price' => 39.99,
                'discount_price' => 29.99,
                'stock' => 100,
                'image' => 'products/tshirt.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 2,
                'name' => 'Classic Denim Jeans',
                'slug' => Str::slug('Classic Denim Jeans'),
                'description' => 'Timeless denim jeans with perfect fit, premium quality denim, and classic styling. Suitable for all occasions.',
                'price' => 79.99,
                'stock' => 80,
                'image' => 'products/jeans.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 2,
                'name' => 'Leather Jacket',
                'slug' => Str::slug('Leather Jacket'),
                'description' => 'Genuine leather jacket with modern styling, excellent craftsmanship, and superior comfort. A wardrobe essential.',
                'price' => 299.99,
                'discount_price' => 249.99,
                'stock' => 25,
                'image' => 'products/jacket.jpg',
                'status' => 'active',
            ],

            // Home & Kitchen
            [
                'category_id' => 3,
                'name' => 'Coffee Maker Deluxe',
                'slug' => Str::slug('Coffee Maker Deluxe'),
                'description' => 'Programmable coffee maker with thermal carafe, multiple brewing options, and easy-to-use interface. Makes perfect coffee every time.',
                'price' => 129.99,
                'stock' => 40,
                'image' => 'products/coffee-maker.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 3,
                'name' => 'Air Fryer Pro',
                'slug' => Str::slug('Air Fryer Pro'),
                'description' => 'Healthy cooking air fryer with 5.8qt capacity, digital controls, and rapid air technology. Perfect for guilt-free frying.',
                'price' => 89.99,
                'discount_price' => 69.99,
                'stock' => 60,
                'image' => 'products/air-fryer.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 3,
                'name' => 'Blender Supreme',
                'slug' => Str::slug('Blender Supreme'),
                'description' => 'High-powered blender with 1500W motor, multiple speed settings, and durable stainless steel blades. Ideal for smoothies and more.',
                'price' => 149.99,
                'stock' => 35,
                'image' => 'products/blender.jpg',
                'status' => 'active',
            ],

            // Sports & Outdoors
            [
                'category_id' => 4,
                'name' => 'Yoga Mat Premium',
                'slug' => Str::slug('Yoga Mat Premium'),
                'description' => 'Extra thick yoga mat with superior grip, cushioning, and durability. Non-slip surface for safe practice.',
                'price' => 49.99,
                'stock' => 90,
                'image' => 'products/yoga-mat.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 4,
                'name' => 'Running Shoes Pro',
                'slug' => Str::slug('Running Shoes Pro'),
                'description' => 'Professional running shoes with advanced cushioning, breathable mesh, and superior traction. Designed for serious runners.',
                'price' => 129.99,
                'discount_price' => 99.99,
                'stock' => 55,
                'image' => 'products/running-shoes.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 4,
                'name' => 'Camping Tent Family',
                'slug' => Str::slug('Camping Tent Family'),
                'description' => 'Spacious 4-person camping tent with weather protection, easy setup, and durable materials. Perfect for family adventures.',
                'price' => 199.99,
                'stock' => 20,
                'image' => 'products/tent.jpg',
                'status' => 'active',
            ],

            // Books & Media
            [
                'category_id' => 5,
                'name' => 'Bestseller Novel Collection',
                'slug' => Str::slug('Bestseller Novel Collection'),
                'description' => 'Collection of award-winning novels from various genres. Perfect for book lovers and casual readers alike.',
                'price' => 29.99,
                'discount_price' => 19.99,
                'stock' => 200,
                'image' => 'products/books.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 5,
                'name' => 'Educational DVD Set',
                'slug' => Str::slug('Educational DVD Set'),
                'description' => 'Comprehensive educational DVD set covering various subjects. Great for students and lifelong learners.',
                'price' => 39.99,
                'stock' => 45,
                'image' => 'products/dvd-set.jpg',
                'status' => 'active',
            ],

            // Toys & Games
            [
                'category_id' => 6,
                'name' => 'Building Blocks Set',
                'slug' => Str::slug('Building Blocks Set'),
                'description' => 'Creative building blocks set with 500+ pieces, compatible with major brands. Promotes creativity and STEM learning.',
                'price' => 49.99,
                'stock' => 70,
                'image' => 'products/blocks.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 6,
                'name' => 'Board Game Collection',
                'slug' => Str::slug('Board Game Collection'),
                'description' => 'Collection of classic board games for family fun. Includes chess, checkers, backgammon, and more.',
                'price' => 34.99,
                'discount_price' => 24.99,
                'stock' => 85,
                'image' => 'products/board-games.jpg',
                'status' => 'active',
            ],

            // Beauty & Personal Care
            [
                'category_id' => 7,
                'name' => 'Skincare Set Premium',
                'slug' => Str::slug('Skincare Set Premium'),
                'description' => 'Complete skincare set with cleanser, toner, moisturizer, and serum. Made with natural ingredients for all skin types.',
                'price' => 89.99,
                'discount_price' => 69.99,
                'stock' => 65,
                'image' => 'products/skincare.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 7,
                'name' => 'Hair Care Kit',
                'slug' => Str::slug('Hair Care Kit'),
                'description' => 'Professional hair care kit with shampoo, conditioner, and styling products. Suitable for all hair types.',
                'price' => 59.99,
                'stock' => 75,
                'image' => 'products/hair-care.jpg',
                'status' => 'active',
            ],

            // Automotive
            [
                'category_id' => 8,
                'name' => 'Car Phone Mount',
                'slug' => Str::slug('Car Phone Mount'),
                'description' => 'Universal car phone mount with 360-degree rotation, strong suction cup, and secure grip. Compatible with all smartphones.',
                'price' => 24.99,
                'stock' => 150,
                'image' => 'products/phone-mount.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 8,
                'name' => 'Car Vacuum Cleaner',
                'slug' => Str::slug('Car Vacuum Cleaner'),
                'description' => 'Portable car vacuum cleaner with strong suction, multiple attachments, and rechargeable battery. Keep your car clean.',
                'price' => 49.99,
                'discount_price' => 39.99,
                'stock' => 95,
                'image' => 'products/car-vacuum.jpg',
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
