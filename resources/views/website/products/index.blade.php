@extends('website.layouts.app')

@section('title', 'Products')

@section('content')
<div class="container-fluid py-5">
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
                            <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action {{ !$category ? 'active' : '' }}">
                                All Products
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="list-group-item list-group-item-action {{ $category && $category->id == $cat->id ? 'active' : '' }}">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="mb-4">
                        <h6>Price Range</h6>
                        <form method="GET" action="{{ route('products.index') }}">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-outline-primary btn-sm mt-2">Apply</button>
                        </form>
                    </div>
                    
                    <!-- Sort -->
                    <div class="mb-4">
                        <h6>Sort By</h6>
                        <form method="GET" action="{{ route('products.index') }}">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
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
        
        <!-- Products Grid -->
        <div class="col-md-10">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Products</li>
                    @if($category)
                        <li class="breadcrumb-item active">{{ $category->name }}</li>
                    @endif
                </ol>
            </nav>
            
            <!-- Results Count -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    @if($category)
                        {{ $category->name }}
                    @else
                        All Products
                    @endif
                    <small class="text-muted">({{ $products->total() }} products)</small>
                </h4>
                
                <!-- View Toggle -->
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary active" id="grid-view">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="list-view">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
            
            <!-- Products -->
            <div id="products-container" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-4">
                @forelse($products as $product)
                    <div class="col mb-4 product-item">
                        <x-product-card :product="$product" :showCategory="false" />
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5>No products found</h5>
                        <p class="text-muted">Try adjusting your filters or check back later.</p>
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
    
    // View toggle
    $('#grid-view').click(function() {
        $('#products-container').removeClass('list-view').addClass('row');
        $(this).addClass('active');
        $('#list-view').removeClass('active');
    });
    
    $('#list-view').click(function() {
        $('#products-container').removeClass('row').addClass('list-view');
        $(this).addClass('active');
        $('#grid-view').removeClass('active');
    });
});
</script>

<style>
.list-view .product-item {
    width: 100% !important;
}

.list-view .product-card {
    display: flex;
    flex-direction: row;
    height: auto !important;
}

.list-view .product-card .card-img-top {
    width: 200px;
    height: 150px !important;
    border-radius: 0.375rem 0 0 0.375rem;
}

.list-view .product-card .card-body {
    flex: 1;
    display: flex;
    flex-direction: row !important;
    align-items: center;
    justify-content: space-between;
}

.list-view .product-card .card-title {
    margin-bottom: 0.5rem;
}

.list-view .product-card .card-text {
    margin-bottom: 1rem;
    flex: 1;
}
</style>
@endpush
