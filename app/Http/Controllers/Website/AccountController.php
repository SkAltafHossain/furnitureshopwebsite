<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('website.account.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user->update($request->only(['name', 'email', 'phone']));
        
        return back()->with('success', 'Profile updated successfully!');
    }
    
    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
            
        return view('website.account.orders', compact('orders'));
    }
    
    public function orderDetails($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('items.product')
            ->firstOrFail();
            
        return view('website.account.order-details', compact('order'));
    }
}
