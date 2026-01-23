<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('website.auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Merge guest cart with user cart if exists
            $this->mergeGuestCart();
            
            return redirect()->intended(route('home'));
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    
    public function showRegister()
    {
        return view('website.auth.register');
    }
    
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'status' => 'active',
        ]);
        
        Auth::login($user);
        
        // Merge guest cart with user cart if exists
        $this->mergeGuestCart();
        
        return redirect()->route('home')->with('success', 'Registration successful!');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }
    
    private function mergeGuestCart()
    {
        $guestCart = session()->get('cart', []);
        
        if (!empty($guestCart) && auth()->check()) {
            // Here you could implement logic to merge with existing user cart from database
            // For now, we'll just keep the guest cart in session
            session()->put('cart', $guestCart);
        }
    }
}
