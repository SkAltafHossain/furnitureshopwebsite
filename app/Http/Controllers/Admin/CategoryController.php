<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index');
    }

    public function getData()
    {
        $categories = Category::latest()->get();
        return response()->json(['data' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'category_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = $request->file('category_image')->store('categories', 'public');

        Category::create([
            'category_name' => $request->category_name,
            'slug' => Str::slug($request->category_name),
            'category_image' => $imagePath,
            'status' => $request->status,
        ]);

        return response()->json(['success' => 'Category created successfully']);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,'.$id,
            'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'category_name' => $request->category_name,
            'slug' => Str::slug($request->category_name),
            'status' => $request->status,
        ];

        if ($request->hasFile('category_image')) {
            $imagePath = $request->file('category_image')->store('categories', 'public');
            $data['category_image'] = $imagePath;
        }

        $category->update($data);

        return response()->json(['success' => 'Category updated successfully']);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['success' => 'Category deleted successfully']);
    }
}
