<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $product = Product::findOrFail($productId);

        if ($product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock'
            ], 400);
        }

        // Store buy-now items in session (structured like API)
        session()->put('buy_now_items', [
            [
                'product_id' => $productId,
                'quantity' => $quantity
            ]
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => route('checkout.index')
        ]);
    }

    public function index(Request $request)
    {
        $cart = [];
        $isBuyNow = false;

        // Clear buy-now session if coming from cart
        if ($request->has('clear_buy_now')) {
            session()->forget('buy_now_items');
        }

        // Check if this is a buy-now checkout
        $buyNowItems = session()->get('buy_now_items');
        if ($buyNowItems) {
            $isBuyNow = true;
            foreach ($buyNowItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $cart[$item['product_id']] = [
                    'quantity' => $item['quantity'],
                    'added_at' => now(),
                    'cart_item_id' => null,
                    'product' => $product
                ];
            }
        } else {
            // Regular cart checkout
            if (auth()->check()) {
                $cartItems = Cart::where('user_id', auth()->id())
                    ->with('product')
                    ->get();

                foreach ($cartItems as $cartItem) {
                    $cart[$cartItem->product_id] = [
                        'quantity' => $cartItem->quantity,
                        'added_at' => $cartItem->created_at,
                        'cart_item_id' => $cartItem->id,
                        'product' => $cartItem->product
                    ];
                }
            } else {
                // For guest checkout, load products from session cart
                $sessionCart = session()->get('cart', []);
                foreach ($sessionCart as $productId => $item) {
                    $product = Product::find($productId);
                    if ($product) {
                        $cart[$productId] = [
                            'quantity' => $item['quantity'],
                            'added_at' => $item['added_at'] ?? now(),
                            'cart_item_id' => null,
                            'product' => $product
                        ];
                    }
                }
            }
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

        return view('website.checkout.index', compact('cart', 'addresses', 'allAddresses', 'isBuyNow'));
    }
    
    public function process(Request $request)
    {
        $request->merge([
            'payment_method' => strtoupper((string) $request->input('payment_method')),
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

        // Check if this is a buy-now checkout or regular cart checkout
        $buyNowItems = session()->get('buy_now_items');
        $isBuyNow = !empty($buyNowItems);

        // Get items to checkout
        $checkoutItems = collect();

        if ($isBuyNow) {
            // Buy Now checkout - use items from session
            foreach ($buyNowItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $checkoutItems->push((object)[
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'product' => $product
                ]);
            }
        } else {
            // Regular cart checkout
            $cartItems = Cart::where('user_id', auth()->id())
                ->with('product')
                ->get();

            if ($cartItems->isEmpty()) {
                \Log::error('Cart is empty during checkout');
                return back()->with('error', 'Your cart is empty');
            }

            $checkoutItems = $cartItems;
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
            foreach ($checkoutItems as $item) {
                $product = $item->product;
                if (!$product) {
                    return back()->with('error', 'One or more products are no longer available.');
                }
                if ($product->stock < $item->quantity) {
                    return back()->with('error', "Insufficient stock for {$product->name}. Available: {$product->stock}, Requested: {$item->quantity}");
                }
            }

            // Calculate total
            $subtotal = 0;
            foreach ($checkoutItems as $item) {
                $product = $item->product;
                if ($product) {
                    $price = $product->discount_price ?? $product->price;
                    $subtotal += $price * $item->quantity;
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
            foreach ($checkoutItems as $item) {
                $product = $item->product;
                if ($product) {
                    $price = $product->discount_price ?? $product->price;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $price,
                        'total' => $price * $item->quantity
                    ]);

                    // Update product stock
                    $product->decrement('stock', $item->quantity);
                }
            }

            DB::commit();

            // Clear cart from database (only for regular checkout)
            if (!$isBuyNow) {
                Cart::where('user_id', auth()->id())->delete();
            }

            // Clear buy-now session
            session()->forget('buy_now_items');

            return redirect()->route('account.order-details', $order->id)
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
