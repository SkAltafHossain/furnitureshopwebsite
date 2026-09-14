@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome to Local Shop Admin Panel')

@section('content')
<div class="row">
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Banners</h5>
                        <p class="mb-0">{{ $stats['total_banners'] }} Total</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-image fa-lg"></i>
                    </div>
                </div>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-light btn-sm mt-2">
                    Manage <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Categories</h5>
                        <p class="mb-0">{{ $stats['total_categories'] }} Total</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tags fa-lg"></i>
                    </div>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm mt-2">
                    Manage <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Products</h5>
                        <p class="mb-0">{{ $stats['total_products'] }} Total</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-lg"></i>
                    </div>
                </div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-sm mt-2">
                    Manage <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Orders</h5>
                        <p class="mb-0">{{ $stats['total_orders'] }} Total</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                    </div>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-sm mt-2">
                    Manage <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Billing</h5>
                        <p class="mb-0">{{ $stats['total_bills'] }} Total</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-invoice fa-lg"></i>
                    </div>
                </div>
                <a href="{{ route('admin.billing.index') }}" class="btn btn-light btn-sm mt-2">
                    Manage <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Users</h5>
                        <p class="mb-0">{{ $stats['total_users'] }} Total</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm mt-2">
                    Manage <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Quick Stats</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Statistics and analytics will be available here.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-shopping-cart"></i> Recent Orders</h5>
            </div>
            <div class="card-body">
                @if($stats['recent_orders']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_orders'] as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->user ? $order->user->name : 'Guest' }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm">
                            View All Orders <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @else
                    <p class="text-muted">No orders found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
