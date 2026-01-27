@extends('website.layouts.app')

@section('title', $product->name)

@section('content')
<div class="container-fluid py-5">
    <div class="px-3 px-md-4 px-lg-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6">
            <div class="product-main-image mb-3">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded" alt="{{ $product->name }}">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                        <i class="fas fa-box fa-5x text-muted"></i>
                    </div>
                @endif
            </div>
            
            <!-- Thumbnail Gallery (if multiple images) -->
            @if(false) <!-- Add when you have multiple images -->
                <div class="product-thumbnails d-flex gap-2">
                    <img src="{{ asset('storage/' . $product->image) }}" class="product-thumbnail border border-primary rounded" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" alt="Thumbnail 1">
                </div>
            @endif
        </div>
        
        <!-- Product Details -->
        <div class="col-md-6">
            <h1>{{ $product->name }}</h1>
            
            <!-- Price -->
            <div class="mb-3">
                <span class="h3 text-primary">₹{{ number_format($product->price, 2) }}</span>
                @if($product->discount_price)
                    <span class="text-muted text-decoration-line-through ms-2">₹{{ number_format($product->discount_price, 2) }}</span>
                    <span class="badge bg-danger ms-2">Save ₹{{ number_format($product->discount_price - $product->price, 2) }}</span>
                @endif
            </div>
            
            <!-- Stock Status -->
            <div class="mb-3">
                <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                    {{ $product->stock > 0 ? 'In Stock (' . $product->stock . ' available)' : 'Out of Stock' }}
                </span>
            </div>
            
            <!-- Description -->
            <div class="mb-4">
                <h5>Description</h5>
                <p class="text-muted">{{ $product->description }}</p>
            </div>
            
            <!-- Category -->
            @if($product->category)
                <div class="mb-4">
                    <h5>Category</h5>
                    <a href="{{ route('category.show', $product->category->slug) }}" class="btn btn-outline-primary btn-sm">
                        {{ $product->category->name }}
                    </a>
                </div>
            @endif
            
            <!-- Add to Cart Form -->
            <div class="mb-4">
                <h5>Quantity</h5>
                <form id="add-to-cart-form" class="d-flex align-items-center gap-3">
                    <div class="input-group" style="width: 150px;">
                        <button class="btn btn-outline-secondary quantity-decrease" type="button">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="{{ $product->stock }}">
                        <button class="btn btn-outline-secondary quantity-increase" type="button">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </form>
            </div>
            
            <!-- Product Features -->
            <div class="mb-4">
                <h5>Features</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i> Quality Assured</li>
                    <li><i class="fas fa-check text-success me-2"></i> Fast Delivery</li>
                    <li><i class="fas fa-check text-success me-2"></i> Easy Returns</li>
                    <li><i class="fas fa-check text-success me-2"></i> Secure Payment</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Product Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                        Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">
                        Specifications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                        Reviews
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="productTabsContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="p-3">
                        <p>{{ $product->description }}</p>
                    </div>
                </div>
                <div class="tab-pane fade" id="specifications" role="tabpanel">
                    <div class="p-3">
                        <table class="table table-striped">
                            <tr>
                                <th>Product Name</th>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <th>SKU</th>
                                <td>{{ $product->sku ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Stock</th>
                                <td>{{ $product->stock }} units</td>
                            </tr>
                            <tr>
                                <th>Weight</th>
                                <td>{{ $product->weight ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="p-3">
                        <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Related Products</h3>
                <div class="row">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100 product-card">
                                @if($relatedProduct->image)
                                    <img src="{{ asset('storage/' . $relatedProduct->image) }}" class="card-img-top" alt="{{ $relatedProduct->name }}" style="height: 150px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="fas fa-box fa-2x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">{{ $relatedProduct->name }}</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-primary">₹{{ number_format($relatedProduct->price, 2) }}</span>
                                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="btn btn-primary btn-sm">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add to cart form
    $('#add-to-cart-form').submit(function(e) {
        e.preventDefault();
        
        var quantity = $('input[name="quantity"]').val();
        var button = $(this).find('button[type="submit"]');
        
        if (button.prop('disabled')) {
            return;
        }
        
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                product_id: {{ $product->id }},
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Update cart count
                $('.badge').text(response.cart_count);
                
                // Show success message
                showToast(response.message, 'success');
                
                // Disable button temporarily
                button.prop('disabled', true).html('<i class="fas fa-check"></i> Added to Cart');
                setTimeout(function() {
                    button.prop('disabled', false).html('<i class="fas fa-cart-plus"></i> Add to Cart');
                }, 2000);
            },
            error: function() {
                showToast('Error occurred while adding product to cart.', 'error');
            }
        });
    });
    
    // Quantity controls
    $('.quantity-decrease').click(function() {
        var input = $(this).siblings('input[type="number"]');
        var currentValue = parseInt(input.val());
        if (currentValue > 1) {
            input.val(currentValue - 1);
        }
    });
    
    $('.quantity-increase').click(function() {
        var input = $(this).siblings('input[type="number"]');
        var currentValue = parseInt(input.val());
        var max = parseInt(input.attr('max'));
        if (!max || currentValue < max) {
            input.val(currentValue + 1);
        }
    });
});
</script>
@endpush
