<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index');
    }

    public function getData()
    {
        $orders = Order::latest()->get();
        return response()->json(['data' => $orders]);
    }

    public function show($id)
    {
        $order = Order::with('address')->findOrFail($id);
        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->order_status = $request->order_status;
        $order->save();

        return response()->json(['success' => 'Order status updated successfully']);
    }
}
