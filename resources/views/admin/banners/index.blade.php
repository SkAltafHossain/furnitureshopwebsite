@extends('admin.layouts.app')

@section('title', 'Banner Management')
@section('page-title', 'Banner Management')
@section('page-subtitle', 'Manage your website banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bannerModal">
            <i class="fas fa-plus"></i> Add Banner
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table id="bannersTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bannerModalTitle">Add Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bannerForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="banner_id" name="banner_id">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subtitle" class="form-label">Subtitle</label>
                        <input type="text" class="form-control" id="subtitle" name="subtitle">
                    </div>
                    
                    <div class="mb-3">
                        <label for="banner_image" class="form-label">Banner Image *</label>
                        <input type="file" class="form-control" id="banner_image" name="banner_image" 
                               accept="image/*" required>
                        <div class="form-text">Allowed formats: JPEG, PNG, JPG, GIF (Max: 2MB)</div>
                        <div id="imagePreview" class="mt-2"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span id="submitBtnText">Save Banner</span>
                    </button>
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
    var table = $('#bannersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.banners.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'subtitle', name: 'subtitle' },
            { 
                data: 'banner_image', 
                name: 'banner_image',
                render: function(data) {
                    return '<img src="' + (data ? '/storage/' + data : '/placeholder.jpg') + 
                           '" class="img-thumbnail" style="max-width: 100px; max-height: 50px;">';
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    var checked = data === 'active' ? 'checked' : '';
                    return `
                        <div class="form-check form-switch">
                            <input class="form-check-input status-toggle" type="checkbox" 
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
                        <button class="btn btn-sm btn-primary edit-banner" data-id="${row.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm ${row.status === 'active' ? 'btn-warning' : 'btn-success'} toggle-status" 
                                data-id="${row.id}" title="${row.status === 'active' ? 'Deactivate' : 'Activate'}">
                            <i class="fas fa-${row.status === 'active' ? 'eye-slash' : 'eye'}"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Reset modal when closed
    $('#bannerModal').on('hidden.bs.modal', function() {
        $('#bannerForm')[0].reset();
        $('#banner_id').val('');
        $('#imagePreview').html('');
        $('#bannerModalTitle').text('Add Banner');
        $('#submitBtnText').text('Save Banner');
        $('#banner_image').prop('required', true);
    });

    // Image preview
    $('#banner_image').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">');
            };
            reader.readAsDataURL(file);
        }
    });

    // Edit banner
    $(document).on('click', '.edit-banner', function() {
        var id = $(this).data('id');
        
        $.get('{{ route("admin.banners.edit", ":id") }}'.replace(':id', id))
            .done(function(data) {
                $('#banner_id').val(data.id);
                $('#title').val(data.title);
                $('#subtitle').val(data.subtitle);
                $('#status').val(data.status);
                
                if (data.banner_image) {
                    $('#imagePreview').html('<img src="/storage/' + data.banner_image + '" class="img-thumbnail" style="max-width: 200px;">');
                }
                
                $('#bannerModalTitle').text('Edit Banner');
                $('#submitBtnText').text('Update Banner');
                $('#banner_image').prop('required', false);
                $('#bannerModal').modal('show');
            });
    });

    // Toggle status with switch
    $(document).on('change', '.status-toggle', function() {
        var id = $(this).data('id');
        var checkbox = $(this);
        
        $.ajax({
            url: '{{ route("admin.banners.toggle-status", ":id") }}'.replace(':id', id),
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
                alert('Error occurred while updating status.');
            }
        });
    });

    // Submit form
    $('#bannerForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var id = $('#banner_id').val();
        var url = id ? '{{ route("admin.banners.update", ":id") }}'.replace(':id', id) 
                  : '{{ route("admin.banners.store") }}';
        var method = id ? 'PUT' : 'POST';
        
        // Add method override for PUT
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#bannerModal').modal('hide');
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
                    alert('Error occurred while saving banner.');
                }
            }
        });
    });
});
</script>
@endpush
