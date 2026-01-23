<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->where('status', 'active')->firstOrFail();
        $products = Product::where('category_id', $category->id)
            ->where('status', 'active')
            ->latest()
            ->paginate(12);
            
        return view('website.category.show', compact('category', 'products'));
    }
}
