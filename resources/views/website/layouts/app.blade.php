<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Local Shop') - Premium E-commerce Store</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/website.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <!-- Main Header -->
    <header class="main-header bg-white shadow-sm sticky-top">
        <div class="container-fluid">
            <div class="row align-items-center py-2">
                <!-- Logo -->
                <div class="col-auto">
                    <a href="{{ route('home') }}" class="brand-logo text-decoration-none d-flex align-items-center">
                        <div class="logo-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <h4 class="brand-name mb-0 text-primary">Local Shop</h4>
                        </div>
                    </a>
                </div>
                
                <!-- Navigation Menu -->
                <div class="col-auto">
                    <nav class="main-nav">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link text-dark {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                    <i class="fas fa-home me-1"></i> Home
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link text-dark dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="fas fa-list me-1"></i> Categories
                                </a>
                                <ul class="dropdown-menu">
                                    @php
                                        $categories = \App\Models\Category::where('status', 'active')->take(6)->get();
                                    @endphp
                                    @foreach($categories as $category)
                                        <li><a class="dropdown-item" href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                    <i class="fas fa-th-large me-1"></i> Products
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                
                <!-- Large Search Bar -->
                <div class="col">
                    <div class="search-container">
                        <form class="search-form d-flex" action="{{ route('products.index') }}" method="GET">
                            <input type="search" class="form-control form-control-sm" name="search" placeholder="Search for products..." value="{{ request('search') }}">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Cart -->
                <div class="col-auto">
                    @if(auth()->check())
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary position-relative">
                            <i class="fas fa-shopping-bag"></i>
                            Cart
                            @if(session('cart'))
                                <span class="action-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    {{ count(session('cart')) }}
                                </span>
                            @else
                                <span class="action-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; display: none;">
                                    0
                                </span>
                            @endif
                        </a>
                    @else
                        <button class="btn btn-outline-primary position-relative" onclick="$('#loginModal').modal('show');">
                            <i class="fas fa-shopping-bag"></i>
                            Cart
                            <span class="action-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; display: none;">
                                0
                            </span>
                        </button>
                    @endif
                </div>
                
                <!-- Account Section -->
                <div class="col-auto">
                    <div class="header-actions d-flex align-items-center">
                        @if(auth()->check())
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user me-1"></i> {{ auth()->user()->name }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('account.index') }}"><i class="fas fa-user me-2"></i> My Account</a></li>
                                    <li><a class="dropdown-item" href="{{ route('account.orders') }}"><i class="fas fa-shopping-bag me-2"></i> My Orders</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                                </ul>
                            </div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </button>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </button>
                        @endif
                    </div>
                </div>
            </row>
        </div>
    </header>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-5 d-none d-lg-block">
                            <div class="auth-sidebar h-100 d-flex align-items-center justify-content-center text-white text-center p-4" style="background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);">
                                <div>
                                    <div class="mb-4">
                                        <i class="fas fa-shopping-cart fa-4x"></i>
                                    </div>
                                    <h4 class="mb-3">Welcome Back!</h4>
                                    <p class="mb-0">Sign in to access your account and continue shopping</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="p-4 p-lg-5">
                                <div class="text-center mb-4">
                                    <h3 class="mb-1">Sign In</h3>
                                    <p class="text-muted">Enter your credentials to access your account</p>
                                </div>
                                
                                <form method="POST" action="{{ route('login') }}" id="modalLoginForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-semibold">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-envelope text-primary"></i>
                                            </span>
                                            <input type="email" class="form-control border-start-0" 
                                                   id="email" name="email" value="{{ old('email') }}" 
                                                   placeholder="Enter your email" required autofocus>
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-semibold">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-lock text-primary"></i>
                                            </span>
                                            <input type="password" class="form-control border-start-0" 
                                                   id="password" name="password" placeholder="Enter your password" required>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">
                                                Remember me
                                            </label>
                                        </div>
                                        <a href="#" class="text-primary text-decoration-none">Forgot password?</a>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-sign-in-alt me-2"></i> Sign In
                                        </button>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <p class="mb-0">Don't have an account? 
                                            <a href="#" class="text-primary text-decoration-none fw-semibold" 
                                               data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">
                                                Create one here
                                            </a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-5 d-none d-lg-block">
                            <div class="auth-sidebar h-100 d-flex align-items-center justify-content-center text-white text-center p-4" style="background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);">
                                <div>
                                    <div class="mb-4">
                                        <i class="fas fa-user-plus fa-4x"></i>
                                    </div>
                                    <h4 class="mb-3">Join Us Today!</h4>
                                    <p class="mb-0">Create your account and start shopping amazing products</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="p-4 p-lg-5">
                                <div class="text-center mb-4">
                                    <h3 class="mb-1">Create Account</h3>
                                    <p class="text-muted">Fill in your details to get started</p>
                                </div>
                                
                                <form method="POST" action="{{ route('register') }}" id="modalRegisterForm">
                                    @csrf
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label fw-semibold">Full Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0">
                                                    <i class="fas fa-user text-primary"></i>
                                                </span>
                                                <input type="text" class="form-control border-start-0" 
                                                       id="name" name="name" value="{{ old('name') }}" 
                                                       placeholder="Enter your name" required autofocus>
                                            </div>
                                            @error('name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label fw-semibold">Email Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0">
                                                    <i class="fas fa-envelope text-primary"></i>
                                                </span>
                                                <input type="email" class="form-control border-start-0" 
                                                       id="email" name="email" value="{{ old('email') }}" 
                                                       placeholder="Enter your email" required>
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-phone text-primary"></i>
                                            </span>
                                            <input type="tel" class="form-control border-start-0" 
                                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                                   placeholder="+1234567890">
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label fw-semibold">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0">
                                                    <i class="fas fa-lock text-primary"></i>
                                                </span>
                                                <input type="password" class="form-control border-start-0" 
                                                       id="password" name="password" placeholder="Create password" required>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0">
                                                    <i class="fas fa-lock text-primary"></i>
                                                </span>
                                                <input type="password" class="form-control border-start-0" 
                                                       id="password_confirmation" name="password_confirmation" 
                                                       placeholder="Confirm password" required>
                                            </div>
                                            @error('password_confirmation')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                            <label class="form-check-label" for="terms">
                                                I agree to the <a href="{{ route('page.terms') }}" target="_blank" class="text-primary">Terms & Conditions</a> 
                                                and <a href="{{ route('page.privacy') }}" target="_blank" class="text-primary">Privacy Policy</a>
                                            </label>
                                            @error('terms')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-user-plus me-2"></i> Create Account
                                        </button>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <p class="mb-0">Already have an account? 
                                            <a href="#" class="text-primary text-decoration-none fw-semibold" 
                                               data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">
                                                Sign in here
                                            </a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Flash Messages as Toasts -->
    @if(session('success') || session('error'))
        <script>
        $(document).ready(function() {
            @if(session('success'))
                showToast('{{ session('success') }}', 'success');
            @endif
            
            @if(session('error'))
                showToast('{{ session('error') }}', 'error');
            @endif
        });
        </script>
    @endif

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <!-- About Section -->
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand">
                        <div class="d-flex align-items-center mb-3">
                            <div class="footer-logo bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-store"></i>
                            </div>
                            <h4 class="mb-0">Local Shop</h4>
                        </div>
                        <p class="text-muted">Your trusted local e-commerce store for quality products at affordable prices. Shop with confidence and enjoy exceptional service.</p>
                        <div class="social-links mt-3">
                            <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}" class="text-white-50"><i class="fas fa-chevron-right me-2"></i>Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-white-50"><i class="fas fa-chevron-right me-2"></i>Products</a></li>
                        <li><a href="{{ route('page.about') }}" class="text-white-50"><i class="fas fa-chevron-right me-2"></i>About Us</a></li>
                        <li><a href="{{ route('page.terms') }}" class="text-white-50"><i class="fas fa-chevron-right me-2"></i>Terms</a></li>
                        <li><a href="{{ route('page.privacy') }}" class="text-white-50"><i class="fas fa-chevron-right me-2"></i>Privacy</a></li>
                    </ul>
                </div>
                
                <!-- Customer Service -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="footer-title">Customer Service</h5>
                    <ul class="footer-links">
                        <li><a href="#" class="text-white-50"><i class="fas fa-chevron-right me-2"></i>Contact Us</a></li>
                        <li><a href="#" class="text-white-50"><i class="fas fa-chevron-right me-2"></i>Shipping Info</a></li>
                        <li><a href="#" class="text-white-50"><i class="fas fa-chevron-right me-2"></i>Returns</a></li>
                        <li><a href="#" class="text-white-50"><i class="fas fa-chevron-right me-2"></i>FAQs</a></li>
                        <li><a href="#" class="text-white-50"><i class="fas fa-chevron-right me-2"></i>Size Guide</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-3 mb-4">
                    <h5 class="footer-title">Contact Info</h5>
                    <div class="contact-info">
                        <p class="text-white-50 mb-2">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                            123 Main Street, City Center
                        </p>
                        <p class="text-white-50 mb-2">
                            <i class="fas fa-phone me-2 text-primary"></i>
                            +1234567890
                        </p>
                        <p class="text-white-50 mb-3">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            info@localshop.com
                        </p>
                        
                        <!-- Payment Methods -->
                        <div class="payment-methods">
                            <p class="text-white-50 small mb-2">We Accept:</p>
                            <div class="payment-icons">
                                <i class="fab fa-cc-visa text-white me-2"></i>
                                <i class="fab fa-cc-mastercard text-white me-2"></i>
                                <i class="fab fa-cc-paypal text-white me-2"></i>
                                <i class="fas fa-money-bill-wave text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Footer -->
            <div class="footer-bottom border-top border-secondary pt-4 mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-white-50 mb-0">&copy; {{ date('Y') }} Local Shop. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p class="text-white-50 mb-0">Designed with <i class="fas fa-heart text-danger"></i> by Local Shop Team</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/website.js') }}"></script>
    
    <!-- Cart URLs for JavaScript -->
    <script>
    window.cartUrls = {
        add: '{{ route("cart.add") }}',
        remove: '{{ route("cart.remove") }}',
        update: '{{ route("cart.update") }}',
        index: '{{ route("cart.index") }}'
    };
    window.csrfToken = '{{ csrf_token() }}';
    </script>
    
    <!-- Modal Form Handling -->
    <script>
    $(document).ready(function() {
        // Clear form errors when modal is hidden
        $('#loginModal, #registerModal').on('hidden.bs.modal', function() {
            $(this).find('form').trigger('reset');
            $(this).find('.invalid-feedback').remove();
            $(this).find('.is-invalid').removeClass('is-invalid');
            $(this).find('.alert').remove();
        });
        
                
        // Show validation errors in a more elegant way
        function showValidationErrors(errors) {
            var errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            errorHtml += '<i class="fas fa-exclamation-triangle me-2"></i><strong>Error:</strong><br>';
            
            if (typeof errors === 'string') {
                errorHtml += errors;
            } else if (Array.isArray(errors)) {
                errorHtml += errors.join('<br>');
            } else {
                Object.values(errors).forEach(function(error) {
                    if (Array.isArray(error)) {
                        errorHtml += error.join('<br>') + '<br>';
                    } else {
                        errorHtml += error + '<br>';
                    }
                });
            }
            
            errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            errorHtml += '</div>';
            
            // Insert at the top of the modal body
            $('.modal-body').prepend(errorHtml);
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut(500, function() {
                    $(this).remove();
                });
            }, 5000);
        }
    });
    </script>
    
    @stack('scripts')
</body>
</html>
