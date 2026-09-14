<?php

namespace Database\Seeders;

use App\Models\ShopSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShopSetting::create([
            'shop_name' => config('app.name', 'Local Shop'),
            'shop_address' => '123 Main Street, City, State - 123456',
            'shop_phone' => '+91 9876543210',
            'shop_email' => 'info@localshop.com',
        ]);
    }
}
