<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Local Shop</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding-top: 20px;
            z-index: 1000;
        }
        
        .sidebar .brand {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left: 4px solid white;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }
        
        .top-header {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-toggle {
                display: block !important;
            }
        }
        
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #667eea;
        }
        
        /* Big size toggle switches */
        .form-check-input {
            width: 3.5em !important;
            height: 2em !important;
            cursor: pointer;
            background-color: #dc3545; /* Red for OFF */
            border-color: #dc3545;
            transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
        }
        
        .form-check-input:checked {
            background-color: #28a745; /* Green for ON */
            border-color: #28a745;
        }
        
        .form-check .form-check-input {
            float: none;
            margin-left: 0;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @auth
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="brand">
                <i class="fas fa-store"></i> Local Shop
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}" 
                       href="{{ route('admin.banners.index') }}">
                        <i class="fas fa-image"></i> Banners
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" 
                       href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-tags"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" 
                       href="{{ route('admin.products.index') }}">
                        <i class="fas fa-box"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-file-alt"></i> Pages
                    </a>
                </li>
            </ul>
            
            <!-- User Section -->
            <div class="mt-auto pt-3 border-top border-light" style="border-color: rgba(255,255,255,0.1) !important;">
                <div class="px-3">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center" 
                           href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <div class="text-start">
                                <div class="small fw-bold">{{ Auth::user()->name }}</div>
                                <div class="small text-white-50">Administrator</div>
                            </div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Toggle -->
        <button class="mobile-toggle position-fixed" style="top: 20px; left: 20px; z-index: 1001;" 
                onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="fas fa-bars"></i>
        </button>
    @endauth

    <!-- Main Content -->
    <main class="main-content">
        @auth
            <div class="top-header d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-muted mb-0">@yield('page-subtitle', 'Welcome to Local Shop Admin Panel')</p>
                </div>
                <div>
                    <span class="text-muted">
                        <i class="fas fa-clock"></i> 
                        {{ now()->format('M d, Y h:i A') }}
                    </span>
                </div>
            </div>
        @endauth
        
        @yield('content')
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    @stack('scripts')
</body>
</html>
