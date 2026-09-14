<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('is_admin', false)->count(),
            'total_banners' => Banner::count(),
            'total_categories' => Category::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_bills' => \App\Models\Bill::count(),
            'recent_orders' => Order::latest()->take(5)->get(),
        ];
        
        return view('admin.dashboard', compact('stats'));
    }
}
