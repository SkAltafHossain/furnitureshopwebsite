@extends('website.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container-fluid py-5">
    <h1 class="mb-4">Checkout</h1>
    
    @forelse($cart as $productId => $item)
        <!-- Cart items will be processed in the form -->
    @empty
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted">Add products to your cart before proceeding to checkout.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Start Shopping
            </a>
        </div>
    @endforelse
    
    @if(!empty($cart))
        <form method="POST" action="{{ route('checkout.process') }}">
            @csrf
            <div class="row">
                <!-- Customer Information -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            @if(auth()->check())
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control" name="phone" value="{{ auth()->user()->phone ?? old('phone') }}" required>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    <a href="{{ route('login') }}" class="alert-link">Login</a> or 
                                    <a href="{{ route('register') }}" class="alert-link">register</a> for faster checkout
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}" required>
                                        @error('phone')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                <label class="form-label">Delivery Address</label>
                                
                                @if(auth()->check())
                                    <!-- Always show address selection for logged-in users -->
                                    <div class="mb-3">
                                        @if($addresses->count() > 0)
                                            <!-- Saved Addresses -->
                                            <div class="form-check-group">
                                                @foreach($addresses as $address)
                                                    <div class="form-check mb-2 p-3 border rounded {{ $address->is_default ? 'border-primary' : '' }}">
                                                        <div class="d-flex">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="address_id" 
                                                                       id="address_{{ $address->id }}" value="{{ $address->id }}"
                                                                       {{ $address->is_default ? 'checked' : ''}}>
                                                                <label class="form-check-label w-100" for="address_{{ $address->id }}">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <strong class="text-capitalize">{{ $address->type }}</strong>
                                                                            @if($address->is_default)
                                                                                <span class="badge bg-primary ms-2">Default</span>
                                                                            @endif
                                                                            <div class="small text-muted mt-1">
                                                                                {{ $address->full_address }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            <!-- New Address Option -->
                                            <div class="form-check mb-2 p-3 border rounded">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="address_id" 
                                                           id="new_address" value="">
                                                    <label class="form-check-label w-100" for="new_address">
                                                        <strong>Add New Address</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <!-- Manual Address Input (Hidden by default, shown when new address is selected) -->
                                            <div id="manualAddressInput" style="display: none;" class="mt-3">
                                                <textarea class="form-control" name="address" rows="3" placeholder="Enter your complete delivery address">{{ old('address') }}</textarea>
                                                @error('address')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @else
                                            <!-- No saved addresses - show only Add New Address button -->
                                            <div class="text-center py-4">
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                                    <i class="fas fa-plus me-2"></i>Add New Address
                                                </button>
                                                <p class="text-muted small mt-2">Add a delivery address to continue</p>
                                            </div>
                                            
                                            <!-- Hidden manual address input as fallback -->
                                            <div id="manualAddressInput" style="display: none;" class="mt-3">
                                                <textarea class="form-control" name="address" rows="3" placeholder="Enter your complete delivery address">{{ old('address') }}</textarea>
                                                @error('address')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <!-- Single Address Input for guests -->
                                    <textarea class="form-control" name="address" rows="3" placeholder="Enter your complete delivery address">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Method</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave"></i> Cash on Delivery (COD)
                                </label>
                                <p class="text-muted small ms-4">Pay when you receive your order</p>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="online" value="online">
                                <label class="form-check-label" for="online">
                                    <i class="fas fa-credit-card"></i> Online Payment
                                </label>
                                <p class="text-muted small ms-4">Pay securely using credit/debit card, UPI, or net banking</p>
                            </div>
                            @error('payment_method')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $subtotal = 0;
                                $totalItems = 0;
                                foreach($cart as $productId => $item) {
                                    $product = \App\Models\Product::find($productId);
                                    if ($product) {
                                        $subtotal += $product->price * $item['quantity'];
                                        $totalItems += $item['quantity'];
                                    }
                                }
                                $shipping = $subtotal > 500 ? 0 : 50;
                                $total = $subtotal + $shipping;
                            @endphp
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal ({{ $totalItems }} items)</span>
                                <span>₹{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping</span>
                                <span>{{ $shipping == 0 ? 'FREE' : '₹' . number_format($shipping, 2) }}</span>
                            </div>
                            @if($subtotal <= 500)
                                <small class="text-muted mb-3 d-block">Add ₹{{ number_format(500 - $subtotal, 2) }} more for free shipping</small>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Total</h5>
                                <h5 class="text-primary">₹{{ number_format($total, 2) }}</h5>
                            </div>
                            
                            <!-- Order Items Summary -->
                            <div class="mb-3">
                                <h6>Order Items</h6>
                                <div class="small">
                                    @foreach($cart as $productId => $item)
                                        @php
                                            $product = \App\Models\Product::find($productId);
                                            if (!$product) continue;
                                        @endphp
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>{{ $product->name }} x {{ $item['quantity'] }}</span>
                                            <span>₹{{ number_format($product->price * $item['quantity'], 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check"></i> Place Order
                            </button>
                            
                            <p class="text-muted small text-center mt-2">
                                <i class="fas fa-lock"></i> Your payment information is secure
                            </p>
                        </div>
                    </div>
                    
                    <!-- Terms -->
                    <div class="alert alert-info small">
                        <p class="mb-1">By placing this order, you agree to our:</p>
                        <a href="{{ route('page.terms') }}" target="_blank">Terms & Conditions</a> and 
                        <a href="{{ route('page.privacy') }}" target="_blank">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection

<!-- Add Address Modal Component -->
@include('components.address-modal')

@push('scripts')
<script>
$(document).ready(function() {
    // Handle address selection toggle
    $('input[name="address_id"]').change(function() {
        if ($(this).val() === '') {
            // Show manual address input when "Add New Address" is selected
            $('#manualAddressInput').show();
        } else {
            // Hide manual address input when saved address is selected
            $('#manualAddressInput').hide();
        }
    });
    
    // Form validation for checkout
    $('form[action="{{ route("checkout.process") }}"]').submit(function(e) {
        var phone = $('input[name="phone"]').val();
        var addressSelected = $('input[name="address_id"]:checked').val();
        var customAddress = $('textarea[name="address"]').val();
        
        // Basic phone validation
        if (phone.replace(/\D/g, '').length < 10) {
            e.preventDefault();
            showToast('Please enter a valid phone number', 'error');
            return false;
        }
        
        // Address validation
        if (auth()->check()) {
            // For logged-in users, check if either saved address is selected or manual address is entered
            if (addressSelected === undefined) {
                e.preventDefault();
                showToast('Please select a delivery address', 'error');
                return false;
            }
            if (addressSelected === '' && !customAddress) {
                e.preventDefault();
                showToast('Please enter a delivery address', 'error');
                return false;
            }
        } else {
            // For guests, check if manual address is entered
            if (!customAddress) {
                e.preventDefault();
                showToast('Please enter a delivery address', 'error');
                return false;
            }
        }
        
        // Disable submit button to prevent double submission
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    });
});
</script>
@endpush
