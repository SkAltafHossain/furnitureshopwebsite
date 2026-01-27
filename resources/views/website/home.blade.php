@extends('website.layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Banner Slider -->
<section class="hero-slider">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @forelse($banners as $index => $banner)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" {{ $index == 0 ? 'class="active"' : '' }}></button>
            @empty
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            @endforelse
        </div>
        <div class="carousel-inner">
            @forelse($banners as $index => $banner)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                    @if($banner->image)
                        <img src="{{ asset('storage/' . $banner->image) }}" class="d-block w-100" alt="{{ $banner->title }}">
                    @else
                        <div class="d-block w-100 bg-primary text-white d-flex align-items-center justify-content-center" style="height: 400px;">
                            <div class="text-center">
                                <h2>{{ $banner->title ?? 'Welcome to Local Shop' }}</h2>
                                <p>{{ $banner->description ?? 'Your trusted local e-commerce store' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="carousel-item active">
                    <div class="d-block w-100 bg-primary text-white d-flex align-items-center justify-content-center" style="height: 400px;">
                        <div class="text-center">
                            <h2>Welcome to Local Shop</h2>
                            <p>Your trusted local e-commerce store</p>
                            <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">Shop Now</a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        @if($banners->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        @endif
    </div>
</section>

<!-- Featured Categories -->
<section class="py-5">
    <div class="container-fluid">
        <div class="text-center mb-4">
            <h2 class="h3">Shop by Category</h2>
            <p class="text-muted">Browse our wide range of products</p>
        </div>
        <div class="row">
            @forelse($categories as $category)
                <div class="col-md-4 col-lg-2 mb-4">
                    <a href="{{ route('category.show', $category->slug) }}" class="text-decoration-none">
                        <div class="card h-100 text-center category-card">
                            <div class="card-body">
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" class="img-fluid mb-2" alt="{{ $category->name }}" style="max-height: 60px;">
                                @else
                                    <i class="fas fa-box fa-2x text-primary mb-2"></i>
                                @endif
                                <h6 class="card-title text-dark">{{ $category->name }}</h6>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No categories available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Latest Products -->
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="text-center mb-4">
            <h2 class="h3">Latest Products</h2>
            <p class="text-muted">Check out our newest arrivals</p>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
            @forelse($latestProducts as $product)
                <div class="col mb-4">
                    <x-product-card 
                        :product="$product" 
                        :showCategory="true"
                        :wishlistActive="in_array($product->id, $wishlistProductIds ?? [])"
                    />
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No products available at the moment.</p>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-primary">View All Products</a>
        </div>
    </div>
</section>

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-5">
    <div class="container-fluid">
        <div class="text-center mb-4">
            <h2 class="h3">Featured Products</h2>
            <p class="text-muted">Handpicked products just for you</p>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
            @foreach($featuredProducts as $product)
                <div class="col mb-4">
                    <x-product-card 
                        :product="$product" 
                        :showCategory="true"
                        :wishlistActive="in_array($product->id, $wishlistProductIds ?? [])"
                    />
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Shop Highlights -->
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="highlight-item">
                    <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                    <h5>Free Shipping</h5>
                    <p class="text-muted">On orders above ₹500</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="highlight-item">
                    <i class="fas fa-undo fa-3x text-primary mb-3"></i>
                    <h5>Easy Returns</h5>
                    <p class="text-muted">7-day return policy</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="highlight-item">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h5>Secure Payment</h5>
                    <p class="text-muted">100% secure transactions</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="highlight-item">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h5>24/7 Support</h5>
                    <p class="text-muted">Dedicated customer support</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.add-to-cart').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var button = $(this);
        
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
                var toast = '<div class="position-fixed top-0 end-0 p-3" style="z-index: 11"><div class="toast show" role="alert"><div class="toast-body">' + response.message + '</div></div></div>';
                $('body').append(toast);
                setTimeout(function() { $('.toast').parent().remove(); }, 3000);
                
                // Disable button temporarily
                button.prop('disabled', true).html('<i class="fas fa-check"></i> Added');
                setTimeout(function() {
                    button.prop('disabled', false).html('<i class="fas fa-cart-plus"></i> Add to Cart');
                }, 2000);
            },
            error: function() {
                alert('Error occurred while adding product to cart.');
            }
        });
    });
});
</script>
@endpush
