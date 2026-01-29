<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        
        if (!$product->canBeReviewedBy($user->id)) {
            return back()->with('error', 'You can only review products you have ordered and had delivered.');
        }

        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true, // Auto-approve reviews from verified purchases
        ]);

        return back()->with('success', 'Your review has been submitted successfully!');
    }

    public function destroy(Review $review)
    {
        $user = Auth::user();
        
        if (!$review->canBeDeletedBy($user->id)) {
            return back()->with('error', 'You can only delete your own reviews.');
        }

        $review->delete();

        return back()->with('success', 'Your review has been deleted successfully!');
    }
}
