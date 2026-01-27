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
                                    <div class="quantity-controls d-flex align-items-center border rounded" style="width: 140px;">
                                        <button class="btn btn-link quantity-decrease p-2 text-decoration-none" type="button" data-product-id="{{ $productId }}">
                                            <i class="fas fa-minus text-muted"></i>
                                        </button>
                                        <input type="number" class="form-control border-0 text-center quantity-input fw-bold" value="{{ $item['quantity'] }}" min="1" max="{{ $product->stock }}" data-product-id="{{ $productId }}" style="background: transparent; cursor: default;" readonly>
                                        <button class="btn btn-link quantity-increase p-2 text-decoration-none" type="button" data-product-id="{{ $productId }}">
                                            <i class="fas fa-plus text-muted"></i>
                                        </button>
                                    </div>
                                    @if($product->stock <= 5)
                                        <small class="text-danger">Only {{ $product->stock }} left</small>
                                    @endif
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
.quantity-controls {
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.quantity-controls:hover {
    background: #e9ecef;
    border-color: #dee2e6;
}

.quantity-controls .quantity-input {
    font-size: 16px;
    color: #495057;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    pointer-events: none;
}

.quantity-controls .quantity-input:focus {
    outline: none;
    box-shadow: none;
}

.quantity-controls .btn-link:hover {
    background: rgba(0,0,0,0.05);
    border-radius: 4px;
}

.quantity-controls .btn-link:hover i {
    color: #007bff !important;
}

.quantity-controls .btn-link:active {
    transform: scale(0.95);
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
            var priceElement = $(this).closest('.cart-item').find('.text-primary.fw-bold');
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
