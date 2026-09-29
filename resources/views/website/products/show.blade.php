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
        <!-- Left Side: Image and Actions -->
        <div class="col-lg-5 col-md-6">
            <div class="product-left-section">
                <!-- Product Gallery -->
                <div class="product-gallery mb-4">
                    <!-- Main Image -->
                    <div class="product-main-image position-relative mb-3">
                        @if($product->image)
                            <div class="image-container">
                                <img id="main-product-image" 
                                     src="{{ asset('storage/' . $product->image) }}" 
                                     class="img-fluid rounded main-product-img" 
                                     alt="{{ $product->name }}"
                                     data-zoom-image="{{ asset('storage/' . $product->image) }}">
                            </div>
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded placeholder-image" style="height: 400px;">
                                <div class="text-center">
                                    <i class="fas fa-box fa-4x text-muted mb-2"></i>
                                    <p class="text-muted small">No image available</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Thumbnail Gallery -->
                    @if(false) <!-- Enable when you have multiple images -->
                        <div class="product-thumbnails d-flex gap-2 flex-wrap">
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 class="product-thumbnail border border-2 border-primary rounded" 
                                 style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;" 
                                 alt="Thumbnail 1"
                                 onclick="changeMainImage(this)">
                        </div>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="product-actions">
                    <div class="action-section p-3 border rounded bg-light">
                        <form id="add-to-cart-form">
                            <div class="row align-items-center g-2">
                                <div class="col-auto">
                                    <label class="form-label fw-semibold mb-0">Qty:</label>
                                </div>
                                <div class="col-auto">
                                    <div class="quantity-selector">
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary quantity-decrease" type="button">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="{{ $product->stock }}" style="width: 60px;">
                                            <button class="btn btn-outline-secondary quantity-increase" type="button">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-primary w-100 add-to-cart-btn" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                    </button>
                                </div>
                                @if($product->stock > 0)
                                    <div class="col">
                                        <button type="button" class="btn btn-success w-100">
                                            <i class="fas fa-bolt me-1"></i> Buy Now
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Product Details -->
        <div class="col-lg-7 col-md-6">
            <div class="product-right-section">
                <!-- Product Title and Rating -->
                <div class="product-header mb-3">
                    <h1 class="product-title mb-2">{{ $product->name }}</h1>
                    <div class="product-meta d-flex align-items-center gap-3">
                        <div class="rating">
                            <div class="stars text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $product->average_rating ? '' : '-o' }}"></i>
                                @endfor
                            </div>
                            <span class="text-muted small ms-1">({{ $product->average_rating }}) · {{ $product->total_reviews }} Reviews</span>
                        </div>
                        <div class="sku text-muted small">
                            SKU: {{ $product->sku ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                
                <!-- Price Section -->
                <div class="price-section mb-4 p-3 bg-light rounded">
                    <div class="d-flex align-items-center gap-3">
                        @if($product->discount_price)
                            <div>
                                <span class="current-price h2 text-primary mb-0">₹{{ number_format($product->discount_price, 2) }}</span>
                                <span class="original-price text-muted text-decoration-line-through h5 ms-2">₹{{ number_format($product->price, 2) }}</span>
                                @php
                                    $discountPercent = round((($product->price - $product->discount_price) / $product->price) * 100);
                                @endphp
                                <span class="discount-badge bg-success text-white px-2 py-1 rounded ms-2">{{ $discountPercent }}% off</span>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-tag"></i> You save ₹{{ number_format($product->price - $product->discount_price, 2) }}
                            </div>
                        @else
                            <span class="current-price h2 text-primary mb-0">₹{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    <div class="text-success small mt-2">
                        <i class="fas fa-check-circle"></i> Inclusive of all taxes
                    </div>
                </div>
                
                <!-- Stock Status & Delivery -->
                <div class="stock-delivery-section mb-4">
                    <div class="stock-info">
                        <span class="stock-badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }} px-3 py-2 rounded">
                            @if($product->stock > 0)
                                <i class="fas fa-check-circle me-1"></i> In Stock
                            @else
                                <i class="fas fa-times-circle me-1"></i> Out of Stock
                            @endif
                        </span>
                        @if($product->stock > 0)
                            <div class="delivery-info text-muted small mt-2">
                                <i class="fas fa-truck me-1"></i> Free delivery in 3-5 business days
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Key Features -->
                <div class="key-features mb-4">
                    <h6 class="section-title mb-3">Key Features</h6>
                    <div class="features-list">
                        <div class="feature-item d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>100% Genuine Products</span>
                        </div>
                        <div class="feature-item d-flex align-items-center mb-2">
                            <i class="fas fa-shipping-fast text-success me-2"></i>
                            <span>Fast & Free Delivery</span>
                        </div>
                        <div class="feature-item d-flex align-items-center mb-2">
                            <i class="fas fa-undo text-success me-2"></i>
                            <span>Easy 7-Day Returns</span>
                        </div>
                        <div class="feature-item d-flex align-items-center mb-2">
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            <span>Secure Payment</span>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="description-section mb-4">
                    <h6 class="section-title mb-3">Description</h6>
                    <div class="description-content">
                        <p class="text-muted mb-0">{!! $product->description !!}</p>
                    </div>
                </div>
                
                <!-- Category -->
                @if($product->category)
                    <div class="category-section mb-4">
                        <h6 class="section-title mb-3">Category</h6>
                        <a href="{{ route('category.show', $product->category->slug) }}" 
                           class="category-link btn btn-outline-primary btn-sm rounded-pill">
                            <i class="fas fa-tag me-1"></i> {{ $product->category->name }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Product Information Tabs -->
    <div class="product-tabs-section mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="tabs-container">
                        <ul class="nav nav-tabs custom-tabs" id="productTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                                    <i class="fas fa-info-circle me-2"></i>Description
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">
                                    <i class="fas fa-list me-2"></i>Specifications
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                                    <i class="fas fa-star me-2"></i>Reviews ({{ $product->total_reviews }})
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content custom-tab-content" id="productTabsContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel">
                                <div class="p-4">
                                    <h5 class="mb-3">Product Description</h5>
                                    <div class="description-text" style="color: #333 !important; font-size: 1rem !important; line-height: 1.6 !important; display: block !important; visibility: visible !important;">
                                        {!! $product->description !!}
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="specifications" role="tabpanel">
                                <div class="p-4">
                                    <h5 class="mb-3">Product Specifications</h5>
                                    <div class="specifications-table">
                                        <table class="table table-striped table-hover">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">Product Name</th>
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
                                                    <th>Stock Availability</th>
                                                    <td>
                                                        <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $product->stock > 0 ? $product->stock . ' units available' : 'Out of Stock' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Weight</th>
                                                    <td>{{ $product->weight ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Dimensions</th>
                                                    <td>{{ $product->dimensions ?? 'N/A' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <div class="p-4">
                                    <div class="reviews-summary mb-4">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <div class="h1 text-warning mb-2">{{ $product->average_rating }}</div>
                                                    <div class="stars text-warning mb-2">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star{{ $i <= $product->average_rating ? '' : '-o' }}"></i>
                                                        @endfor
                                                    </div>
                                                    <p class="text-muted">Based on {{ $product->total_reviews }} reviews</p>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="rating-bars">
                                                    @for($rating = 5; $rating >= 1; $rating--)
                                                        @php
                                                            $ratingCount = $product->reviews()->where('rating', $rating)->count();
                                                            $ratingPercent = $product->total_reviews > 0 ? ($ratingCount / $product->total_reviews) * 100 : 0;
                                                        @endphp
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="small me-2">{{ $rating }} star</span>
                                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                                <div class="progress-bar bg-warning" style="width: {{ $ratingPercent }}%"></div>
                                                            </div>
                                                            <span class="small ms-2">{{ $ratingCount }}</span>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if(auth()->check())
                                        <div class="write-review-section mb-4">
                                            @if($canReview)
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                                    <i class="fas fa-pen me-2"></i>Write a Review
                                                </button>
                                            @else
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    You can only review products after you have ordered and confirmed delivery.
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="write-review-section mb-4">
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                                                <i class="fas fa-pen me-2"></i>Login to Write a Review
                                            </button>
                                        </div>
                                    @endif
                                    
                                    <div class="reviews-list">
                                        @if($reviews->count() > 0)
                                            @foreach($reviews as $review)
                                                <div class="review-item border-bottom pb-3 mb-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                                <strong>{{ $review->user->name }}</strong>
                                                                <div class="stars text-warning small">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                                                        </div>
                                                        @if(auth()->check() && $review->canBeDeletedBy(auth()->id()))
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-review" 
                                                                    data-review-id="{{ $review->id }}" 
                                                                    data-review-url="{{ route('reviews.destroy', $review->id) }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @if($review->comment)
                                                        <p class="mb-0">{{ $review->comment }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                            
                                            @if($reviews->hasPages())
                                                <div class="pagination-container mt-3">
                                                    {{ $reviews->links('vendor.pagination.custom') }}
                                                </div>
                                            @endif
                                        @else
                                            <p class="text-muted text-center">No reviews yet. Be the first to review this product!</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="related-products-section mt-5">
            <div class="container">
                <div class="text-center mb-4">
                    <h3 class="section-title">Related Products</h3>
                    <p class="text-muted">Customers who viewed this item also viewed</p>
                </div>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col mb-4">
                            <x-product-card 
                                :product="$relatedProduct" 
                                :showCategory="false"
                                :wishlistActive="in_array($relatedProduct->id, $wishlistProductIds ?? [])"
                            />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    </div>
</div>

<!-- Review Modal -->
@if(auth()->check() && $canReview)
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom pb-3 px-4 pt-4">
                <h5 class="modal-title mb-0">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('reviews.store', $product->slug) }}" method="POST">
                @csrf
                <div class="modal-body py-4 px-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-3">Rating</label>
                        <div class="rating-input d-flex gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="d-none" required>
                                <label for="star{{ $i }}" class="star-rating me-1" style="cursor: pointer; font-size: 1.8rem;">
                                    <i class="far fa-star"></i>
                                </label>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="comment" class="form-label fw-semibold mb-2">Comment (Optional)</label>
                        <textarea name="comment" id="comment" class="form-control" rows="4" 
                                  placeholder="Share your experience with this product..." maxlength="1000"></textarea>
                        <small class="text-muted d-block mt-1">Maximum 1000 characters</small>
                    </div>
                </div>
                <div class="modal-footer border-top pt-3 px-4 pb-4">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Check if user is logged in
    function isLoggedIn() {
        return @if(auth()->check()) true @else false @endif;
    }
    
    // Add to cart button functionality
    $('.add-to-cart-btn').click(function() {
        console.log('Add to Cart button clicked');
        
        var button = $(this);
        
        if (button.prop('disabled')) {
            console.log('Add to Cart button is disabled');
            return;
        }
        
        // Check if user is logged in
        console.log('Checking if user is logged in...');
        var userLoggedIn = isLoggedIn();
        console.log('Add to Cart - User logged in:', userLoggedIn);
        
        if (!userLoggedIn) {
            console.log('Add to Cart - User not logged in, showing login modal');
            $('#loginModal').modal('show');
            return;
        }
        
        // Get quantity
        var quantity = $('input[name="quantity"]').val();
        
        // Debug: Log the URL being used
        var cartAddUrl = '{{ route("cart.add") }}';
        console.log('Add to Cart URL:', cartAddUrl);
        console.log('Product ID: {{ $product->id }}');
        console.log('Quantity:', quantity);
        
        // Show loading state
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Adding...');
        
        $.ajax({
            url: cartAddUrl,
            method: 'POST',
            data: {
                product_id: {{ $product->id }},
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Add to Cart success:', response);
                
                // Reset button
                button.prop('disabled', false).html('<i class="fas fa-cart-plus me-1"></i> Add to Cart');
                
                // Update cart count if available
                if (response.cart_count) {
                    $('.badge').text(response.cart_count);
                }
                
                // Show success message
                if (typeof showToast === 'function') {
                    showToast(response.message || 'Product added to cart successfully!', 'success');
                } else {
                    alert('Product added to cart successfully!');
                }
            },
            error: function(xhr) {
                console.log('Add to Cart error:', xhr);
                
                // Reset button
                button.prop('disabled', false).html('<i class="fas fa-cart-plus me-1"></i> Add to Cart');
                
                var errorMessage = 'Error occurred while adding product to cart.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (typeof showToast === 'function') {
                    showToast(errorMessage, 'error');
                } else {
                    alert(errorMessage);
                }
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
    
    // Image zoom functionality
    $('.zoom-btn').click(function() {
        var mainImage = $('#main-product-image');
        var zoomImage = mainImage.attr('data-zoom-image');
        
        // Create zoom modal
        var zoomModal = `
            <div class="modal fade" id="imageZoomModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content bg-transparent border-0">
                        <div class="modal-body p-0">
                            <button type="button" class="btn-close btn-close-white position-absolute" style="top: 20px; right: 20px; z-index: 1060;" data-bs-dismiss="modal"></button>
                            <img src="${zoomImage}" class="img-fluid" alt="Product Zoom" style="max-height: 90vh;">
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        $('#imageZoomModal').remove();
        
        // Add modal to body and show
        $('body').append(zoomModal);
        $('#imageZoomModal').modal('show');
    });
    
    // Change main image on thumbnail click
    window.changeMainImage = function(thumbnail) {
        var newSrc = $(thumbnail).attr('src');
        var newZoomSrc = newSrc; // Use same image for zoom
        
        $('#main-product-image').fadeOut(200, function() {
            $(this).attr('src', newSrc).attr('data-zoom-image', newZoomSrc).fadeIn(200);
        });
        
        // Update thumbnail active state
        $('.product-thumbnail').removeClass('border-primary').addClass('border-secondary');
        $(thumbnail).removeClass('border-secondary').addClass('border-primary');
    };
    
    // Toast notification function
    function showToast(message, type) {
        var toastClass = type === 'success' ? 'bg-success' : 'bg-danger';
        var toast = `
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
                <div class="toast show" role="alert">
                    <div class="toast-header ${toastClass} text-white">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                        <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(toast);
        setTimeout(function() {
            $('.toast').parent().fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    // Smooth scroll to tabs
    $('.custom-tabs .nav-link').click(function() {
        $('html, body').animate({
            scrollTop: $('.product-tabs-section').offset().top - 100
        }, 500);
    });
    
    // Add hover effect to product image
    $('.main-product-img').hover(
        function() {
            $(this).css('transform', 'scale(1.02)');
        },
        function() {
            $(this).css('transform', 'scale(1)');
        }
    );
    
    // Buy Now button functionality
    $('.btn-success').click(function() {
        console.log('Buy Now button clicked');
        // Check if user is logged in
        var userLoggedIn = isLoggedIn();
        console.log('Buy Now - User logged in:', userLoggedIn);

        if (!userLoggedIn) {
            console.log('Buy Now - User not logged in, showing login modal');
            $('#loginModal').modal('show');
            return;
        }

        // Get quantity
        var quantity = $('input[name="quantity"]').val();

        // Debug: Log the URL being used
        var buyNowUrl = '{{ route("checkout.buy-now") }}';
        console.log('Buy Now URL:', buyNowUrl);

        $.ajax({
            url: buyNowUrl,
            method: 'POST',
            data: {
                product_id: {{ $product->id }},
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Buy Now success:', response);

                // Redirect to checkout
                if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                }
            },
            error: function(xhr) {
                console.log('Buy Now error:', xhr);

                var errorMessage = 'Error occurred while processing your request.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (typeof showToast === 'function') {
                    showToast(errorMessage, 'error');
                } else {
                    alert(errorMessage);
                }
            }
        });
    });
    
    // Enhanced star rating functionality
    function initRating() {
        const ratingInput = document.querySelector('#reviewModal .rating-input');
        if (!ratingInput) return;
        
        const stars = Array.from(ratingInput.querySelectorAll('.star-rating'));
        const radioInputs = Array.from(ratingInput.querySelectorAll('input[type="radio"]'));
        
        // Initialize all stars to empty
        stars.forEach(star => {
            star.classList.remove('text-warning');
            const icon = star.querySelector('i');
            if (icon) {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        });
        
        // Handle hover and click for each star
        stars.forEach((star, index) => {
            // Hover in
            star.addEventListener('mouseenter', function() {
                const hoverIndex = stars.indexOf(star);
                
                // Highlight all stars up to the hovered one
                stars.forEach((s, i) => {
                    const icon = s.querySelector('i');
                    if (i <= hoverIndex) {
                        s.classList.add('text-warning');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    } else {
                        s.classList.remove('text-warning');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    }
                });
            });
            
            // Hover out
            star.addEventListener('mouseleave', function() {
                const checked = ratingInput.querySelector('input[type="radio"]:checked');
                if (!checked) {
                    // If nothing is selected, reset all stars
                    stars.forEach(s => {
                        s.classList.remove('text-warning');
                        const icon = s.querySelector('i');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    });
                } else {
                    // Show the selected rating
                    const value = parseInt(checked.value);
                    
                    stars.forEach((s, i) => {
                        const icon = s.querySelector('i');
                        if (i < value) {
                            s.classList.add('text-warning');
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                        } else {
                            s.classList.remove('text-warning');
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                        }
                    });
                }
            });
            
            // Click
            star.addEventListener('click', function() {
                const radio = this.previousElementSibling;
                if (radio && radio.type === 'radio') {
                    radio.checked = true;
                    // Trigger change event to update the display
                    const event = new Event('change');
                    radio.dispatchEvent(event);
                }
            });
            
            // Handle radio button change
            if (radioInputs[index]) {
                radioInputs[index].addEventListener('change', function() {
                    const value = parseInt(this.value);
                    
                    stars.forEach((s, i) => {
                        const icon = s.querySelector('i');
                        if (i < value) {
                            s.classList.add('text-warning');
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                        } else {
                            s.classList.remove('text-warning');
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                        }
                    });
                });
            }
        });
    }
    
    // Initialize rating when review modal is shown
    $('#reviewModal').on('show.bs.modal', function () {
        // Small delay to ensure the modal is fully shown
        setTimeout(initRating, 100);
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        const ratingInput = this.querySelector('input[name="rating"]:checked');
        if (!ratingInput) {
            e.preventDefault();
            showToast('Please select a rating', 'error');
            return false;
        }
    });
    
    // Initialize star rating display
    $('.star-rating').each(function(index) {
        $(this).find('i').removeClass('fas').addClass('far');
    });
    
    // Function to update review count and rating display
    function updateReviewStats() {
        // Get the current product ID from the URL
        const productId = '{{ $product->id }}';
        
        // Make an AJAX call to get updated review stats
        $.get('{{ route("products.show", $product->slug) }}', function(html) {
            // Create a temporary div to parse the HTML response
            const tempDiv = $('<div>').html(html);
            
            // Update the review count in the product header
            const newReviewCount = tempDiv.find('#reviews-tab').text().match(/\(([^)]+)\)/)[1];
            $('.product-header .text-muted.small').html(
                $('.product-header .text-muted.small', tempDiv).html()
            );
            
            // Update the reviews tab count
            $('#reviews-tab').html(
                $('<i class="fas fa-star me-2"></i>').get(0).outerHTML + 'Reviews (' + newReviewCount + ')'
            );
            
            // Update the reviews summary section
            $('.reviews-summary').html($('.reviews-summary', tempDiv).html());
            
            // Update the rating display
            const ratingText = $('.product-header .stars', tempDiv).html();
            $('.product-header .stars').html(ratingText);
            
            // Update the average rating display
            const averageRating = tempDiv.find('.reviews-summary .average-rating').text();
            $('.reviews-summary .average-rating').text(averageRating);
            
            // Update the rating bars
            $('.rating-bars').html($('.rating-bars', tempDiv).html());
        });
    }
    
    // Handle review deletion
    $(document).on('click', '.delete-review', function() {
        if (!confirm('Are you sure you want to delete this review?')) {
            return false;
        }
        
        const button = $(this);
        const reviewId = button.data('review-id');
        const url = button.data('review-url');
        const reviewItem = button.closest('.review-item');
        
        // Show loading state
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Send delete request
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function(response) {
                // Remove the review item with animation
                reviewItem.fadeOut(300, function() {
                    $(this).remove();
                    
                    // Update reviews count and stats
                    updateReviewStats();
                    
                    // Update reviews list if empty
                    const reviewsCount = $('.review-item').length;
                    if (reviewsCount === 0) {
                        $('.reviews-list').html('<p class="text-muted text-center">No reviews yet. Be the first to review this product!</p>');
                    }
                    
                    // Show success message
                    showToast('Review deleted successfully', 'success');
                });
            },
            error: function(xhr) {
                button.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                const errorMessage = xhr.responseJSON?.message || 'Failed to delete review. Please try again.';
                showToast(errorMessage, 'error');
            }
        });
    });
});
</script>
@endpush
