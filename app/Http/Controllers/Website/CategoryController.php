<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;

class CategoryController extends Controller
{
    public function show(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->where('status', 'active')->firstOrFail();
        $products = Product::where('category_id', $category->id)
            ->where('status', 'active');
        
        // Price range filter
        if ($request->has('min_price') && $request->min_price) {
            $products->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && $request->max_price) {
            $products->where('price', '<=', $request->max_price);
        }
        
        // Sorting
        switch ($request->sort) {
            case 'price_low':
                $products->orderBy('price', 'asc');
                break;
            case 'price_high':
                $products->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $products->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $products->orderBy('name', 'desc');
                break;
            case 'newest':
                $products->latest();
                break;
            default:
                $products->latest();
        }
        
        $products = $products->paginate(12);
        $categories = Category::where('status', 'active')
            ->whereHas('products', function($query) {
                $query->where('status', 'active');
            })
            ->get();
        
        // Get user's wishlist product IDs if logged in
        $wishlistProductIds = [];
        if (auth()->check()) {
            $wishlistProductIds = Wishlist::where('user_id', auth()->id())
                                        ->pluck('product_id')
                                        ->toArray();
        }
            
        return view('website.category.show', compact('category', 'products', 'categories', 'wishlistProductIds'));
    }
}
