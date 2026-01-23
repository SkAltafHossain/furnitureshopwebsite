<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleWebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample categories
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'image' => null, 'status' => 'active'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'image' => null, 'status' => 'active'],
            ['name' => 'Books', 'slug' => 'books', 'image' => null, 'status' => 'active'],
            ['name' => 'Home & Kitchen', 'slug' => 'home-kitchen', 'image' => null, 'status' => 'active'],
            ['name' => 'Sports', 'slug' => 'sports', 'image' => null, 'status' => 'active'],
            ['name' => 'Toys', 'slug' => 'toys', 'image' => null, 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }

        // Create sample products
        $products = [
            [
                'name' => 'Laptop Pro 15"',
                'slug' => 'laptop-pro-15',
                'category_id' => 1,
                'description' => 'High-performance laptop with 15-inch display, perfect for professionals and gamers.',
                'price' => 89999,
                'discount_price' => 79999,
                'stock' => 10,
                'image' => null,
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'Wireless Headphones',
                'slug' => 'wireless-headphones',
                'category_id' => 1,
                'description' => 'Premium noise-cancelling wireless headphones with 30-hour battery life.',
                'price' => 12999,
                'stock' => 25,
                'image' => null,
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'Smartphone X',
                'slug' => 'smartphone-x',
                'category_id' => 1,
                'description' => 'Latest smartphone with advanced camera system and 5G connectivity.',
                'price' => 69999,
                'discount_price' => 64999,
                'stock' => 15,
                'image' => null,
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name' => 'Cotton T-Shirt',
                'slug' => 'cotton-t-shirt',
                'category_id' => 2,
                'description' => 'Comfortable 100% cotton t-shirt available in multiple colors.',
                'price' => 599,
                'stock' => 50,
                'image' => null,
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name' => 'Programming Book',
                'slug' => 'programming-book',
                'category_id' => 3,
                'description' => 'Comprehensive guide to modern programming practices and techniques.',
                'price' => 899,
                'stock' => 30,
                'image' => null,
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name' => 'Coffee Maker',
                'slug' => 'coffee-maker',
                'category_id' => 4,
                'description' => 'Automatic coffee maker with programmable settings and thermal carafe.',
                'price' => 4999,
                'discount_price' => 3999,
                'stock' => 8,
                'image' => null,
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name' => 'Yoga Mat',
                'slug' => 'yoga-mat',
                'category_id' => 5,
                'description' => 'Non-slip exercise yoga mat with carrying strap.',
                'price' => 799,
                'stock' => 40,
                'image' => null,
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name' => 'Building Blocks Set',
                'slug' => 'building-blocks-set',
                'category_id' => 6,
                'description' => 'Educational building blocks set for creative play and development.',
                'price' => 1299,
                'stock' => 20,
                'image' => null,
                'status' => 'active',
                'featured' => false,
            ],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }

        // Create sample banners
        $banners = [
            [
                'title' => 'Summer Sale',
                'description' => 'Get up to 50% off on selected items',
                'image' => null,
                'status' => 'active',
            ],
            [
                'title' => 'New Arrivals',
                'description' => 'Check out our latest collection',
                'image' => null,
                'status' => 'active',
            ],
            [
                'title' => 'Free Shipping',
                'description' => 'On orders above ₹500',
                'image' => null,
                'status' => 'active',
            ],
        ];

        foreach ($banners as $banner) {
            \App\Models\Banner::create($banner);
        }
    }
}
