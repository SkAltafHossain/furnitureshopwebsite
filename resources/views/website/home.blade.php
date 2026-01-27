@extends('website.layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Banner Slider -->
<section class="hero-slider">
    <div id="heroCarousel" class="carousel slide">
        <div class="carousel-indicators">
            @forelse($banners as $index => $banner)
                <span class="dot {{ $index == 0 ? 'active' : '' }}" data-slide="{{ $index }}"></span>
            @empty
                <span class="dot active" data-slide="0"></span>
            @endforelse
        </div>
        <div class="carousel-inner">
            @forelse($banners as $index => $banner)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                    @if($banner->image)
                        <img src="{{ asset('storage/' . $banner->image) }}" class="d-block w-100" alt="{{ $banner->title }}">
                    @else
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTkyMCIgaGVpZ2h0PSI1MDAiIHZpZXdCb3g9IjAgMCAxOTIwIDUwMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjE5MjAiIGhlaWdodD0iNTAwIiBmaWxsPSIjMGRkNmZkIi8+Cjx0ZXh0IHg9Ijk2MCIgeT0iMjUwIiBmaWxsPSIjZmZmZmZmIiBmb250LXNpemU9IjQ4IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj57eyBiYW5uZXItPnRpdGxlIH19PC90ZXh0Pgo8L3N2Zz4K" class="d-block w-100" alt="{{ $banner->title }}">
                        <div class="carousel-caption d-none d-md-block">
                            <h2>{{ $banner->title ?? 'Welcome to Local Shop' }}</h2>
                            <p>{{ $banner->description ?? 'Your trusted local e-commerce store' }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="carousel-item active">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTkyMCIgaGVpZ2h0PSI1MDAiIHZpZXdCb3g9IjAgMCAxOTIwIDUwMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjE5MjAiIGhlaWdodD0iNTAwIiBmaWxsPSIjMGRkNmZkIi8+Cjx0ZXh0IHg9Ijk2MCIgeT0iMjUwIiBmaWxsPSIjZmZmZmZmIiBmb250LXNpemU9IjQ4IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5XZWxjb21lIHRvIExvY2FsIFNob3A8L3RleHQ+Cjwvc3ZnPgo=" class="d-block w-100" alt="Welcome to Local Shop">
                    <div class="carousel-caption d-none d-md-block">
                        <h2>Welcome to Local Shop</h2>
                        <p>Your trusted local e-commerce store</p>
                        <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">Shop Now</a>
                    </div>
                </div>
            @endforelse
        </div>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? 'loaded' : 'NOT LOADED');
    
    var carouselElement = document.getElementById('heroCarousel');
    console.log('Carousel element found:', !!carouselElement);
    
    if (carouselElement && typeof bootstrap !== 'undefined') {
        try {
            // Get carousel items
            var items = carouselElement.querySelectorAll('.carousel-item');
            var currentIndex = 0;
            
            console.log('Found carousel items:', items.length);
            
            // Manual slide function
            function goToSlide(index) {
                // Remove active from all items
                items.forEach(function(item) {
                    item.classList.remove('active');
                });
                
                // Remove active from all dots
                var dots = carouselElement.querySelectorAll('.dot');
                dots.forEach(function(dot) {
                    dot.classList.remove('active');
                });
                
                // Add active to target item
                if (items[index]) {
                    items[index].classList.add('active');
                    currentIndex = index;
                    console.log('Slid to item:', index);
                    
                    // Add active to corresponding dot
                    if (dots[index]) {
                        dots[index].classList.add('active');
                    }
                }
            }
            
            // Auto-slide function
            function nextSlide() {
                currentIndex = (currentIndex + 1) % items.length;
                goToSlide(currentIndex);
            }
            
            // Start auto-slide
            setInterval(nextSlide, 3000);
            console.log('Auto-slide started with manual sliding');
            
            // Manual controls
            var dots = carouselElement.querySelectorAll('.dot');
            
            // Dot indicator clicks
            dots.forEach(function(dot, index) {
                dot.addEventListener('click', function(e) {
                    e.preventDefault();
                    goToSlide(index);
                });
            });
            
        } catch (error) {
            console.error('Error with carousel:', error);
        }
    }
    
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
                $('.badge').text(response.cart_count);
                
                var toast = '<div class="position-fixed top-0 end-0 p-3" style="z-index: 11"><div class="toast show" role="alert"><div class="toast-body">' + response.message + '</div></div></div>';
                $('body').append(toast);
                setTimeout(function() { $('.toast').parent().remove(); }, 3000);
                
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
