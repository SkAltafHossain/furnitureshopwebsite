@extends('website.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container-fluid py-5 checkout-page">
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
                <!-- Left Side: Products and Order Summary -->
                <div class="col-md-8">
                    <!-- Cart Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Cart Items ({{ count($cart) }})</h5>
                        </div>
                        <div class="card-body">
                            @foreach($cart as $productId => $item)
                                @php
                                    $product = isset($item['product']) ? $item['product'] : \App\Models\Product::find($productId);
                                    if (!$product) continue;
                                @endphp
                                <div class="cart-item-checkout mb-4 p-4 border rounded-3 shadow-sm">
                                    <div class="row align-items-center g-4">
                                        <!-- Product Image -->
                                        <div class="col-auto">
                                            <div class="product-image-container">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                         alt="{{ $product->name }}"
                                                         class="rounded-3 product-image-checkout">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center rounded-3 product-image-checkout">
                                                        <i class="fas fa-box fa-3x text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Product Details -->
                                        <div class="col">
                                            <h6 class="product-title-checkout mb-2">
                                                <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
                                                    {{ $product->name }}
                                                </a>
                                            </h6>
                                            <p class="product-category-checkout text-muted mb-2">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                            <div class="product-price-checkout">
                                                <span class="current-price fw-bold text-primary fs-5">₹{{ number_format($product->discount_price ?? $product->price, 2) }}</span>
                                                @if($product->discount_price)
                                                    <span class="original-price text-muted text-decoration-line-through ms-3">₹{{ number_format($product->price, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Quantity Controls -->
                                        <div class="col-auto">
                                            <div class="text-center">
                                                <label class="form-label small text-muted mb-2 d-block">Quantity</label>
                                                <div class="quantity-controls d-flex align-items-center border rounded-3 overflow-hidden {{ $isBuyNow ? 'disabled' : '' }}">
                                                    <button class="btn quantity-btn quantity-decrease" type="button" data-product-id="{{ $productId }}" {{ $isBuyNow ? 'disabled' : '' }}>
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" class="form-control border-0 text-center quantity-input fw-bold" value="{{ $item['quantity'] }}" min="1" max="{{ $product->stock }}" data-product-id="{{ $productId }}" {{ $isBuyNow ? 'readonly' : '' }}>
                                                    <button class="btn quantity-btn quantity-increase" type="button" data-product-id="{{ $productId }}" {{ $isBuyNow ? 'disabled' : '' }}>
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                @if($product->stock <= 5)
                                                    <div class="alert alert-warning alert-dismissible fade show mt-2 py-2 px-3 small" role="alert">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        <strong>Only {{ $product->stock }} left!</strong> Order soon.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Item Total & Remove -->
                                        <div class="col-auto text-end">
                                            <label class="form-label small text-muted mb-2 d-block">Total</label>
                                            <div class="item-total-checkout fw-bold text-success fs-4 mb-3">₹{{ number_format(($product->discount_price ?? $product->price) * $item['quantity'], 2) }}</div>
                                            @if(!$isBuyNow)
                                                <button class="btn btn-outline-danger remove-item" data-product-id="{{ $productId }}">
                                                    <i class="fas fa-trash-alt"></i> Remove
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
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
                                        $subtotal += ($product->discount_price ?? $product->price) * $item['quantity'];
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
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check"></i> Place Order
                            </button>
                            
                            <p class="text-muted small text-center mt-2">
                                <i class="fas fa-lock"></i> Your payment information is secure
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side: Customer Information -->
                <div class="col-md-4">
                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            @if(auth()->check())
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control" name="phone" value="{{ auth()->user()->phone ?? old('phone') ?? '' }}" placeholder="Enter your phone number" required>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    <a href="{{ route('login') }}" class="alert-link">Login</a> or 
                                    <a href="{{ route('register') }}" class="alert-link">register</a> for faster checkout
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control" name="phone" value="{{ old('phone') ?? '' }}" placeholder="Enter your phone number" required>
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
                                            @php
                                                $hasDefaultAddress = $addresses->contains('is_default', true);
                                            @endphp
                                            <!-- Saved Addresses -->
                                            <div class="form-check-group">
                                                @foreach($addresses as $address)
                                                    <div class="form-check mb-2 p-3 border rounded {{ $address->is_default ? 'border-primary' : '' }}">
                                                        <div class="d-flex">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="address_id" 
                                                                       id="address_{{ $address->id }}" value="{{ $address->id }}"
                                                                       {{ ($address->is_default || (!$hasDefaultAddress && $loop->first)) ? 'checked' : ''}}>
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
                                            
                                            <!-- View All Addresses Option (if more than 3) -->
                                            @if($allAddresses->count() > 3)
                                                <div class="text-center py-2">
                                                    <a href="{{ route('account.addresses') }}" class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-list me-2"></i>View All {{ $allAddresses->count() }} Addresses
                                                    </a>
                                                </div>
                                            @endif
                                            
                                            <!-- New Address Option (only show if less than 3 addresses) -->
                                            @if($allAddresses->count() < 3)
                                                <div class="text-center py-4">
                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                                        <i class="fas fa-plus me-2"></i>Add New Address
                                                    </button>
                                                </div>
                                            @endif
                                            
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
                                                    <i class="fas fa-plus me-2"></i>Add Your First Address
                                                </button>
                                                <p class="text-muted small mt-2">Add a delivery address to continue</p>
                                            </div>
                                            
                                            <!-- Manual address input as fallback -->
                                            <div id="manualAddressInput" class="mt-3">
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
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave"></i> Cash on Delivery (COD)
                                </label>
                                <p class="text-muted small ms-4">Pay when you receive your order</p>
                            </div>
                            @error('payment_method')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Terms -->
                    <div class="alert alert-info small mt-3">
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

@push('styles')
<style>
/* Checkout Cart Items Styles */
.cart-item-checkout {
    background: white;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef !important;
}

/* Stock Warning Alert Styles */
.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%) !important;
    border: 2px solid #ffc107 !important;
    border-radius: 8px !important;
    color: #856404 !important;
    font-weight: 600 !important;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
    }
}

.cart-item-checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    border-color: #0d6efd !important;
}

.product-image-checkout {
    width: 100px;
    height: 100px;
    object-fit: cover;
    transition: transform 0.3s ease;
    border: 2px solid #f8f9fa;
}

.product-image-checkout:hover {
    transform: scale(1.05);
    border-color: #0d6efd;
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.2);
}

.product-title-checkout {
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.4;
    color: #2c3e50;
    transition: color 0.3s ease;
}

.product-title-checkout:hover {
    color: #0d6efd;
}

.product-category-checkout {
    font-size: 0.9rem;
    font-weight: 500;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-price-checkout .current-price {
    font-size: 1.2rem;
    font-weight: 700;
    color: #0d6efd;
}

.product-price-checkout .original-price {
    font-size: 1rem;
    font-weight: 500;
    color: #9ca3af;
}

.item-total-checkout {
    font-size: 1.4rem;
    font-weight: 700;
    color: #28a745;
}

/* Quantity Controls */
.quantity-controls {
    background: #f8f9fa;
    border: 2px solid #e9ecef !important;
    transition: all 0.3s ease;
    height: 44px;
    width: 140px;
}

.quantity-controls:hover {
    background: #e9ecef;
    border-color: #0d6efd !important;
}

.quantity-btn {
    padding: 0 12px !important;
    background: transparent;
    border: none;
    color: #6c757d;
    transition: all 0.2s ease;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity-btn:hover {
    background: #0d6efd;
    color: white;
}

.quantity-btn:active {
    transform: scale(0.9);
}

.quantity-input {
    font-size: 16px;
    font-weight: 700;
    color: #495057;
    background: transparent;
    border: none;
    text-align: center;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    pointer-events: none;
}

.quantity-input:focus {
    outline: none;
    box-shadow: none;
}

/* Desktop Styles */
@media (min-width: 769px) {
    .cart-item-checkout {
        border-radius: 16px !important;
        padding: 1.5rem !important;
    }
    
    .product-image-checkout {
        width: 120px;
        height: 120px;
    }
    
    .product-title-checkout {
        font-size: 1.2rem;
        font-weight: 700;
    }
    
    .product-price-checkout .current-price {
        font-size: 1.4rem;
    }
    
    .item-total-checkout {
        font-size: 1.6rem;
    }
    
    .quantity-controls {
        width: 160px;
        height: 48px;
    }
}

/* Mobile Styles */
@media (max-width: 768px) {
    .cart-item-checkout {
        padding: 1rem !important;
    }
    
    .product-image-checkout {
        width: 70px;
        height: 70px;
    }
    
    .product-title-checkout {
        font-size: 1rem;
    }
    
    .product-price-checkout .current-price {
        font-size: 1.1rem;
    }
    
    .item-total-checkout {
        font-size: 1.2rem;
    }
    
    .quantity-controls {
        width: 120px;
        height: 40px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Cart URLs
var cartUrls = {
    add: '{{ route("cart.add") }}',
    update: '{{ route("cart.update") }}',
    remove: '{{ route("cart.remove") }}'
};

var csrfToken = '{{ csrf_token() }}';
var isBuyNow = {{ $isBuyNow ? 'true' : 'false' }};

$(document).ready(function() {
    var updating = false; // Flag to prevent multiple updates

    // Only enable quantity controls if not buy-now checkout
    if (!isBuyNow) {
        // Update quantity - Simple version
        $('.quantity-decrease').click(function() {
            if (updating) return;
            console.log('Decrease clicked');
            var $input = $(this).siblings('.quantity-input');
            var currentVal = parseInt($input.val()) || 1;
            console.log('Current value:', currentVal);
            if (currentVal > 1) {
                $input.val(currentVal - 1);
                console.log('Set input to:', currentVal - 1);
                updateCartQuantity($input.data('product-id'), currentVal - 1, $input);
            }
        });

        $('.quantity-increase').click(function() {
            if (updating) return;
            console.log('Increase clicked');
            var $input = $(this).siblings('.quantity-input');
            var currentVal = parseInt($input.val()) || 1;
            var max = parseInt($input.attr('max')) || 9999;
            console.log('Current value:', currentVal, 'Max:', max);
            if (currentVal < max) {
                $input.val(currentVal + 1);
                console.log('Set input to:', currentVal + 1);
                updateCartQuantity($input.data('product-id'), currentVal + 1, $input);
            }
        });

        // Remove item
        $('.remove-item').click(function() {
            var productId = $(this).data('product-id');

            if (confirm('Are you sure you want to remove this item from your cart?')) {
                removeFromCart(productId);
            }
        });
    }
    
    function updateCartQuantity(productId, quantity, input) {
        if (updating) return;
        updating = true;
        console.log('Sending to server:', productId, quantity);
        $.ajax({
            url: cartUrls.update,
            method: 'PATCH',
            data: {
                product_id: productId,
                quantity: quantity,
                _token: csrfToken
            },
            success: function(response) {
                console.log('Server response:', response);
                if (response.success) {
                    // Update cart totals without reloading
                    updateCartTotals();
                    showCheckoutToast('Cart updated successfully', 'success');
                } else {
                    // Revert the input value if update failed
                    var currentVal = parseInt(input.val());
                    input.val(currentVal - 1); // Simple revert
                    showCheckoutToast(response.message || 'Error updating cart', 'error');
                }
                updating = false;
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', error);
                // Revert the input value if error occurred
                var currentVal = parseInt(input.val());
                input.val(currentVal - 1); // Simple revert
                showCheckoutToast('Error occurred while updating cart.', 'error');
                updating = false;
            }
        });
    }
    
    function updateCartTotals() {
        console.log('updateCartTotals called');
        // Update the order summary section
        var subtotal = 0;
        var totalItems = 0;
        
        $('.quantity-input').each(function() {
            var productId = $(this).data('product-id');
            var quantity = parseInt($(this).val());
            console.log('Reading input value:', quantity, 'for product:', productId);
            var priceElement = $(this).closest('.cart-item-checkout').find('.current-price');
            var price = parseFloat(priceElement.text().replace('₹', '').replace(',', ''));
            
            subtotal += price * quantity;
            totalItems += quantity;
            
            // Update item total
            var itemTotal = price * quantity;
            $(this).closest('.cart-item-checkout').find('.text-end .fw-bold').text('₹' + itemTotal.toFixed(2));
        });
        
        var shipping = subtotal > 500 ? 0 : 50;
        var total = subtotal + shipping;
        
        // Update order summary
        $('.card-header:contains("Order Summary")').closest('.card').find('.d-flex justify-content-between').each(function() {
            var text = $(this).find('span:first').text();
            if (text.includes('Subtotal')) {
                $(this).find('span:last').text('₹' + subtotal.toFixed(2));
            } else if (text.includes('Shipping')) {
                $(this).find('span:last').text(shipping == 0 ? 'FREE' : '₹' + shipping.toFixed(2));
            } else if (text.includes('Total')) {
                $(this).find('span:last').text('₹' + total.toFixed(2));
            }
        });
        
        // Update cart items count
        $('.card-header:contains("Cart Items")').text('Cart Items (' + totalItems + ')');
    }
    
    function showCheckoutToast(message, type) {
        // Simple toast notification
        var toast = $('<div class="alert alert-' + type + ' position-fixed top-0 end-0 m-3" style="z-index: 9999;">' + message + '</div>');
        $('body').append(toast);
        setTimeout(function() {
            toast.fadeOut(function() {
                toast.remove();
            });
        }, 3000);
    }
    
    function removeFromCart(productId) {
        $.ajax({
            url: cartUrls.remove,
            method: 'DELETE',
            data: {
                product_id: productId,
                _token: csrfToken
            },
            success: function(response) {
                if (response.success) {
                    location.reload(); // Reload to update cart
                } else {
                    showCheckoutToast(response.message || 'Error removing item', 'error');
                }
            },
            error: function() {
                showCheckoutToast('Error occurred while removing item.', 'error');
            }
        });
    }
    
    function addToCart(productId, quantity) {
        $.ajax({
            url: cartUrls.add,
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                _token: csrfToken
            },
            success: function(response) {
                // Update cart count
                var cartBadge = $('.action-badge');
                cartBadge.text(response.cart_count);
                
                // Show badge if cart has items, hide if empty
                if (response.cart_count > 0) {
                    cartBadge.show();
                } else {
                    cartBadge.hide();
                }
                
                showCheckoutToast(response.message, 'success');
            },
            error: function(xhr) {
                var message = 'Error occurred while adding product to cart.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showCheckoutToast(message, 'error');
            }
        });
    }
    
    // Handle address selection toggle
    $('input[name="address_id"]').change(function() {
        if ($(this).val() === '') {
            // Show manual address input when "Add New Address" is selected
            $('#manualAddressInput').show();
        } else {
            // Hide manual address input when saved address is selected
            $('#manualAddressInput').hide();
            $('textarea[name="address"]').val(''); // Clear the value when hidden
        }
    });
    
    // Initialize: clear manual address input if address is selected
    var selectedAddress = $('input[name="address_id"]:checked').val();
    if (selectedAddress && selectedAddress !== '') {
        $('textarea[name="address"]').val('');
    }
    
    // Form validation for checkout
    $('form[action="{{ route("checkout.process") }}"]').submit(function(e) {
        // Get the first visible phone input
        var phoneInput = $('input[name="phone"]:visible').first();
        var phone = phoneInput.val();
        var addressSelected = $('input[name="address_id"]:checked').val();
        var customAddress = $('textarea[name="address"]').val();
        
        // Basic phone validation (more flexible for international numbers)
        var cleanPhone = phone.replace(/\D/g, '');
        if (cleanPhone.length < 10) {
            e.preventDefault();
            showCheckoutToast('Please enter a valid phone number (at least 10 digits)', 'error');
            return false;
        }
        
        // Additional validation for common formats
        if (!phone.match(/^[\+]?[0-9\s\-\(\)]+$/)) {
            e.preventDefault();
            showCheckoutToast('Please enter a valid phone number format', 'error');
            return false;
        }
        
        // Address validation
        @if(auth()->check())
            // For logged-in users, require either address selection or manual entry
            if (!addressSelected && !customAddress) {
                e.preventDefault();
                showCheckoutToast('Please select a delivery address or enter one manually', 'error');
                return false;
            }
        @else
            // For guests, check if manual address is entered
            if (!customAddress) {
                e.preventDefault();
                showCheckoutToast('Please enter a delivery address', 'error');
                return false;
            }
        @endif
        
        // Disable submit button to prevent double submission
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    });
});
</script>
@endpush
