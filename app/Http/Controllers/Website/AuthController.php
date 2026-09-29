<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return redirect()->route('home');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'password' => 'required',
        ]);

        // Custom validation: either email or phone must be provided
        if (empty($request->email) && empty($request->phone)) {
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Either email or phone is required.',
                    'errors' => ['email' => 'Either email or phone is required.']
                ], 422);
            }

            return back()->withErrors([
                'email' => 'Either email or phone is required.',
            ])->onlyInput('email');
        }

        // Determine login field (email or phone)
        $loginField = !empty($request->email) ? 'email' : 'phone';
        $loginValue = $request->email ?? $request->phone;

        $authCredentials = [
            $loginField => $loginValue,
            'password' => $request->password,
        ];

        if (Auth::attempt($authCredentials)) {
            $request->session()->regenerate();

            // Merge guest cart with user cart if exists
            $this->mergeGuestCart();

            // Check if request is from modal
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => route('home')
                ]);
            }

            return redirect()->intended(route('home'));
        }

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => ['email' => 'Invalid credentials']
            ], 401);
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ])->onlyInput('email');
    }
    
    public function showRegister()
    {
        return view('website.auth.register');
    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ], [
            'email.unique' => 'The email has already been taken.',
            'phone.unique' => 'The phone number has already been taken.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator->errors())->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        Auth::login($user);

        // Merge guest cart with user cart if exists
        $this->mergeGuestCart();

        // Check if request is from modal
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Registration successful!',
                'redirect' => route('home')
            ]);
        }

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
