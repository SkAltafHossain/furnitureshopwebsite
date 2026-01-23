<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserManagementController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function getData()
    {
        $users = User::latest()->get();
        return response()->json(['data' => $users]);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'blocked' : 'active';
        $user->save();

        return response()->json(['success' => 'User status updated successfully']);
    }

    public function showOrderHistory($id)
    {
        $user = User::findOrFail($id);
        // For now, return empty orders array - this would be implemented when orders are linked to users
        $orders = [];
        
        return response()->json([
            'user' => $user,
            'orders' => $orders
        ]);
    }
}
