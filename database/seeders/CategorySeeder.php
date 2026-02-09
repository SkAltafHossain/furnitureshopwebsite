<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => Str::slug('Electronics'),
                'image' => 'categories/electronics.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Clothing & Fashion',
                'slug' => Str::slug('Clothing & Fashion'),
                'image' => 'categories/clothing.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Home & Kitchen',
                'slug' => Str::slug('Home & Kitchen'),
                'image' => 'categories/home-kitchen.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => Str::slug('Sports & Outdoors'),
                'image' => 'categories/sports.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Books & Media',
                'slug' => Str::slug('Books & Media'),
                'image' => 'categories/books.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Toys & Games',
                'slug' => Str::slug('Toys & Games'),
                'image' => 'categories/toys.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Beauty & Personal Care',
                'slug' => Str::slug('Beauty & Personal Care'),
                'image' => 'categories/beauty.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Automotive',
                'slug' => Str::slug('Automotive'),
                'image' => 'categories/automotive.jpg',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
