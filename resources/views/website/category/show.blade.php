@extends('website.layouts.app')

@section('title', $category->name)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>
    
    <!-- Category Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" class="img-fluid mb-3" alt="{{ $category->name }}" style="max-height: 200px;">
                    @else
                        <i class="fas fa-box fa-4x text-primary mb-3"></i>
                    @endif
                    <h1>{{ $category->name }}</h1>
                    <p class="text-muted">Browse our selection of {{ $category->name }} products</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Products ({{ $products->total() }})</h4>
                
                <!-- Sort Options -->
                <div class="d-flex align-items-center">
                    <label class="me-2">Sort by:</label>
                    <select class="form-select" onchange="window.location.href='?sort=' + this.value">
                        <option value="">Default</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                @forelse($products as $product)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 product-card">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-box fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text text-muted small">{{ Str::limit($product->description, 100) }}</p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="h5 text-primary mb-0">₹{{ number_format($product->price, 2) }}</span>
                                        @if($product->discount_price)
                                            <span class="text-muted text-decoration-line-through">₹{{ number_format($product->discount_price, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                        </span>
                                        <div>
                                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-primary btn-sm me-1">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <button class="btn btn-primary btn-sm add-to-cart" data-product-id="{{ $product->id }}" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-cart-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5>No products found in this category</h5>
                        <p class="text-muted">Check back later or browse other categories.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">Browse All Products</a>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($products->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Back to Products -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to All Products
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add to cart functionality
    $('.add-to-cart').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var button = $(this);
        
        if (button.prop('disabled')) {
            return;
        }
        
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: 1,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Update cart count
                $('.badge').text(response.cart_count);
                
                // Show success message
                showToast(response.message, 'success');
                
                // Disable button temporarily
                button.prop('disabled', true).html('<i class="fas fa-check"></i> Added');
                setTimeout(function() {
                    button.prop('disabled', false).html('<i class="fas fa-cart-plus"></i> Add');
                }, 2000);
            },
            error: function() {
                showToast('Error occurred while adding product to cart.', 'error');
            }
        });
    });
});
</script>
@endpush
