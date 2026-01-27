<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = auth()->id();
        $productId = $request->product_id;

        // Check if product exists
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Check if already in wishlist
        $wishlistItem = Wishlist::where('user_id', $userId)
                               ->where('product_id', $productId)
                               ->first();

        if ($wishlistItem) {
            // Remove from wishlist
            $wishlistItem->delete();
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist',
                'action' => 'removed'
            ]);
        } else {
            // Add to wishlist
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist',
                'action' => 'added'
            ]);
        }
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $wishlistItems = Wishlist::where('user_id', auth()->id())
                               ->with('product')
                               ->latest()
                               ->paginate(12);

        return view('website.wishlist.index', compact('wishlistItems'));
    }
}
