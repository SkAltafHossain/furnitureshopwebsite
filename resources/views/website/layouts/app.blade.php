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
    <!-- Top Bar -->
    <div class="top-bar bg-dark text-white py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-phone me-2"></i>
                        <span class="me-3">+1234567890</span>
                        <i class="fas fa-envelope me-2"></i>
                        <span>info@localshop.com</span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-inline-flex align-items-center">
                        <a href="{{ route('page.about') }}" class="text-white text-decoration-none me-3">About Us</a>
                        <a href="{{ route('page.terms') }}" class="text-white text-decoration-none me-3">Terms</a>
                        <a href="{{ route('page.privacy') }}" class="text-white text-decoration-none">Privacy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header bg-white shadow-sm sticky-top">
        <div class="container">
            <div class="row align-items-center py-4">
                <!-- Logo -->
                <div class="col-lg-3">
                    <a href="{{ route('home') }}" class="brand-logo text-decoration-none d-flex align-items-center">
                        <div class="logo-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <h3 class="brand-name mb-0 text-primary">Local Shop</h3>
                            <small class="text-muted">Premium Store</small>
                        </div>
                    </a>
                </div>
                
                <!-- Search Bar -->
                <div class="col-lg-6">
                    <div class="search-container">
                        <form class="search-form" action="{{ route('products.index') }}" method="GET">
                            <div class="input-group input-group-lg">
                                <input type="search" class="form-control border-end-0" name="search" placeholder="Search for products..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Header Actions -->
                <div class="col-lg-3">
                    <div class="header-actions d-flex justify-content-end align-items-center">
                        <!-- Cart -->
                        <a href="{{ route('cart.index') }}" class="header-action-btn position-relative me-3">
                            <div class="action-icon">
                                <i class="fas fa-shopping-bag"></i>
                                @if(session('cart'))
                                    <span class="action-badge">{{ count(session('cart')) }}</span>
                                @endif
                            </div>
                            <span class="action-label">Cart</span>
                        </a>
                        
                        <!-- User Account -->
                        @if(auth()->check())
                            <div class="dropdown">
                                <button class="header-action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <div class="action-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="action-label">{{ auth()->user()->name }}</span>
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
                            <div class="header-actions-group">
                                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-sign-in-alt me-1"></i> Login
                                </a>
                                <a href="{{ route('register') }}" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-1"></i> Register
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="main-nav bg-primary">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="main-menu">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                    <i class="fas fa-home me-1"></i> Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                    <i class="fas fa-th-large me-1"></i> All Products
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link text-white dropdown-toggle" href="#" data-bs-toggle="dropdown">
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
                                <a class="nav-link text-white" href="{{ route('page.about') }}">
                                    <i class="fas fa-info-circle me-1"></i> About
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="{{ route('page.terms') }}">
                                    <i class="fas fa-file-contract me-1"></i> Terms
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Special Offers -->
                    <div class="special-offer">
                        <span class="badge bg-danger text-white">
                            <i class="fas fa-tag me-1"></i> Special Offers
                        </span>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Flash Messages -->
    @if(session('success') || session('error'))
        <div class="flash-messages">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                    <div class="container">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                    <div class="container">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif
        </div>
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
    
    @stack('scripts')
</body>
</html>
