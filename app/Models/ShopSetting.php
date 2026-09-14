<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
    protected $fillable = [
        'shop_name',
        'shop_address',
        'shop_phone',
        'shop_email',
    ];

    public static function getSettings()
    {
        return self::first() ?? self::create([
            'shop_name' => config('app.name', 'Local Shop'),
            'shop_address' => '123 Main Street, City, State - 123456',
            'shop_phone' => '+91 9876543210',
            'shop_email' => 'info@localshop.com',
        ]);
    }
}
