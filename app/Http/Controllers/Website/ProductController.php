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
        
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $products->where('category_id', $category->id);
            }
        }
        
        $products = $products->latest()->paginate(12);
        $categories = Category::where('status', 'active')->get();
        
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
