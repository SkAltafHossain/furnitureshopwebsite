// Website JavaScript - Modern Design

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Auto-hide flash messages after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Back to Top Button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $('#backToTop').addClass('show');
        } else {
            $('#backToTop').removeClass('show');
        }
    });
    
    $('#backToTop').click(function() {
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
    });
    
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this).attr('href');
        if (target === '#') return;
        
        var $target = $(target);
        if ($target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: $target.offset().top - 100
            }, 1000);
        }
    });
    
    // Modal Form Submissions
    $('#modalLoginForm, #modalRegisterForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var modal = form.closest('.modal');
        
        // Disable submit button and show loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showToast(response.message, 'success');
                    
                    // Close modal
                    modal.modal('hide');
                    
                    // Redirect after a short delay
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1000);
                } else {
                    // Handle validation errors
                    showToast(response.message || 'An error occurred', 'error');
                    submitBtn.prop('disabled', false).html(submitBtn.data('original-text') || 'Submit');
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON || {};
                var message = response.message || 'An error occurred. Please try again.';
                
                // Show validation errors if any
                if (response.errors) {
                    var firstError = Object.keys(response.errors)[0];
                    message = response.errors[firstError][0];
                }
                
                showToast(message, 'error');
                submitBtn.prop('disabled', false).html(submitBtn.data('original-text') || 'Submit');
            }
        });
    });
    
    // Store original button text
    $('#loginModal button[type="submit"], #registerModal button[type="submit"]').each(function() {
        $(this).data('original-text', $(this).html());
    });
    
    // Reset forms when modals are hidden
    $('#loginModal, #registerModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();
        $(this).find('button[type="submit"]').prop('disabled', false).html($(this).find('button[type="submit"]').data('original-text'));
    });
    
    // Cart functionality
    function updateCartCount() {
        $.get(cartUrls.index, function(data) {
            // Update cart badge if needed
            var cartCount = $('.action-badge');
            if (cartCount.length > 0) {
                // This would be updated based on actual cart data
            }
        });
    }
    
    // Form validation enhancements
    $('form').on('submit', function() {
        var form = $(this);
        var submitBtn = form.find('button[type="submit"], input[type="submit"]');
        
        // Disable submit button to prevent double submission
        submitBtn.prop('disabled', true);
        
        // Add loading state
        submitBtn.addClass('loading');
        
        // Re-enable after 5 seconds in case of errors
        setTimeout(function() {
            submitBtn.prop('disabled', false).removeClass('loading');
        }, 5000);
    });
    
    // Product image gallery (for product detail page)
    $('.product-thumbnail').click(function() {
        var mainImage = $('.product-main-image img');
        var thumbnailSrc = $(this).attr('src');
        mainImage.attr('src', thumbnailSrc);
        
        // Update active state
        $('.product-thumbnail').removeClass('border-primary');
        $(this).addClass('border-primary');
    });
    
    // Quantity selector
    $('.quantity-decrease').click(function() {
        var input = $(this).siblings('input[type="number"]');
        var currentValue = parseInt(input.val());
        var min = parseInt(input.attr('min'));
        if (currentValue > min) {
            input.val(currentValue - 1);
            input.trigger('change');
        }
    });
    
    $('.quantity-increase').click(function() {
        var input = $(this).siblings('input[type="number"]');
        var currentValue = parseInt(input.val());
        var max = parseInt(input.attr('max'));
        if (!max || currentValue < max) {
            input.val(currentValue + 1);
            input.trigger('change');
        }
    });
    
    // Search functionality enhancements
    $('.search-form input').on('input', function() {
        var searchValue = $(this).val().trim();
        if (searchValue.length >= 3) {
            // Could implement live search here
            console.log('Searching for:', searchValue);
        }
    });
    
    // Category dropdown hover effect
    $('.main-nav .dropdown').hover(
        function() {
            $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(300);
        },
        function() {
            $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(300);
        }
    );
    
    // Product card hover effects
    $('.product-card').hover(
        function() {
            $(this).addClass('hovered');
        },
        function() {
            $(this).removeClass('hovered');
        }
    );
    
    // Lazy loading for images
    $('img[data-src]').each(function() {
        var $img = $(this);
        $img.on('load', function() {
            $img.removeClass('loading');
        }).on('error', function() {
            $img.attr('src', '/images/placeholder.jpg');
        });
        
        // Start loading when image is in viewport
        if (isElementInViewport($img[0])) {
            $img.attr('src', $img.data('src'));
        }
    });
    
    // Scroll animations
    $(window).scroll(function() {
        $('.fade-in').each(function() {
            if (isElementInViewport($(this)[0])) {
                $(this).addClass('visible');
            }
        });
    });
    
    // Initialize scroll animations on load
    $('.fade-in').each(function() {
        if (isElementInViewport($(this)[0])) {
            $(this).addClass('visible');
        }
    });
});

// Global functions
function showToast(message, type = 'success') {
    var toastClass = type === 'success' ? 'bg-success' : 'bg-danger';
    var icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    
    var toast = `
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show" role="alert">
                <div class="toast-body ${toastClass} text-white d-flex align-items-center">
                    <i class="${icon} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(toast);
    
    // Auto-remove after 3 seconds
    setTimeout(function() { 
        $('.toast').parent().fadeOut(500, function() {
            $(this).remove();
        }); 
    }, 3000);
}

function formatPrice(price) {
    return '₹' + parseFloat(price).toFixed(2);
}

function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    var re = /^[\d\s\-\+\(\)]+$/;
    return re.test(phone) && phone.replace(/\D/g, '').length >= 10;
}

function isElementInViewport(el) {
    var rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

function addToCart(productId, quantity = 1) {
    $.ajax({
        url: cartUrls.add,
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity,
            _token: csrfToken
        },
        success: function(response) {
            // Update cart count
            $('.action-badge').text(response.cart_count);
            
            // Show success message
            showToast(response.message, 'success');
            
            // Add animation to cart icon
            $('.header-action-btn').addClass('bounce');
            setTimeout(function() {
                $('.header-action-btn').removeClass('bounce');
            }, 600);
        },
        error: function(xhr) {
            var message = 'Error occurred while adding product to cart.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast(message, 'error');
        }
    });
}

function removeFromCart(productId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }
    
    $.ajax({
        url: cartUrls.remove,
        method: 'DELETE',
        data: {
            product_id: productId,
            _token: csrfToken
        },
        success: function(response) {
            showToast(response.message, 'success');
            // Reload page to update cart
            location.reload();
        },
        error: function(xhr) {
            var message = 'Error occurred while removing item.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast(message, 'error');
        }
    });
}

function updateCartQuantity(productId, quantity) {
    $.ajax({
        url: cartUrls.update,
        method: 'PATCH',
        data: {
            product_id: productId,
            quantity: quantity,
            _token: csrfToken
        },
        success: function(response) {
            if (response.success) {
                location.reload(); // Reload to update totals
            } else {
                showToast(response.message || 'Error updating cart', 'error');
            }
        },
        error: function(xhr) {
            var message = 'Error occurred while updating cart.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast(message, 'error');
        }
    });
}

// Add CSS animations dynamically
var style = document.createElement('style');
style.textContent = `
    @keyframes bounce {
        0%, 20%, 53%, 80%, 100% {
            transform: translate3d(0,0,0);
        }
        40%, 43% {
            transform: translate3d(0, -30px, 0);
        }
        70% {
            transform: translate3d(0, -15px, 0);
        }
        90% {
            transform: translate3d(0, -4px, 0);
        }
    }
    
    .bounce {
        animation: bounce 1s;
    }
    
    .fade-in {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }
    
    .fade-in.visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    .product-card.hovered {
        transform: translateY(-12px) scale(1.02);
    }
`;
document.head.appendChild(style);
