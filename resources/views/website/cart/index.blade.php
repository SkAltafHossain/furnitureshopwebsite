@extends('website.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container-fluid py-5">
    <h1 class="mb-4">Shopping Cart</h1>
    
    @if(count($cart) > 0)
        <div class="row">
            <!-- Cart Items -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Cart Items ({{ count($cart) }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach($cart as $productId => $item)
                            @php
                                $product = \App\Models\Product::find($productId);
                                if (!$product) continue;
                            @endphp
                            <div class="cart-item desktop-cart-item mb-4 p-4 border rounded-3 shadow-sm">
                                <div class="row align-items-center g-4">
                                    <!-- Product Image -->
                                    <div class="col-auto">
                                        <div class="product-image-container">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="rounded-3 product-image">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded-3 product-image">
                                                    <i class="fas fa-box fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="col">
                                        <h6 class="product-title mb-2">
                                            <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
                                                {{ $product->name }}
                                            </a>
                                        </h6>
                                        <p class="product-category text-muted mb-2">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                        <div class="product-price">
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
                                            <div class="quantity-controls d-flex align-items-center border rounded-3 overflow-hidden">
                                                <button class="btn quantity-btn quantity-decrease" type="button" data-product-id="{{ $productId }}">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control border-0 text-center quantity-input fw-bold" value="{{ $item['quantity'] }}" min="1" max="{{ $product->stock }}" data-product-id="{{ $productId }}" readonly>
                                                <button class="btn quantity-btn quantity-increase" type="button" data-product-id="{{ $productId }}">
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
                                        <div class="item-total fw-bold text-success fs-4 mb-3">₹{{ number_format(($product->discount_price ?? $product->price) * $item['quantity'], 2) }}</div>
                                        <button class="btn btn-outline-danger remove-item" data-product-id="{{ $productId }}">
                                            <i class="fas fa-trash-alt"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Continue Shopping -->
                <div class="mt-3">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>
            
            <!-- Cart Summary -->
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
                        
                        <a href="{{ route('checkout.index') }}?clear_buy_now=1" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-lock"></i> Proceed to Checkout
                        </a>
                        
                        <p class="text-muted small text-center">
                            <i class="fas fa-shield-alt"></i> Secure Checkout
                        </p>
                    </div>
                </div>
                
                <!-- Promo Code -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="mb-3">Promo Code</h6>
                        <form class="d-flex">
                            <input type="text" class="form-control me-2" placeholder="Enter code">
                            <button type="submit" class="btn btn-outline-primary">Apply</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted">Looks like you haven't added any products to your cart yet.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
/* Cart Item Styles */
.cart-item {
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

.cart-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    border-color: #0d6efd !important;
}

.product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-image:hover {
    transform: scale(1.05);
}

.product-title {
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.4;
    color: #2c3e50;
}

.product-category {
    font-size: 0.85rem;
    font-weight: 500;
}

.current-price {
    font-size: 1.1rem;
}

.original-price {
    font-size: 0.9rem;
}

/* Quantity Controls */
.quantity-controls {
    background: #f8f9fa;
    border: 2px solid #e9ecef !important;
    transition: all 0.3s ease;
    height: 44px;
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

.item-total {
    font-size: 1.2rem;
}

/* Desktop Styles */
@media (min-width: 769px) {
    .desktop-cart-item {
        border-radius: 16px !important;
        background: white;
        border: 1px solid #e9ecef !important;
        transition: all 0.3s ease;
    }
    
    .desktop-cart-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12) !important;
        border-color: #0d6efd !important;
    }
    
    .product-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 2px solid #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .product-image:hover {
        transform: scale(1.05);
        border-color: #0d6efd;
        box-shadow: 0 8px 25px rgba(13, 110, 253, 0.2);
    }
    
    .product-title {
        font-size: 1.2rem;
        font-weight: 700;
        line-height: 1.4;
        color: #2c3e50;
        transition: color 0.3s ease;
    }
    
    .product-title:hover {
        color: #0d6efd;
    }
    
    .product-category {
        font-size: 0.95rem;
        font-weight: 500;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .current-price {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0d6efd;
        text-shadow: 0 1px 2px rgba(13, 110, 253, 0.1);
    }
    
    .original-price {
        font-size: 1.1rem;
        font-weight: 500;
        color: #9ca3af;
    }
    
    .quantity-controls {
        width: 160px !important;
        height: 48px;
        background: #f8f9fa;
        border: 2px solid #e9ecef !important;
        border-radius: 12px !important;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .quantity-controls:hover {
        background: #fff;
        border-color: #0d6efd !important;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.15);
        transform: translateY(-1px);
    }
    
    .quantity-btn {
        padding: 0 16px !important;
        background: transparent;
        border: none;
        color: #6c757d;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .quantity-btn:hover {
        background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
        color: white;
        transform: scale(1.05);
    }
    
    .quantity-btn:active {
        transform: scale(0.95);
    }
    
    .quantity-input {
        font-size: 18px;
        font-weight: 800;
        color: #2c3e50;
        background: transparent;
        border: none;
        text-align: center;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        pointer-events: none;
        letter-spacing: 1px;
    }
    
    .quantity-input:focus {
        outline: none;
        box-shadow: none;
    }
    
    .item-total {
        font-size: 1.6rem;
        font-weight: 800;
        color: #28a745;
        text-shadow: 0 1px 2px rgba(40, 167, 69, 0.1);
        letter-spacing: 0.5px;
    }
    
    .form-label {
        font-size: 0.85rem !important;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c757d;
        margin-bottom: 0.75rem !important;
    }
    
    .btn-outline-danger {
        border: 2px solid #dc3545;
        color: #dc3545;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .btn-outline-danger:hover {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border-color: transparent;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
    }
    
    /* Desktop Card Improvements */
    .card {
        border: none !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        border-radius: 20px !important;
        background: white;
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #e9ecef !important;
        padding: 1.5rem !important;
        border-radius: 20px 20px 0 0 !important;
    }
    
    .card-header h5 {
        font-size: 1.3rem;
        font-weight: 800;
        color: #2c3e50;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
    }
    
    .card-body {
        padding: 2rem !important;
    }
    
    /* Desktop Continue Shopping Button */
    .btn-outline-primary {
        border: 2px solid #0d6efd;
        color: #0d6efd;
        font-weight: 700;
        padding: 12px 30px;
        border-radius: 12px;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
        border-color: transparent;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
    }
    
    /* Desktop Cart Summary */
    .col-md-4 .card {
        border-radius: 20px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
    }
    
    .col-md-4 .card .card-body {
        padding: 2rem !important;
    }
    
    .col-md-4 .card .d-flex.justify-content-between {
        font-size: 1rem;
        margin-bottom: 1rem !important;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .col-md-4 .card .d-flex.justify-content-between:last-of-type {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem !important;
    }
    
    .col-md-4 .card h5 {
        font-size: 1.2rem !important;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .col-md-4 .card .btn {
        font-size: 1rem;
        padding: 16px 24px;
        border-radius: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        height: auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .col-md-4 .card .small {
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    /* Desktop Promo Code */
    .col-md-4 .card:last-child .d-flex {
        flex-direction: row;
        gap: 0.75rem;
    }
    
    .col-md-4 .card:last-child .form-control {
        margin-right: 0 !important;
        margin-bottom: 0 !important;
        height: 48px;
        border-radius: 12px;
        font-size: 0.95rem;
        border: 2px solid #e9ecef;
        font-weight: 500;
    }
    
    .col-md-4 .card:last-child .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .col-md-4 .card:last-child .btn {
        width: auto;
        height: 48px;
        min-width: 120px;
    }
}

/* Tablet Styles */
@media (max-width: 768px) {
    .cart-item {
        margin-bottom: 1.5rem !important;
        padding: 1.25rem !important;
        border-radius: 16px !important;
    }
    
    .product-image {
        width: 70px;
        height: 70px;
    }
    
    .product-title {
        font-size: 0.95rem;
        line-height: 1.3;
    }
    
    .product-category {
        font-size: 0.8rem;
    }
    
    .current-price {
        font-size: 1rem;
    }
    
    .original-price {
        font-size: 0.85rem;
    }
    
    .quantity-controls {
        height: 40px;
    }
    
    .quantity-btn {
        padding: 0 10px !important;
        font-size: 0.9rem;
    }
    
    .quantity-input {
        font-size: 14px;
    }
    
    .item-total {
        font-size: 1.1rem;
    }
    
    .form-label {
        font-size: 0.8rem !important;
    }
}

/* Mobile Styles */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    
    .card {
        border: none !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05) !important;
        border-radius: 20px !important;
        margin-bottom: 1rem;
    }
    
    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #e9ecef !important;
        padding: 1rem !important;
        border-radius: 20px 20px 0 0 !important;
    }
    
    .card-header h5 {
        font-size: 1rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .cart-item {
        margin-bottom: 1.25rem !important;
        padding: 1rem !important;
        border-radius: 16px !important;
        border: 1px solid #f0f0f0 !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
    }
    
    .cart-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
    }
    
    .product-image {
        width: 60px;
        height: 60px;
        border-radius: 12px !important;
    }
    
    .product-title {
        font-size: 0.9rem;
        line-height: 1.3;
        margin-bottom: 0.5rem !important;
    }
    
    .product-category {
        font-size: 0.75rem;
        margin-bottom: 0.75rem !important;
    }
    
    .current-price {
        font-size: 0.95rem;
    }
    
    .original-price {
        font-size: 0.8rem;
    }
    
    .quantity-controls {
        width: 100% !important;
        height: 44px;
        border-radius: 12px !important;
        border: 2px solid #e9ecef !important;
    }
    
    .quantity-btn {
        padding: 0 14px !important;
        font-size: 0.85rem;
    }
    
    .quantity-input {
        font-size: 14px;
        font-weight: 600;
    }
    
    .item-total {
        font-size: 1rem;
    }
    
    .form-label {
        font-size: 0.75rem !important;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
    }
    
    /* Cart Summary Mobile Styles */
    .col-md-4 .card {
        margin-top: 1.5rem;
        border-radius: 20px !important;
    }
    
    .col-md-4 .card .card-body {
        padding: 1.25rem !important;
    }
    
    .col-md-4 .card .d-flex.justify-content-between {
        font-size: 0.85rem;
        margin-bottom: 0.75rem !important;
        padding: 0.5rem 0;
    }
    
    .col-md-4 .card h5 {
        font-size: 1rem !important;
        font-weight: 700;
    }
    
    .col-md-4 .card .btn {
        font-size: 0.85rem;
        padding: 12px 16px;
        border-radius: 12px;
        font-weight: 600;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .col-md-4 .card .small {
        font-size: 0.75rem;
    }
    
    /* Promo Code Mobile */
    .col-md-4 .card:last-child .d-flex {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .col-md-4 .card:last-child .form-control {
        margin-right: 0 !important;
        margin-bottom: 0 !important;
        height: 44px;
        border-radius: 12px;
        font-size: 0.85rem;
    }
    
    .col-md-4 .card:last-child .btn {
        width: 100%;
        height: 44px;
    }
    
    /* Continue Shopping Button */
    .btn-outline-primary {
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 20px;
        font-size: 0.85rem;
    }
}

/* Extra Small Mobile */
@media (max-width: 375px) {
    .cart-item {
        padding: 0.875rem !important;
    }
    
    .product-image {
        width: 50px;
        height: 50px;
    }
    
    .product-title {
        font-size: 0.85rem;
    }
    
    .current-price {
        font-size: 0.9rem;
    }
    
    .quantity-controls {
        height: 40px;
    }
    
    .quantity-btn {
        padding: 0 12px !important;
        font-size: 0.8rem;
    }
    
    .item-total {
        font-size: 0.95rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    var updating = false; // Flag to prevent multiple updates
    
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
    
    // Manual quantity input change
    $('.quantity-input').change(function() {
        var $input = $(this);
        var productId = $input.data('product-id');
        var quantity = parseInt($input.val()) || 1;  // Default to 1 if invalid
        var max = parseInt($input.attr('max')) || 9999;
        
        // Ensure quantity is within bounds
        quantity = Math.max(1, Math.min(quantity, max));
        $input.val(quantity);  // Update input with validated value
        
        // Update cart with the validated quantity
        updateCartQuantity(productId, quantity, $input);
    });
    
    // Remove item
    $('.remove-item').click(function() {
        var productId = $(this).data('product-id');
        
        if (confirm('Are you sure you want to remove this item from your cart?')) {
            removeFromCart(productId);
        }
    });
    
    function updateCartQuantity(productId, quantity, input) {
        if (updating) return;
        updating = true;
        console.log('Sending to server:', productId, quantity);
        $.ajax({
            url: '{{ route("cart.update") }}',
            method: 'PATCH',
            data: {
                product_id: productId,
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Server response:', response);
                if (response.success) {
                    // Update cart totals without reloading
                    updateCartTotals();
                    showToast('Cart updated successfully', 'success');
                } else {
                    // Revert the input value if update failed
                    var currentVal = parseInt(input.val());
                    input.val(currentVal - 1); // Simple revert
                    showToast(response.message || 'Error updating cart', 'error');
                }
                updating = false;
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', error);
                // Revert the input value if error occurred
                var currentVal = parseInt(input.val());
                input.val(currentVal - 1); // Simple revert
                showToast('Error occurred while updating cart.', 'error');
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
            var priceElement = $(this).closest('.cart-item').find('.current-price');
            var price = parseFloat(priceElement.text().replace('₹', '').replace(',', ''));
            
            subtotal += price * quantity;
            totalItems += quantity;
            
            // Update item total
            var itemTotal = price * quantity;
            $(this).closest('.cart-item').find('.text-end .fw-bold').text('₹' + itemTotal.toFixed(2));
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
    
    function showToast(message, type) {
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
            url: '{{ route("cart.remove") }}',
            method: 'DELETE',
            data: {
                product_id: productId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload(); // Reload to update cart
                } else {
                    showToast(response.message || 'Error removing item', 'error');
                }
            },
            error: function() {
                showToast('Error occurred while removing item.', 'error');
            }
        });
    }
});
</script>
@endpush
