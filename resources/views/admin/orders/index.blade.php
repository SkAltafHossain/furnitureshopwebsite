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

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#ordersTable').DataTable({
        processing: true,
        serverSide: true,
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
                data: 'payment_type', 
                name: 'payment_type',
                render: function(data) {
                    var badgeClass = data === 'COD' ? 'bg-success' : 'bg-info';
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
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
                    return `
                        <button class="btn btn-sm btn-info view-order" data-id="${row.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary update-status" 
                                data-id="${row.id}" data-status="${row.order_status}">
                            <i class="fas fa-edit"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // View order details
    $(document).on('click', '.view-order', function() {
        var id = $(this).data('id');
        
        $.get('{{ route("admin.orders.show", ":id") }}'.replace(':id', id))
            .done(function(data) {
                var html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p><strong>Order ID:</strong> ${data.order_id}</p>
                            <p><strong>Customer Name:</strong> ${data.customer_name}</p>
                            <p><strong>Total Amount:</strong> ₹${parseFloat(data.total_amount).toFixed(2)}</p>
                            <p><strong>Payment Type:</strong> ${data.payment_type}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Status & Dates</h6>
                            <p><strong>Order Status:</strong> ${data.order_status.charAt(0).toUpperCase() + data.order_status.slice(1)}</p>
                            <p><strong>Order Date:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                            <p><strong>Last Updated:</strong> ${new Date(data.updated_at).toLocaleDateString()}</p>
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
                } else {
                    alert('Error occurred while updating order status.');
                }
            }
        });
    });
});
</script>
@endpush
