@extends('admin.layouts.app')

@section('title', 'Order Management')
@section('page-title', 'Order Management')
@section('page-subtitle', 'View and manage customer orders')

@section('content')
<div class="card">
    <div class="card-body">
        <table id="ordersTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Amount</th>
                    <th>Payment Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Order details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStatusForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="order_id" name="order_id">
                    
                    <div class="mb-3">
                        <label for="order_status" class="form-label">Order Status</label>
                        <select class="form-select" id="order_status" name="order_status" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .order-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }
    .order-section-title {
        color: #0d6efd; /* Blue for all section titles */
        border-color: #0d6efd30;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#ordersTable').DataTable({
        processing: true,
        ajax: '{{ route("admin.orders.data") }}',
        columns: [
            { data: 'order_id', name: 'order_id' },
            { data: 'customer_name', name: 'customer_name' },
            { 
                data: 'total_amount', 
                name: 'total_amount',
                render: function(data) {
                    return '₹' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'payment_method', 
                name: 'payment_method',
                render: function(data) {
                    var displayText = data ? data.toUpperCase() : 'N/A';
                    var badgeClass = data && data.toUpperCase() === 'COD' ? 'bg-success' : 'bg-info';
                    return '<span class="badge ' + badgeClass + '">' + displayText + '</span>';
                }
            },
            { 
                data: 'order_status', 
                name: 'order_status',
                render: function(data) {
                    var badges = {
                        'pending': 'bg-warning',
                        'processing': 'bg-info',
                        'shipped': 'bg-primary',
                        'delivered': 'bg-success',
                        'cancelled': 'bg-danger'
                    };
                    var badgeClass = badges[data] || 'bg-secondary';
                    return '<span class="badge ' + badgeClass + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                }
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var actions = `
                        <button class="btn btn-sm btn-info view-order" data-id="${row.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                    `;
                    
                    // Only show update status button if order is not cancelled, delivered, or delivered_confirmed
                    if (row.order_status !== 'cancelled' && row.order_status !== 'delivered' && row.order_status !== 'delivered_confirmed') {
                        actions += `
                            <button class="btn btn-sm btn-primary update-status" 
                                    data-id="${row.id}" data-status="${row.order_status}">
                                <i class="fas fa-edit"></i>
                            </button>
                        `;
                    }
                    
                    return actions;
                }
            }
        ]
    });

    // View order details
    $(document).on('click', '.view-order', function() {
        var id = $(this).data('id');
        
        $.get('{{ route("admin.orders.show", ":id") }}'.replace(':id', id))
            .done(function(data) {
                // Build order items HTML
                var itemsHtml = '';
                if (data.items && data.items.length > 0) {
                    itemsHtml = `
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.items.forEach(function(item) {
                        itemsHtml += `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="ms-3">
                                            <h6 class="mb-0">${item.product ? item.product.name : 'Product not found'}</h6>
                                            ${item.product ? `<small class="text-muted">SKU: ${item.product.sku || 'N/A'}</small>` : ''}
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">₹${parseFloat(item.price).toFixed(2)}</td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-end">₹${parseFloat(item.total).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    
                    itemsHtml += `
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Subtotal:</th>
                                        <td class="text-end">₹${parseFloat(data.total_amount).toFixed(2)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    `;
                } else {
                    itemsHtml = '<div class="alert alert-info">No items found in this order.</div>';
                }

                // Format status badge
                var statusBadges = {
                    'pending': 'bg-warning',
                    'processing': 'bg-info',
                    'shipped': 'bg-primary',
                    'delivered': 'bg-success',
                    'delivered_confirmed': 'bg-success',
                    'cancelled': 'bg-danger'
                };
                var statusText = data.order_status === 'delivered_confirmed' ? 'Delivered' : data.order_status.charAt(0).toUpperCase() + data.order_status.slice(1);
                var statusBadge = `<span class="badge ${statusBadges[data.order_status] || 'bg-secondary'}">${statusText}</span>`;
                
                // Format payment status
                var paymentStatus = data.payment_status ? data.payment_status.charAt(0).toUpperCase() + data.payment_status.slice(1) : 'N/A';

                var html = `
                    <div class="row">
                        <!-- Left Column: Order Information -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Order Information</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Order ID:</strong> ${data.order_id}</p>
                                    <p><strong>Customer Name:</strong> ${data.customer_name}</p>
                                    <p><strong>Customer Email:</strong> ${data.customer_email}</p>
                                    <p><strong>Customer Phone:</strong> ${data.customer_phone}</p>
                                    <hr>
                                    <p><strong>Payment Method:</strong> ${data.payment_method ? data.payment_method.toUpperCase() : 'N/A'}</p>
                                    <p class="mb-0"><strong>Total Amount:</strong> ₹${parseFloat(data.total_amount).toFixed(2)}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Status and Delivery Address -->
                        <div class="col-md-6">
                            <!-- Status Card -->
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Order Status</h6>
                                    ${statusBadge}
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>Payment Status:</strong> ${paymentStatus}
                                    </div>
                                    <div class="mb-2">
                                        <strong>Order Date:</strong> ${new Date(data.created_at).toLocaleString()}
                                    </div>
                                    <div class="mb-0">
                                        <strong>Last Updated:</strong> ${new Date(data.updated_at).toLocaleString()}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delivery Address Card -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Delivery Address</h6>
                                </div>
                                <div class="card-body">
                                    ${data.address ? `
                                        <p class="mb-1"><strong>${data.customer_name}</strong></p>
                                        <p class="mb-1">${data.customer_phone}</p>
                                        <p class="mb-1">${data.address.address_line1}</p>
                                        ${data.address.address_line2 ? `<p class="mb-1">${data.address.address_line2}</p>` : ''}
                                        <p class="mb-1">
                                            ${data.address.city}, ${data.address.state} - ${data.address.postal_code}
                                        </p>
                                        ${data.address.country ? `<p class="mb-0">${data.address.country}</p>` : ''}
                                    ` : '<p class="mb-0">No address found</p>'}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Full Width: Order Items -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Order Items</h6>
                                </div>
                                <div class="card-body p-0">
                                    ${itemsHtml}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#orderDetailsContent').html(html);
                $('#orderDetailsModal').modal('show');
            });
    });

    // Update status
    $(document).on('click', '.update-status', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        
        $('#order_id').val(id);
        $('#order_status').val(status);
        $('#updateStatusModal').modal('show');
    });

    // Submit status update
    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        
        var id = $('#order_id').val();
        var formData = new FormData(this);
        
        // Add CSRF token to form data
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');
        
        $.ajax({
            url: '{{ route("admin.orders.update-status", ":id") }}'.replace(':id', id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#updateStatusModal').modal('hide');
                table.ajax.reload();
                alert(response.success);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    for (var key in errors) {
                        errorMessages.push(errors[key][0]);
                    }
                    alert('Validation errors:\n' + errorMessages.join('\n'));
                }
            }
        });

    // Function to show notifications
    function showNotification(message) {
        // Create notification element
        var notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
    });
});
</script>
@endpush
