<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $categories = Category::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories)
        ]);
    }

    /**
     * Display the specified category
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Get products by category
     */
    public function products(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $query = Product::where('category_id', $category->id)
            ->where('status', 'active');

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->whereRaw('COALESCE(discount_price, price) >= ?', [$request->min_price]);
        }
        if ($request->filled('max_price')) {
            $query->whereRaw('COALESCE(discount_price, price) <= ?', [$request->max_price]);
        }

        // Sort
        switch ($request->sort) {
            case 'price_low':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate($request->per_page ?? 12);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => new CategoryResource($category),
                'products' => ProductResource::collection($products)
            ],
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }
}
