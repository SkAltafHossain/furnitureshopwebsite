<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:10',
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

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $price = $product->discount_price ? $product->discount_price : $product->price;
                $itemTotal = $price * $item['quantity'];
                $totalAmount += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $price,
                    'total' => $itemTotal,
                ];

                // Update stock
                $product->decrement('stock', $item['quantity']);
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
                'customer_address' => $address->address,
                'city' => $address->city,
                'state' => $address->state,
                'postal_code' => $address->postal_code,
                'address_id' => $address->id,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            // Clear cart
            session(['cart' => []]);

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
}
