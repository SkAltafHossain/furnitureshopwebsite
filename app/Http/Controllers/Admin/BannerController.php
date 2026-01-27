<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index()
    {
        return view('admin.banners.index');
    }

    public function getData()
    {
        $banners = Banner::latest()->get();
        return response()->json(['data' => $banners]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = $request->file('banner_image')->store('banners', 'public');

        Banner::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        return response()->json(['success' => 'Banner created successfully']);
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return response()->json($banner);
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
        ];

        if ($request->hasFile('banner_image')) {
            $imagePath = $request->file('banner_image')->store('banners', 'public');
            $data['image'] = $imagePath;
        }

        $banner->update($data);

        return response()->json(['success' => 'Banner updated successfully']);
    }

    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->status = $banner->status === 'active' ? 'inactive' : 'active';
        $banner->save();

        return response()->json(['success' => 'Banner status updated successfully']);
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return response()->json(['success' => 'Banner deleted successfully']);
    }
}
