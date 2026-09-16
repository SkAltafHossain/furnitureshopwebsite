<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Get shop settings
     */
    public function settings()
    {
        $settings = ShopSetting::getSettings();

        return response()->json([
            'success' => true,
            'data' => [
                'shop_name' => $settings->shop_name,
                'shop_address' => $settings->shop_address,
                'shop_phone' => $settings->shop_phone,
                'shop_email' => $settings->shop_email,
            ]
        ]);
    }
}
