@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome to Local Shop Admin Panel')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>Banners</h4>
                        <p class="mb-0">Manage banners</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-image fa-2x"></i>
                    </div>
                </div>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-light btn-sm mt-3">
                    Manage <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>Categories</h4>
                        <p class="mb-0">Product categories</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                </div>
                <button class="btn btn-light btn-sm mt-3" disabled>
                    Coming Soon
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>Products</h4>
                        <p class="mb-0">Manage products</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
                <button class="btn btn-light btn-sm mt-3" disabled>
                    Coming Soon
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>Orders</h4>
                        <p class="mb-0">View orders</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
                <button class="btn btn-light btn-sm mt-3" disabled>
                    Coming Soon
                </button>
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
                <h5><i class="fas fa-bell"></i> Recent Activity</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Recent admin activities will be displayed here.</p>
            </div>
        </div>
    </div>
</div>
@endsection
