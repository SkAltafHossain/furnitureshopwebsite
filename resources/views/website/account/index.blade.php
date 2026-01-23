@extends('website.layouts.app')

@section('title', 'My Account')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">My Account</h1>
    
    <div class="row">
        <!-- Account Navigation -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <h5 class="mt-2">{{ auth()->user()->name }}</h5>
                        <p class="text-muted small">{{ auth()->user()->email }}</p>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a href="{{ route('account.index') }}" class="nav-link active">
                            <i class="fas fa-user me-2"></i> Profile Information
                        </a>
                        <a href="{{ route('account.orders') }}" class="nav-link">
                            <i class="fas fa-shopping-bag me-2"></i> My Orders
                        </a>
                        <a href="{{ route('account.orders') }}" class="nav-link">
                            <i class="fas fa-map-marker-alt me-2"></i> Addresses
                        </a>
                        <a href="#" class="nav-link">
                            <i class="fas fa-heart me-2"></i> Wishlist
                        </a>
                        <a href="#" class="nav-link">
                            <i class="fas fa-cog me-2"></i> Settings
                        </a>
                        <hr>
                        <a href="{{ route('logout') }}" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- Profile Information -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('account.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" value="{{ auth()->user()->phone }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Account Status</label>
                                    <input type="text" class="form-control" value="{{ auth()->user()->status }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="fas fa-lock"></i> Change Password
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Account Statistics -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-shopping-bag fa-2x text-primary mb-2"></i>
                            <h4>{{ auth()->user()->orders->count() ?? 0 }}</h4>
                            <p class="text-muted mb-0">Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-box fa-2x text-success mb-2"></i>
                            <h4>{{ auth()->user()->orders()->where('order_status', 'delivered')->count() ?? 0 }}</h4>
                            <p class="text-muted mb-0">Delivered Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-calendar fa-2x text-info mb-2"></i>
                            <h4>{{ auth()->user()->created_at->format('M Y') }}</h4>
                            <p class="text-muted mb-0">Member Since</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
