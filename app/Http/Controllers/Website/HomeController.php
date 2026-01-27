<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('status', 'active')->latest()->get();
        $categories = Category::where('status', 'active')->take(6)->get();
        $latestProducts = Product::where('status', 'active')->latest()->take(8)->get();
        $featuredProducts = Product::where('status', 'active')->where('featured', 1)->take(8)->get();
        
        // Get user's wishlist product IDs if logged in
        $wishlistProductIds = [];
        if (auth()->check()) {
            $wishlistProductIds = Wishlist::where('user_id', auth()->id())
                                        ->pluck('product_id')
                                        ->toArray();
        }
        
        return view('website.home', compact('banners', 'categories', 'latestProducts', 'featuredProducts', 'wishlistProductIds'));
    }
}
