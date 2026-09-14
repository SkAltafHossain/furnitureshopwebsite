@extends('admin.layouts.app')

@section('title', 'Bill Details - ' . $bill->bill_number)
@section('page-title', 'Bill Details')
@section('page-subtitle', $bill->bill_number)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice"></i> {{ $bill->bill_number }}</h5>
                <div class="btn-group">
                    <a href="{{ route('admin.billing.download', $bill->id) }}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                    <a href="{{ route('admin.billing.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Bills
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Bill Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><strong>Customer Details</strong></h6>
                                <p class="mb-1"><strong>Name:</strong> {{ $bill->customer_name }}</p>
                                <p class="mb-1"><strong>Phone:</strong> {{ $bill->customer_phone }}</p>
                                @if($bill->customer_email)
                                <p class="mb-0"><strong>Email:</strong> {{ $bill->customer_email }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><strong>Bill Information</strong></h6>
                                <p class="mb-1"><strong>Date:</strong> {{ $bill->created_at->format('d M Y, h:i A') }}</p>
                                <p class="mb-1"><strong>Payment:</strong> {{ ucfirst($bill->payment_method) }}</p>
                                <p class="mb-0"><strong>Status:</strong> 
                                    @if($bill->is_sent)
                                        <span class="badge bg-success">Sent</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><strong>Bill Items</strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th class="text-right">MRP</th>
                                        <th class="text-right">Price</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bill->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->product_name }}</strong>
                                            @if($item->product && $item->product->category)
                                            <br><small class="text-muted">{{ $item->product->category->name }}</small>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <span class="text-decoration-line-through text-muted">{{ $item->formatted_unit_price }}</span>
                                        </td>
                                        <td class="text-right">
                                            <strong class="text-success">
                                                ₹{{ number_format($item->unit_price - $item->discount, 2) }}
                                            </strong>
                                        </td>
                                        <td class="text-right">{{ $item->quantity }}</td>
                                        <td class="text-right">
                                            @if($item->discount > 0)
                                                <span class="text-danger">{{ number_format($item->discount, 2) }}</span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ number_format(($item->discount / $item->unit_price) * 100, 1) }}%
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-right"><strong>{{ $item->formatted_total }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="row">
                    <div class="col-md-6">
                        @if($bill->notes)
                        <div class="card bg-warning bg-opacity-10">
                            <div class="card-body">
                                <h6 class="card-title"><strong>Notes</strong></h6>
                                <p class="mb-0">{{ $bill->notes }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title"><strong>Bill Summary</strong></h6>
                                @php
                                    $mrpTotal = $bill->items->sum(function($item) {
                                        return $item->unit_price * $item->quantity;
                                    });
                                    $totalDiscount = $mrpTotal - $bill->subtotal;
                                @endphp
                                <div class="d-flex justify-content-between mb-2">
                                    <span>MRP Total:</span>
                                    <span class="text-decoration-line-through text-muted">₹{{ number_format($mrpTotal, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 text-danger">
                                    <span>Total Discount:</span>
                                    <span>{{ number_format($totalDiscount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>{{ $bill->formatted_subtotal }}</span>
                                </div>
                                <hr class="bg-light">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold fs-5">Total:</span>
                                    <span class="fw-bold fs-5">{{ $bill->formatted_total }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Created By -->
                <div class="mt-3 text-muted">
                    <small>
                        <i class="fas fa-user"></i> Created by: {{ $bill->createdBy ? $bill->createdBy->name : 'System' }} | 
                        <i class="fas fa-clock"></i> {{ $bill->created_at->diffForHumans() }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection