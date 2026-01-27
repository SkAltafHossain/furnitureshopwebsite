<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 'active')->get();
        return view('admin.products.index', compact('categories'));
    }

    public function getData()
    {
        $products = Product::with('category')->latest()->get();
        return response()->json(['data' => $products]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string|max:255|unique:products,name',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = $request->file('product_image')->store('products', 'public');

        Product::create([
            'category_id' => $request->category_id,
            'name' => $request->product_name,
            'slug' => Str::slug($request->product_name),
            'description' => $request->description,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        return response()->json(['success' => 'Product created successfully']);
    }

    public function edit($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string|max:255|unique:products,name,'.$id,
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'name' => $request->product_name,
            'slug' => Str::slug($request->product_name),
            'description' => $request->description,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock' => $request->stock,
            'status' => $request->status,
        ];

        if ($request->hasFile('product_image')) {
            $imagePath = $request->file('product_image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        return response()->json(['success' => 'Product updated successfully']);
    }

    public function toggleStatus($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        // Check if the category is inactive
        if ($product->category->status === 'inactive') {
            return response()->json(['error' => 'Cannot change product status when category is inactive. Please activate the category first.'], 422);
        }
        
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();

        return response()->json(['success' => 'Product status updated successfully']);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['success' => 'Product deleted successfully']);
    }
}
