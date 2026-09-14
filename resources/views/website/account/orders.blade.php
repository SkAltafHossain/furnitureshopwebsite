@extends('website.layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container-fluid py-5">
    <h1 class="mb-4">My Orders</h1>
    
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
        
        <!-- Orders List -->
        <div class="col-md-9">
            @forelse($orders as $order)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Order #{{ $order->order_id }}</h6>
                            <small class="text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $order->order_status == 'delivered' || $order->order_status == 'delivered_confirmed' ? 'bg-success' : ($order->order_status == 'pending' ? 'bg-warning' : 'bg-info') }}">
                                {{ $order->order_status == 'delivered_confirmed' ? 'Delivered' : ucfirst($order->order_status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p class="mb-1"><strong>Total Amount:</strong> ₹{{ number_format($order->total_amount, 2) }}</p>
                                <p class="mb-1"><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                                <p class="mb-1"><strong>Items:</strong> {{ $order->items->count() }} products</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('account.order-details', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <a href="{{ route('account.order-download', $order->id) }}" class="btn btn-info btn-sm mt-1">
                                    <i class="fas fa-file-pdf"></i> Download Bill
                                </a>
                                @if($order->order_status == 'delivered')
                                    @if($order->canBeConfirmedByUser())
                                        <button class="btn btn-success btn-sm mt-1" onclick="confirmDeliveryFromList({{ $order->id }})">
                                            <i class="fas fa-check"></i> Confirm Delivery
                                        </button>
                                    @else
                                        <button class="btn btn-outline-success btn-sm mt-1">
                                            <i class="fas fa-redo"></i> Reorder
                                        </button>
                                    @endif
                                @elseif($order->order_status == 'delivered_confirmed')
                                    <button class="btn btn-success btn-sm mt-1" disabled>
                                        <i class="fas fa-check"></i> Confirmed
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h4>No Orders Yet</h4>
                    <p class="text-muted">You haven't placed any orders yet. Start shopping to see your orders here.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Start Shopping
                    </a>
                </div>
            @endforelse
            
            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function confirmDeliveryFromList(orderId) {
    if (confirm('Have you received this order?')) {
        fetch(`{{ route('account.order-confirm-delivery', ':id') }}`.replace(':id', orderId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.success);
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to confirm delivery'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while confirming delivery');
        });
    }
}
</script>
@endsection
