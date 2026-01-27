@extends('website.layouts.app')

@section('title', 'My Account')

@section('content')
<div class="container-fluid py-5">
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
                        <a href="{{ route('account.addresses') }}" class="nav-link">
                            <i class="fas fa-map-marker-alt me-2"></i> Addresses
                        </a>
                        <a href="{{ route('account.index', ['tab' => 'wishlist']) }}" class="nav-link {{ $tab ?? 'profile' === 'wishlist' ? 'active' : '' }}">
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
            @if(($tab ?? 'profile') === 'wishlist')
                <!-- Wishlist Content -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-heart text-danger me-2"></i>
                            My Wishlist
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(isset($wishlistItems) && $wishlistItems->count() > 0)
                            <div class="row">
                                @foreach($wishlistItems as $item)
                                    @if($item->product)
                                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                            <!-- Product Card Component with active wishlist -->
                                            <x-product-card 
                                                :product="$item->product" 
                                                :showAddToCart="true" 
                                                :showWishlist="true"
                                                :showCategory="false"
                                                :showRating="true"
                                                :wishlistActive="true"
                                                :wishlistId="$item->id"
                                            />
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $wishlistItems->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-heart fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Your wishlist is empty</h4>
                                <p class="text-muted">Start adding products you love to your wishlist!</p>
                                <a href="{{ route('products.index') }}" class="btn btn-primary">
                                    <i class="fas fa-shopping-bag me-2"></i> Browse Products
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Original Profile Content -->
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
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="fas fa-lock"></i> Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-3 flex-wrap">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                <i class="fas fa-plus me-2"></i>Add New Address
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fas fa-lock me-2"></i>Change Password
                            </button>
                        </div>
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
            @endif
        </div>
    </div>
</div>

<!-- Add Address Modal Component -->
@include('components.address-modal')

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="fas fa-lock me-2"></i>Change Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('account.change-password') }}">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label for="current_password" class="form-label fw-semibold">Current Password</label>
                        <input type="password" class="form-control form-control-lg @error('current_password') is-invalid @enderror" 
                               id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">New Password</label>
                        <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-2">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" name="password_confirmation" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer bg-light p-4">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-lg px-5" style="min-height: 48px;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-lg px-5" style="min-height: 48px;">
                            <i class="fas fa-save me-2"></i>Change Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
