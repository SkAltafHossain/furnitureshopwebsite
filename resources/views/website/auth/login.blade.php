@extends('website.layouts.app')

@section('title', 'Login')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Login to Your Account</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
                    </div>
                </div>
            </div>
            
            <!-- Benefits of Registering -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">Why Register?</h6>
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-check text-success me-2"></i> Track your orders</li>
                        <li><i class="fas fa-check text-success me-2"></i> Faster checkout</li>
                        <li><i class="fas fa-check text-success me-2"></i> Save multiple addresses</li>
                        <li><i class="fas fa-check text-success me-2"></i> View order history</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
