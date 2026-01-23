@extends('admin.layouts.app')

@section('title', 'Category Management')
@section('page-title', 'Category Management')
@section('page-subtitle', 'Manage product categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
            <i class="fas fa-plus"></i> Add Category
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table id="categoriesTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Slug</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="category_id" name="category_id">
                    
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                        <div class="form-text">Slug will be automatically generated from the category name</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_image" class="form-label">Category Image *</label>
                        <input type="file" class="form-control" id="category_image" name="category_image" 
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
                        <span id="submitBtnText">Save Category</span>
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
    var table = $('#categoriesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.categories.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'category_name', name: 'category_name' },
            { data: 'slug', name: 'slug' },
            { 
                data: 'category_image', 
                name: 'category_image',
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
                            <input class="form-check-input category-status-toggle" type="checkbox" 
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
                        <button class="btn btn-sm btn-primary edit-category" data-id="${row.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-category" 
                                data-id="${row.id}" data-name="${row.category_name}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Reset modal when closed
    $('#categoryModal').on('hidden.bs.modal', function() {
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('#imagePreview').html('');
        $('#categoryModalTitle').text('Add Category');
        $('#submitBtnText').text('Save Category');
        $('#category_image').prop('required', true);
    });

    // Image preview
    $('#category_image').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">');
            };
            reader.readAsDataURL(file);
        }
    });

    // Edit category
    $(document).on('click', '.edit-category', function() {
        var id = $(this).data('id');
        
        $.get('{{ route("admin.categories.edit", ":id") }}'.replace(':id', id))
            .done(function(data) {
                $('#category_id').val(data.id);
                $('#category_name').val(data.category_name);
                $('#status').val(data.status);
                
                if (data.category_image) {
                    $('#imagePreview').html('<img src="/storage/' + data.category_image + '" class="img-thumbnail" style="max-width: 200px;">');
                }
                
                $('#categoryModalTitle').text('Edit Category');
                $('#submitBtnText').text('Update Category');
                $('#category_image').prop('required', false);
                $('#categoryModal').modal('show');
            });
    });

    // Toggle status with switch
    $(document).on('change', '.category-status-toggle', function() {
        var id = $(this).data('id');
        var checkbox = $(this);
        
        $.ajax({
            url: '{{ route("admin.categories.toggle-status", ":id") }}'.replace(':id', id),
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

    // Delete category
    $(document).on('click', '.delete-category', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('Are you sure you want to delete the category "' + name + '"? This action can be undone.')) {
            $.ajax({
                url: '{{ route("admin.categories.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload();
                    alert(response.success);
                },
                error: function() {
                    alert('Error occurred while deleting category.');
                }
            });
        }
    });

    // Submit form
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var id = $('#category_id').val();
        var url = id ? '{{ route("admin.categories.update", ":id") }}'.replace(':id', id) 
                  : '{{ route("admin.categories.store") }}';
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
                $('#categoryModal').modal('hide');
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
                    alert('Error occurred while saving category.');
                }
            }
        });
    });
});
</script>
@endpush
