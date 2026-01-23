<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaticPage;

class StaticPageController extends Controller
{
    public function index()
    {
        return view('admin.static-pages.index');
    }

    public function getData()
    {
        $pages = StaticPage::latest()->get();
        return response()->json(['data' => $pages]);
    }

    public function edit($id)
    {
        $page = StaticPage::findOrFail($id);
        return response()->json($page);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:published,unpublished',
        ]);

        $page = StaticPage::findOrFail($id);
        $page->update([
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->status,
            'last_updated' => now(),
        ]);

        return response()->json(['success' => 'Page updated successfully']);
    }

    public function toggleStatus($id)
    {
        $page = StaticPage::findOrFail($id);
        $page->status = $page->status === 'published' ? 'unpublished' : 'published';
        $page->last_updated = now();
        $page->save();

        return response()->json(['success' => 'Page status updated successfully']);
    }
}
