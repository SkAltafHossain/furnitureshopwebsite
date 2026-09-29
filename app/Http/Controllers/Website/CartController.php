<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = [];
        if (auth()->check()) {
            $cartItems = Cart::where('user_id', auth()->id())
                ->with('product')
                ->get();

            foreach ($cartItems as $cartItem) {
                $cart[$cartItem->product_id] = [
                    'quantity' => $cartItem->quantity,
                    'added_at' => $cartItem->created_at,
                    'cart_item_id' => $cartItem->id
                ];
            }
        } else {
            $cart = session()->get('cart', []);
        }

        return view('website.cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $product = Product::findOrFail($productId);

        if ($product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock'
            ], 400);
        }

        if (auth()->check()) {
            $cartItem = Cart::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                $cartItem = Cart::create([
                    'user_id' => auth()->id(),
                    'product_id' => $productId,
                    'quantity' => $quantity
                ]);
            }

            $cartCount = Cart::where('user_id', auth()->id())->count();
        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $quantity;
            } else {
                $cart[$productId] = [
                    'quantity' => $quantity,
                    'added_at' => now()
                ];
            }

            session()->put('cart', $cart);
            $cartCount = count($cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => $cartCount
        ]);
    }

    public function update(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        if (auth()->check()) {
            $cartItem = Cart::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                if ($quantity > 0) {
                    $product = Product::findOrFail($productId);
                    if ($product->stock < $quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient stock'
                        ], 400);
                    }
                    $cartItem->quantity = $quantity;
                    $cartItem->save();
                } else {
                    $cartItem->delete();
                }
            }
        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$productId])) {
                if ($quantity > 0) {
                    $cart[$productId]['quantity'] = $quantity;
                } else {
                    unset($cart[$productId]);
                }
            }

            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully'
        ]);
    }

    public function remove(Request $request)
    {
        $productId = $request->input('product_id');

        if (auth()->check()) {
            Cart::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->delete();
        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$productId])) {
                unset($cart[$productId]);
            }

            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart successfully'
        ]);
    }
}
