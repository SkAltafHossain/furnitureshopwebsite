@extends('admin.layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')
@section('page-subtitle', 'Manage users and their access')

@section('content')
<div class="card">
    <div class="card-body">
        <table id="usersTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Registration Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Order History Modal -->
<div class="modal fade" id="orderHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Order History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderHistoryContent">
                <!-- Order history will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.users.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    var checked = data === 'active' ? 'checked' : '';
                    return `
                        <div class="form-check form-switch">
                            <input class="form-check-input user-status-toggle" type="checkbox" 
                                   data-id="${row.id}" ${checked}>
                        </div>
                    `;
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
                        <button class="btn btn-sm btn-info view-orders" data-id="${row.id}" data-name="${row.name}">
                            <i class="fas fa-shopping-cart"></i> Orders
                        </button>
                    `;
                }
            }
        ]
    });

    // Toggle user status
    $(document).on('change', '.user-status-toggle', function() {
        var id = $(this).data('id');
        var checkbox = $(this);
        
        $.ajax({
            url: '{{ route("admin.users.toggle-status", ":id") }}'.replace(':id', id),
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                table.ajax.reload();
                // Show success message
                var toast = '<div class="position-fixed top-0 end-0 p-3" style="z-index: 11"><div class="toast show" role="alert"><div class="toast-body">' + response.success + '</div></div></div>';
                $('body').append(toast);
                setTimeout(function() { $('.toast').parent().remove(); }, 3000);
            },
            error: function() {
                // Revert checkbox on error
                checkbox.prop('checked', !checkbox.prop('checked'));
                alert('Error occurred while updating user status.');
            }
        });
    });

    // View order history
    $(document).on('click', '.view-orders', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        $.get('{{ route("admin.users.order-history", ":id") }}'.replace(':id', id))
            .done(function(data) {
                var html = `
                    <div class="mb-3">
                        <h6>User Information</h6>
                        <p><strong>Name:</strong> ${data.user.name}</p>
                        <p><strong>Email:</strong> ${data.user.email}</p>
                        <p><strong>Phone:</strong> ${data.user.phone || 'N/A'}</p>
                        <p><strong>Status:</strong> <span class="badge ${data.user.status === 'active' ? 'bg-success' : 'bg-danger'}">${data.user.status}</span></p>
                    </div>
                    
                    <div>
                        <h6>Order History</h6>
                        ${data.orders.length > 0 ? `
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.orders.map(order => `
                                            <tr>
                                                <td>${order.order_id}</td>
                                                <td>${new Date(order.created_at).toLocaleDateString()}</td>
                                                <td>₹${parseFloat(order.total_amount).toFixed(2)}</td>
                                                <td><span class="badge bg-info">${order.order_status}</span></td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        ` : '<p class="text-muted">No orders found for this user.</p>'}
                    </div>
                `;
                $('#orderHistoryContent').html(html);
                $('#orderHistoryModal').modal('show');
            });
    });
});
</script>
@endpush
