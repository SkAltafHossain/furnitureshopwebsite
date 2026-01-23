<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StaticPage;

class StaticPagesSeeder extends Seeder
{
    public function run()
    {
        $pages = [
            [
                'title' => 'About Us',
                'content' => '<h2>About Our Shop</h2><p>Welcome to our local shop! We are dedicated to providing quality products and excellent service to our community.</p>',
                'status' => 'published',
                'last_updated' => now(),
            ],
            [
                'title' => 'Terms & Conditions',
                'content' => '<h2>Terms & Conditions</h2><p>By using our website, you agree to these terms and conditions of use.</p>',
                'status' => 'published',
                'last_updated' => now(),
            ],
            [
                'title' => 'Privacy Policy',
                'content' => '<h2>Privacy Policy</h2><p>We are committed to protecting your privacy and personal information.</p>',
                'status' => 'published',
                'last_updated' => now(),
            ],
        ];

        foreach ($pages as $page) {
            StaticPage::create($page);
        }
    }
}
