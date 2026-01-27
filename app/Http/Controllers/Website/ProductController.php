<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = null;
        $products = Product::where('status', 'active');
        
        // Category filter
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $products->where('category_id', $category->id);
            }
        }
        
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
        
        return view('website.products.index', compact('products', 'categories', 'category'));
    }
    
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->where('status', 'active')->firstOrFail();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(4)
            ->get();
            
        return view('website.products.show', compact('product', 'relatedProducts'));
    }
}
