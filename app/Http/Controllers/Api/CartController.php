<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display the user's cart
     */
    public function index(Request $request)
    {
        $cart = session('cart', []);

        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product_id' => $product->id,
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->discount_price ? ($product->discount_price * $quantity) : ($product->price * $quantity)
                ];
                $total += $cartItems[count($cartItems) - 1]['subtotal'];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'total' => $total,
                'count' => count($cartItems)
            ]
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock'
            ], 400);
        }

        $cart = session('cart', []);
        $cart[$request->product_id] = $request->quantity;
        session(['cart' => $cart]);

        $cartCount = count($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'data' => [
                'cart_count' => $cartCount
            ]
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock'
            ], 400);
        }

        $cart = session('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart'
            ], 404);
        }

        $cart[$id] = $request->quantity;
        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated'
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy($id)
    {
        $cart = session('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart'
            ], 404);
        }

        unset($cart[$id]);
        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => [
                'cart_count' => count($cart)
            ]
        ]);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        session(['cart' => []]);

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }
}
