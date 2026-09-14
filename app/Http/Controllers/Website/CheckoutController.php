<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
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
        
        // Debug: Show cart contents
        if (!empty($cart)) {
            $message = 'Cart has ' . count($cart) . ' items: ';
            foreach ($cart as $productId => $item) {
                $message .= 'Product ' . $productId . ' (Qty: ' . $item['quantity'] . '), ';
            }
            // Log instead of alert since alert() doesn't exist in PHP
            \Log::info('Cart debug: ' . $message);
        }
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        $addresses = [];
        $allAddresses = [];
        if (auth()->check()) {
            $allAddresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();
            $addresses = $allAddresses->take(3);
        }
        
        return view('website.checkout.index', compact('cart', 'addresses', 'allAddresses'));
    }
    
    public function process(Request $request)
    {
        $request->merge([
            'payment_method' => strtoupper((string) $request->input('payment_method')),
        ]);

        // Debug: Log incoming request data
        \Log::info('Checkout process called', [
            'request_data' => $request->all(),
            'cart_contents' => session()->get('cart', []),
            'user_id' => auth()->id()
        ]);
        
        // Only allow authenticated users with addresses to checkout
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to proceed with checkout.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address_id' => [
                'nullable',
                Rule::exists('addresses', 'id')->where('user_id', auth()->id())
            ],
            'address' => 'nullable|string|max:500',
            'payment_method' => 'required|in:COD'
        ]);
        
        // Custom validation to ensure either address_id or address is provided
        $validator->after(function ($validator) use ($request) {
            if (auth()->check()) {
                // For logged-in users, require either address_id or address
                if (empty($request->address_id) && empty($request->address)) {
                    $validator->errors()->add('address', 'Please select a delivery address or enter one manually');
                }
            } else {
                // For guests, require address
                if (empty($request->address)) {
                    $validator->errors()->add('address', 'Please enter a delivery address');
                }
            }
        });
        
        if ($validator->fails()) {
            \Log::error('Checkout validation failed', ['errors' => $validator->errors()->toArray()]);
            return back()->withErrors($validator)->withInput();
        }
        
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            \Log::error('Cart is empty during checkout');
            return back()->with('error', 'Your cart is empty');
        }
        
        // Get address
        $address = null;
        $customerAddress = '';
        
        if ($request->address_id) {
            $address = Address::find($request->address_id);
            if (!$address || $address->user_id !== auth()->id()) {
                return back()->with('error', 'Invalid address selected');
            }
            $customerAddress = $address->full_address;
        } elseif ($request->address) {
            $customerAddress = $request->address;
        }
        
        DB::beginTransaction();
        
        try {
            // Validate stock availability
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if (!$product) {
                    return back()->with('error', 'One or more products in your cart are no longer available.');
                }
                if ($product->stock < $item['quantity']) {
                    return back()->with('error', "Insufficient stock for {$product->name}. Available: {$product->stock}, Requested: {$item['quantity']}");
                }
            }
            
            // Calculate total
            $subtotal = 0;
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if ($product) {
                    $price = $product->discount_price ?? $product->price;
                    $subtotal += $price * $item['quantity'];
                }
            }
            
            // Calculate shipping (free if subtotal > 500, otherwise ₹50)
            $shipping = $subtotal > 500 ? 0 : 50;
            $total = $subtotal + $shipping;
            
            // Create order with address_id reference
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_id' => 'ORD' . time() . rand(1000, 9999),
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'customer_address' => $customerAddress,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total_amount' => $total,
                'address_id' => $request->address_id,
                'payment_method' => $request->payment_method,
                'order_status' => 'pending',
                'payment_status' => 'pending',
            ]);
            
            // Create order items and update stock
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if ($product) {
                    $price = $product->discount_price ?? $product->price;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'quantity' => $item['quantity'],
                        'price' => $price,
                        'total' => $price * $item['quantity']
                    ]);
                    
                    // Update product stock
                    $product->decrement('stock', $item['quantity']);
                }
            }
            
            DB::commit();
            
            // Clear cart
            session()->forget('cart');

            if (auth()->check()) {
                return redirect()->route('account.order-details', $order->id)
                    ->with('success', 'Order placed successfully!');
            }

            return redirect()->route('home')
                ->with('success', 'Order placed successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);
            return back()->with('error', 'Failed to place order. Please try again.');
        }
    }
    
    public function storeAddress(Request $request)
    {
        // Debug: Log incoming request data
        \Log::info('Address request data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:home,office,other',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable|boolean'
        ]);
        
        // Convert is_default to proper boolean
        $request->merge([
            'is_default' => filter_var($request->input('is_default'), FILTER_VALIDATE_BOOLEAN)
        ]);
        
        if ($validator->fails()) {
            // Debug: Log validation errors
            \Log::error('Address validation errors:', $validator->errors()->toArray());
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        try {
            // Convert is_default to proper boolean
            $isDefault = filter_var($request->input('is_default'), FILTER_VALIDATE_BOOLEAN);
            
            \Log::info('Address creation - is_default value:', ['is_default' => $isDefault, 'original' => $request->input('is_default')]);
            
            // If setting as default, unset other default addresses
            if ($isDefault) {
                $unsetCount = auth()->user()->addresses()->where('is_default', true)->update(['is_default' => false]);
                \Log::info('Unset default addresses count:', ['count' => $unsetCount]);
            }
            
            $request->merge(['is_default' => $isDefault]);
            
            $address = auth()->user()->addresses()->create($request->all());
            
            \Log::info('Address created:', ['address_id' => $address->id, 'is_default' => $address->is_default]);
            
            return response()->json([
                'success' => true, 
                'address' => $address,
                'message' => 'Address added successfully!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Address creation error:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to add address'], 500);
        }
    }
}
