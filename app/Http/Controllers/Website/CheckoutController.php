<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
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
        
        $addresses = [];
        if (auth()->check()) {
            $addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();
        }
        
        return view('website.checkout.index', compact('cart', 'addresses'));
    }
    
    public function process(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address_id' => 'nullable|exists:addresses,id',
            'address' => 'required_without:address_id|string|max:500',
            'payment_method' => 'required|in:cod'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return back()->with('error', 'Your cart is empty');
        }
        
        // Get address
        $address = null;
        if ($request->address_id) {
            $address = Address::find($request->address_id);
            if (!$address || $address->user_id !== auth()->id()) {
                return back()->with('error', 'Invalid address selected');
            }
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
                'customer_address' => $address ? $address->full_address : $request->address,
                'total_amount' => $total,
                'payment_method' => 'cod',
                'order_status' => 'pending',
                'payment_status' => 'pending'
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
    
    public function storeAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:home,office,other',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        try {
            // If setting as default, unset other default addresses
            if ($request->is_default) {
                auth()->user()->addresses()->update(['is_default' => false]);
            }
            
            $address = auth()->user()->addresses()->create($request->all());
            
            return response()->json([
                'success' => true, 
                'address' => $address,
                'message' => 'Address added successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add address'], 500);
        }
    }
}
