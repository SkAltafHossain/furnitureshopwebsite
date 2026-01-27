@extends('website.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container-fluid py-5">
    <h1 class="mb-4">Shopping Cart</h1>
    
    @forelse($cart as $productId => $item)
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
                            <div class="cart-item d-flex align-items-center mb-3 pb-3 border-bottom">
                                <!-- Product Image -->
                                <div class="me-3">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 80px; height: 80px; object-fit: cover;" class="rounded">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 80px; height: 80px;">
                                            <i class="fas fa-box fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Product Details -->
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
                                            {{ $product->name }}
                                        </a>
                                    </h6>
                                    <p class="text-muted mb-1 small">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                    <div class="d-flex align-items-center">
                                        <span class="text-primary fw-bold">₹{{ number_format($product->price, 2) }}</span>
                                        @if($product->discount_price)
                                            <span class="text-muted text-decoration-line-through ms-2 small">₹{{ number_format($product->discount_price, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Quantity Controls -->
                                <div class="me-3">
                                    <div class="input-group" style="width: 120px;">
                                        <button class="btn btn-outline-secondary quantity-decrease" type="button" data-product-id="{{ $productId }}">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center quantity-input" value="{{ $item['quantity'] }}" min="1" max="{{ $product->stock }}" data-product-id="{{ $productId }}">
                                        <button class="btn btn-outline-secondary quantity-increase" type="button" data-product-id="{{ $productId }}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Item Total & Remove -->
                                <div class="text-end">
                                    <div class="fw-bold mb-2">₹{{ number_format($product->price * $item['quantity'], 2) }}</div>
                                    <button class="btn btn-sm btn-outline-danger remove-item" data-product-id="{{ $productId }}">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
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
                        
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 mb-2">
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
    @empty
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted">Looks like you haven't added any products to your cart yet.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Start Shopping
            </a>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update quantity
    $('.quantity-decrease, .quantity-increase').click(function() {
        var productId = $(this).data('product-id');
        var input = $('.quantity-input[data-product-id="' + productId + '"]');
        var currentValue = parseInt(input.val());
        var max = parseInt(input.attr('max'));
        
        if ($(this).hasClass('quantity-decrease')) {
            if (currentValue > 1) {
                input.val(currentValue - 1);
                updateCartQuantity(productId, currentValue - 1);
            }
        } else {
            if (!max || currentValue < max) {
                input.val(currentValue + 1);
                updateCartQuantity(productId, currentValue + 1);
            }
        }
    });
    
    // Manual quantity input change
    $('.quantity-input').change(function() {
        var productId = $(this).data('product-id');
        var quantity = parseInt($(this).val());
        var max = parseInt($(this).attr('max'));
        
        if (quantity >= 1 && (!max || quantity <= max)) {
            updateCartQuantity(productId, quantity);
        } else {
            // Reset to valid quantity
            $(this).val(Math.max(1, Math.min(quantity, max || 1)));
        }
    });
    
    // Remove item
    $('.remove-item').click(function() {
        var productId = $(this).data('product-id');
        
        if (confirm('Are you sure you want to remove this item from your cart?')) {
            removeFromCart(productId);
        }
    });
    
    function updateCartQuantity(productId, quantity) {
        $.ajax({
            url: '{{ route("cart.update") }}',
            method: 'PATCH',
            data: {
                product_id: productId,
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload(); // Reload to update totals
                } else {
                    showToast(response.message || 'Error updating cart', 'error');
                }
            },
            error: function() {
                showToast('Error occurred while updating cart.', 'error');
            }
        });
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
