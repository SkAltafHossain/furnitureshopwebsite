<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\Wishlist;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'profile');
        
        if ($tab === 'wishlist') {
            $wishlistItems = Wishlist::where('user_id', auth()->id())
                                   ->with('product')
                                   ->latest()
                                   ->paginate(12);
            
            // Get array of product IDs that are in wishlist
            $wishlistProductIds = $wishlistItems->pluck('product_id')->toArray();
            
            return view('website.account.index', compact('user', 'tab', 'wishlistItems', 'wishlistProductIds'));
        }
        
        return view('website.account.index', compact('user', 'tab'));
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
    
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = auth()->user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        return back()->with('success', 'Password changed successfully!');
    }
    
    public function addresses()
    {
        $addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();
        return view('website.account.addresses', compact('addresses'));
    }
    
    public function deleteAddress($id)
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        
        // Prevent deletion if it's the only address
        if (auth()->user()->addresses()->count() === 1) {
            return back()->with('error', 'You cannot delete your only address.');
        }
        
        $address->delete();
        return back()->with('success', 'Address deleted successfully!');
    }
    
    public function setDefaultAddress($id)
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        
        // Unset all other default addresses
        auth()->user()->addresses()->update(['is_default' => false]);
        
        // Set this address as default
        $address->update(['is_default' => true]);
        
        return back()->with('success', 'Default address updated successfully!');
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
            ->with(['items.product', 'address'])
            ->firstOrFail();
            
        return view('website.account.order-details', compact('order'));
    }
    
    public function removeFromWishlist($id)
    {
        $wishlistItem = Wishlist::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
            
        $wishlistItem->delete();
        
        return back()->with('success', 'Item removed from wishlist!');
    }
    
    public function cancelOrder($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('order_status', 'pending')
            ->firstOrFail();
            
        $order->order_status = 'cancelled';
        $order->save();
        
        return response()->json([
            'success' => 'Order cancelled successfully',
            'status' => 'cancelled'
        ]);
    }
    
    public function confirmDelivery($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
            
        if ($order->confirmDelivery()) {
            return response()->json([
                'success' => 'Delivery confirmed successfully',
                'status' => 'delivered_confirmed'
            ]);
        }
        
        return response()->json([
            'error' => 'Order cannot be confirmed. It must be delivered and within 24 hours of delivery.'
        ], 422);
    }
}
