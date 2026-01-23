<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        return view('website.checkout.index', compact('cart'));
    }
    
    public function process(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,online'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return back()->with('error', 'Your cart is empty');
        }
        
        DB::beginTransaction();
        
        try {
            // Calculate total
            $total = 0;
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if ($product) {
                    $total += $product->price * $item['quantity'];
                }
            }
            
            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_id' => 'ORD' . time() . rand(1000, 9999),
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'customer_address' => $request->address,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'order_status' => 'pending',
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'pending'
            ]);
            
            // Create order items
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if ($product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'total' => $product->price * $item['quantity']
                    ]);
                }
            }
            
            DB::commit();
            
            // Clear cart
            session()->forget('cart');
            
            return redirect()->route('account.order-details', $order->id)
                ->with('success', 'Order placed successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order. Please try again.');
        }
    }
}
