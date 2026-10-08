<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Product;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Display a listing of user's orders
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product', 'address'])
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $orders,
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ]
        ]);
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['items.product', 'address'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Create a new order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:cod,online',
            'is_buy_now' => 'required|boolean',
            'items' => 'required_if:is_buy_now,true|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $address = Address::where('id', $request->address_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $isBuyNow = $request->input('is_buy_now', false);
        $checkoutItems = collect();

        if ($isBuyNow) {
            // Buy Now checkout - use provided items
            foreach ($request->items as $item) {
                $product = Product::with('category')->findOrFail($item['product_id']);
                $checkoutItems->push((object)[
                    'product' => $product,
                    'quantity' => $item['quantity']
                ]);
            }
        } else {
            // Regular cart checkout - get items from database
            $cartItems = Cart::where('user_id', auth()->id())
                ->with('product')
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty'
                ], 400);
            }

            $checkoutItems = $cartItems;
        }

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $orderItems = [];

            foreach ($checkoutItems as $checkoutItem) {
                $product = $checkoutItem->product;

                if (!$product) {
                    throw new \Exception("Product not found");
                }

                if ($product->stock < $checkoutItem->quantity) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $price = $product->discount_price ? $product->discount_price : $product->price;
                $itemTotal = $price * $checkoutItem->quantity;
                $totalAmount += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $checkoutItem->quantity,
                    'price' => $price,
                    'total' => $itemTotal,
                ];

                // Update stock
                $product->decrement('stock', $checkoutItem->quantity);
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_id' => 'ORD-' . time() . rand(1000, 9999),
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'order_status' => 'pending',
                'customer_name' => auth()->user()->name,
                'customer_email' => auth()->user()->email,
                'customer_phone' => auth()->user()->phone,
                'customer_address' => $address->full_address,
                'address_id' => $address->id,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            // Clear cart from database only for regular checkout
            if (!$isBuyNow) {
                Cart::where('user_id', auth()->id())->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order->load('items.product')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an order
     */
    public function cancel($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('order_status', 'pending')
            ->firstOrFail();

        $order->order_status = 'cancelled';
        $order->save();

        // Restore stock
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => [
                'status' => 'cancelled'
            ]
        ]);
    }

    /**
     * Confirm delivery
     */
    public function confirmDelivery($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->confirmDelivery()) {
            return response()->json([
                'success' => true,
                'message' => 'Delivery confirmed successfully',
                'data' => [
                    'status' => 'delivered_confirmed'
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Order cannot be confirmed. It must be delivered and within 24 hours of delivery.'
        ], 422);
    }

    /**
     * Download order bill as PDF
     */
    public function downloadBill($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['items.product', 'address'])
            ->firstOrFail();

        $shopSettings = ShopSetting::getSettings();

        $pdf = Pdf::loadView('website.orders.pdf', [
            'order' => $order,
            'shop_name' => $shopSettings->shop_name,
            'shop_address' => $shopSettings->shop_address,
            'shop_phone' => $shopSettings->shop_phone,
            'shop_email' => $shopSettings->shop_email,
        ]);

        return $pdf->download('order-' . $order->order_id . '.pdf');
    }
}
