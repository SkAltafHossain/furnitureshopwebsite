@props([
    'product',
    'showAddToCart' => true,
    'showWishlist' => true,
    'showCategory' => false,
    'showRating' => true
])

<div class="product-card position-relative mb-4">
    <!-- Discount Badge -->
    @if($product->discount_price)
        @php
            $discountPercent = round((($product->price - $product->discount_price) / $product->price) * 100);
        @endphp
        <div class="discount-badge-top-right bg-danger text-white px-2 py-1 rounded position-absolute" 
             style="top: 10px; right: 10px; z-index: 10; font-size: 12px; font-weight: 600;">
            {{ $discountPercent }}% OFF
        </div>
    @endif
    
    <!-- Wishlist Button -->
    @if($showWishlist)
        <button class="wishlist-btn position-absolute" 
                style="top: 10px; left: 10px; z-index: 10; background: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;"
                onclick="toggleWishlist(this, {{ $product->id }})">
            <i class="far fa-heart text-muted" style="font-size: 16px;"></i>
        </button>
    @endif
    
    <!-- Product Image -->
    <div class="product-image-container position-relative overflow-hidden" style="height: 280px; background: #f8f9fa;">
        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" 
                     alt="{{ $product->name }}" 
                     class="img-fluid w-100 h-100" 
                     style="object-fit: cover; transition: transform 0.3s ease;">
            @else
                <div class="d-flex align-items-center justify-content-center h-100">
                    <i class="fas fa-box fa-3x text-muted"></i>
                </div>
            @endif
        </a>
        
        <!-- Quick View Button (Hover) -->
        <div class="quick-view-overlay position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 p-2" 
             style="transform: translateY(100%); transition: transform 0.3s ease;">
            <button class="btn btn-sm btn-light w-100" onclick="quickView({{ $product->id }})">
                <i class="fas fa-eye me-1"></i> Quick View
            </button>
        </div>
    </div>
    
    <!-- Product Details -->
    <div class="product-details p-3">
        <!-- Category -->
        @if($showCategory && $product->category)
            <div class="text-muted small mb-1">
                <a href="{{ route('category.show', $product->category->slug) }}" class="text-decoration-none">
                    {{ $product->category->name }}
                </a>
            </div>
        @endif
        
        <!-- Product Name -->
        <h5 class="product-title mb-2">
            <a href="{{ route('products.show', $product->slug) }}" 
               class="text-decoration-none text-dark" 
               style="font-size: 15px; font-weight: 500; line-height: 1.4;">
                {{ Str::limit($product->name, 50) }}
            </a>
        </h5>
        
        <!-- Rating -->
        @if($showRating)
            <div class="rating mb-2">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white px-1 py-0 rounded me-1" style="font-size: 11px;">
                        4.5 <i class="fas fa-star" style="font-size: 9px;"></i>
                    </div>
                    <span class="text-muted small">(234)</span>
                </div>
            </div>
        @endif
        
        <!-- Price -->
        <div class="price-section mb-3">
            <div class="d-flex align-items-center">
                @if($product->discount_price)
                    <span class="current-price fw-bold text-dark" style="font-size: 16px;">
                        ₹{{ number_format($product->discount_price, 2) }}
                    </span>
                    <span class="original-price text-muted text-decoration-line-through ms-2" style="font-size: 13px;">
                        ₹{{ number_format($product->price, 2) }}
                    </span>
                @else
                    <span class="current-price fw-bold text-dark" style="font-size: 16px;">
                        ₹{{ number_format($product->price, 2) }}
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Stock Status -->
        <div class="stock-status mb-3">
            @if($product->stock > 0)
                <span class="text-success small">
                    <i class="fas fa-check-circle"></i> In Stock
                </span>
            @else
                <span class="text-danger small">
                    <i class="fas fa-times-circle"></i> Out of Stock
                </span>
            @endif
        </div>
        
        <!-- Action Buttons -->
        @if($showAddToCart)
            <div class="action-buttons d-flex gap-2">
                <button class="btn btn-primary flex-fill" 
                        {{ $product->stock <= 0 ? 'disabled' : '' }}
                        onclick="addToCart({{ $product->id }})">
                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                </button>
                <button class="btn btn-success flex-fill" 
                        {{ $product->stock <= 0 ? 'disabled' : '' }}
                        onclick="buyNow({{ $product->id }})">
                    <i class="fas fa-bolt me-1"></i> Buy Now
                </button>
            </div>
        @endif
    </div>
</div>

<style>
.product-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
}

.product-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.product-card:hover .product-image-container img {
    transform: scale(1.05);
}

.product-card:hover .quick-view-overlay {
    transform: translateY(0);
}

.wishlist-btn:hover {
    background: white !important;
}

.wishlist-btn.active i {
    color: #dc3545 !important;
}

.product-title a:hover {
    color: #ff9f00 !important;
}

.btn-primary:hover:not(:disabled) {
    background: #0056b3;
    border-color: #0056b3;
}

.btn-success:hover:not(:disabled) {
    background: #218838;
    border-color: #218838;
}

.discount-badge-top-right {
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
</style>

<script>
// These functions should be defined in your main layout or a separate JS file
function toggleWishlist(btn, productId) {
    btn.classList.toggle('active');
    const icon = btn.querySelector('i');
    if (btn.classList.contains('active')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        // Add to wishlist logic here
    } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        // Remove from wishlist logic here
    }
}

function quickView(productId) {
    // Quick view modal logic here
    console.log('Quick view for product:', productId);
}

function addToCart(productId) {
    // Add to cart logic here
    console.log('Add to cart:', productId);
}

function buyNow(productId) {
    // Buy now logic here
    console.log('Buy now:', productId);
}
</script>
