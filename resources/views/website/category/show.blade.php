@extends('website.layouts.app')

@section('title', $category->name)

@section('content')
<div class="container-fluid py-5">
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
        <!-- Sidebar Filters -->
        <div class="col-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <!-- Categories -->
                    <div class="mb-4">
                        <h6>Categories</h6>
                        <div class="list-group">
                            <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action">
                                All Products
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ route('category.show', $cat->slug) }}" class="list-group-item list-group-item-action {{ $category && $category->id == $cat->id ? 'active' : '' }}">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="mb-4">
                        <h6>Price Range</h6>
                        <form method="GET" action="{{ route('category.show', $category->slug) }}">
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}">
                                </div>
                            </div>
                            @if(request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif
                            <button type="submit" class="btn btn-outline-primary btn-sm mt-2">Apply</button>
                        </form>
                    </div>
                    
                    <!-- Sort -->
                    <div class="mb-4">
                        <h6>Sort By</h6>
                        <form method="GET" action="{{ route('category.show', $category->slug) }}">
                            @if(request('min_price'))
                                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                            @endif
                            @if(request('max_price'))
                                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            @endif
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="">Default</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Content -->
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Products ({{ $products->total() }})</h4>
            </div>
            
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-4">
                @forelse($products as $product)
                    <div class="col mb-4">
                        <x-product-card 
                            :product="$product" 
                            :showCategory="false"
                            :wishlistActive="in_array($product->id, $wishlistProductIds ?? [])"
                        />
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
