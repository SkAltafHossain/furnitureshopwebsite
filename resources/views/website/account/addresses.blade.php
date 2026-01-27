@extends('website.layouts.app')

@section('title', 'My Addresses')

@section('content')
<div class="container-fluid py-5">
    <h1 class="mb-4">My Addresses</h1>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="row">
        <!-- Account Navigation -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <h5 class="mt-2">{{ auth()->user()->name }}</h5>
                        <p class="text-muted small">{{ auth()->user()->email }}</p>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a href="{{ route('account.index') }}" class="nav-link">
                            <i class="fas fa-user me-2"></i> Profile Information
                        </a>
                        <a href="{{ route('account.orders') }}" class="nav-link">
                            <i class="fas fa-shopping-bag me-2"></i> My Orders
                        </a>
                        <a href="{{ route('account.addresses') }}" class="nav-link active">
                            <i class="fas fa-map-marker-alt me-2"></i> Addresses
                        </a>
                        <a href="#" class="nav-link">
                            <i class="fas fa-heart me-2"></i> Wishlist
                        </a>
                        <a href="#" class="nav-link">
                            <i class="fas fa-cog me-2"></i> Settings
                        </a>
                        <hr>
                        <a href="{{ route('logout') }}" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- Addresses Section -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Saved Addresses</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                        <i class="fas fa-plus me-2"></i>Add New Address
                    </button>
                </div>
                <div class="card-body">
                    @if($addresses->count() > 0)
                        <div class="row">
                            @foreach($addresses as $address)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 {{ $address->is_default ? 'border-primary' : '' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <span class="badge bg-{{ $address->is_default ? 'primary' : 'secondary' }}">
                                                        {{ ucfirst($address->type) }}
                                                    </span>
                                                    @if($address->is_default)
                                                        <span class="badge bg-success ms-2">Default</span>
                                                    @endif
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                                        @if(!$address->is_default)
                                                            <li>
                                                                <form action="{{ route('account.set-default-address', $address->id) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-star me-2"></i>Set as Default
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('account.delete-address', $address->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this address?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash me-2"></i>Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            
                                            <div class="address-details">
                                                <p class="mb-2"><strong>{{ $address->name ?? auth()->user()->name }}</strong></p>
                                                <p class="mb-1">{{ $address->address_line1 }}</p>
                                                @if($address->address_line2)
                                                    <p class="mb-1">{{ $address->address_line2 }}</p>
                                                @endif
                                                <p class="mb-1">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                                <p class="mb-0">{{ $address->country }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No saved addresses</h5>
                            <p class="text-muted">Add your first address to make checkout faster</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                <i class="fas fa-plus me-2"></i>Add Your First Address
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal Component -->
@include('components.address-modal')
@endsection
