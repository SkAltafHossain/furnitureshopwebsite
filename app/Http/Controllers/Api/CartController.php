<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
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
        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with('product')
            ->get();

        $items = [];
        $total = 0;

        foreach ($cartItems as $cartItem) {
            $price = $cartItem->product->discount_price ?? $cartItem->product->price;
            $subtotal = $price * $cartItem->quantity;
            $total += $subtotal;

            $items[] = [
                'id' => $cartItem->id,
                'product_id' => $cartItem->product_id,
                'product' => $cartItem->product,
                'quantity' => $cartItem->quantity,
                'subtotal' => $subtotal
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'total' => $total,
                'count' => count($items)
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

        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        $cartCount = Cart::where('user_id', $request->user()->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'data' => [
                'cart_count' => $cartCount,
                'cart_item' => $cartItem
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

        $cartItem = Cart::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart'
            ], 404);
        }

        $product = Product::findOrFail($cartItem->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock'
            ], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated'
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, $id)
    {
        $cartItem = Cart::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart'
            ], 404);
        }

        $cartItem->delete();

        $cartCount = Cart::where('user_id', $request->user()->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => [
                'cart_count' => $cartCount
            ]
        ]);
    }

    /**
     * Clear cart
     */
    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }

    /**
     * Buy Now - Get product details for single item checkout
     */
    public function buyNow(Request $request)
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

        $product = Product::with('category')->findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock'
            ], 400);
        }

        $price = $product->discount_price ?? $product->price;
        $subtotal = $price * $request->quantity;
        $shipping = $subtotal > 500 ? 0 : 50;
        $total = $subtotal + $shipping;

        return response()->json([
            'success' => true,
            'message' => 'Product ready for checkout',
            'data' => [
                'product' => $product,
                'quantity' => $request->quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
                'is_buy_now' => true
            ]
        ]);
    }
}
