<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\BannerResource;
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

    /**
     * Get home page data
     */
    public function home()
    {
        // Get active banners
        $banners = Banner::where('status', 'active')
            ->orderBy('id', 'desc')
            ->get();

        // Get 5 products (first 5 by ID)
        $products = Product::with('category')
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->limit(5)
            ->get();

        // Get 10 categories
        $categories = Category::where('status', 'active')
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();

        // Get 5 latest products (by ID descending)
        $latestProducts = Product::with('category')
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Get 5 featured products
        $featuredProducts = Product::with('category')
            ->where('status', 'active')
            ->where('featured', true)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => BannerResource::collection($banners),
                'products' => ProductResource::collection($products),
                'categories' => CategoryResource::collection($categories),
                'latest_products' => ProductResource::collection($latestProducts),
                'featured_products' => ProductResource::collection($featuredProducts),
            ]
        ]);
    }
}
