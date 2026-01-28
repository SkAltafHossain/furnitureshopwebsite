@extends('website.layouts.app')

@section('title', 'Order Details - ' . $order->order_id)

@section('content')
<div class="container-fluid py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.orders') }}">My Orders</a></li>
            <li class="breadcrumb-item active">Order #{{ $order->order_id }}</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Order Navigation -->
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
                        <a href="{{ route('account.orders') }}" class="nav-link active">
                            <i class="fas fa-shopping-bag me-2"></i> My Orders
                        </a>
                        <a href="{{ route('account.addresses') }}" class="nav-link">
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
        
        <!-- Order Details -->
        <div class="col-md-9">
            <!-- Order Header -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">Order #{{ $order->order_id }}</h4>
                            <small class="text-muted">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $order->order_status == 'delivered' ? 'bg-success' : ($order->order_status == 'pending' ? 'bg-warning' : 'bg-info') }} fs-6">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Shipping Address</h6>
                            <p class="mb-0"><strong>{{ $order->customer_name }}</strong></p>
                            <p class="mb-0">{{ $order->customer_phone }}</p>
                            @if($order->address)
                                <p class="mb-0">{{ $order->address->full_address }}</p>
                            @else
                                <p class="text-danger">No address found</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p class="mb-0"><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                            <p class="mb-0"><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
                            <p class="mb-0"><strong>Total Amount:</strong> ₹{{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Items ({{ $order->items->count() }})</h5>
                </div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <!-- Product Image -->
                            <div class="me-3">
                                @if($item->product && $item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" style="width: 60px; height: 60px; object-fit: cover;" class="rounded">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px;">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Product Details -->
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    @if($item->product)
                                        <a href="{{ route('products.show', $item->product->slug) }}" class="text-decoration-none text-dark">
                                            {{ $item->product->name }}
                                        </a>
                                    @else
                                        Product Not Available
                                    @endif
                                </h6>
                                <p class="text-muted mb-0 small">Quantity: {{ $item->quantity }}</p>
                                <p class="text-muted mb-0 small">Price: ₹{{ number_format($item->price, 2) }} each</p>
                            </div>
                            
                            <!-- Item Total -->
                            <div class="text-end">
                                <div class="fw-bold">₹{{ number_format($item->total, 2) }}</div>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- Order Summary -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Order Summary</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal</span>
                                    <span>₹{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping</span>
                                    <span>FREE</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax</span>
                                    <span>Included</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <h6>Total</h6>
                                    <h6 class="text-primary">₹{{ number_format($order->total_amount, 2) }}</h6>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <h6>Order Timeline</h6>
                                <div class="small">
                                    <p class="mb-1"><strong>Order Placed:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
                                    @if($order->updated_at != $order->created_at)
                                        <p class="mb-1"><strong>Last Updated:</strong> {{ $order->updated_at->format('d M Y, h:i A') }}</p>
                                    @endif
                                    <p class="mb-1"><strong>Expected Delivery:</strong> {{ $order->created_at->addDays(5)->format('d M Y') }}</p>
                                </div>
                                
                                <div class="mt-3">
                                    @if($order->order_status == 'delivered')
                                        <button class="btn btn-success btn-sm">
                                            <i class="fas fa-redo"></i> Reorder
                                        </button>
                                    @elseif($order->order_status == 'pending')
                                        <button class="btn btn-outline-danger btn-sm" onclick="cancelOrder({{ $order->id }})" style="cursor: pointer;">
                                            <i class="fas fa-times"></i> Cancel Order
                                        </button>
                                    @else
                                        <small class="text-muted">Order cannot be cancelled (Status: {{ $order->order_status }})</small>
                                    @endif
                                    <a href="{{ route('account.orders') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Back to Orders
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelOrder(orderId) {
    console.log('Cancel order function called with ID:', orderId);
    if (confirm('Are you sure you want to cancel this order?')) {
        console.log('User confirmed cancellation');
        fetch(`{{ route('account.order-cancel', ':id') }}`.replace(':id', orderId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            console.log('Response received:', response);
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.success) {
                alert(data.success);
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to cancel order'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling the order');
        });
    } else {
        console.log('User cancelled the action');
    }
}
</script>
@endsection
