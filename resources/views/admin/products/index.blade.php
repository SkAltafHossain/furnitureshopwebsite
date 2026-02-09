@extends('admin.layouts.app')

@section('title', 'Product Management')
@section('page-title', 'Product Management')
@section('page-subtitle', 'Manage your products and inventory')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
            <i class="fas fa-plus"></i> Add Product
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table id="productsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalTitle">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="product_id" name="product_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Product Category *</label>
                                <select class="form-select" id="category_id" name="category_id" required style="color: black !important;">
                                    <option value="" style="color: black !important;">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" style="color: black !important;">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                                <div class="form-text">Slug will be automatically generated from the product name</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="15" required></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="discount_price" class="form-label">Discount Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="discount_price" name="discount_price" 
                                           step="0.01" min="0">
                                </div>
                                <div class="form-text">Must be less than regular price</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control" id="stock" name="stock" 
                                       min="0" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="product_image" class="form-label">Product Image *</label>
                                <input type="file" class="form-control" id="product_image" name="product_image" 
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span id="submitBtnText">Save Product</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- CKEditor CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
$(document).ready(function() {
    // Initialize CKEditor
    let descriptionEditor;
    
    function initCKEditor() {
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#description'), {
                    height: '400px',
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'bulletedList', 'numberedList', '|',
                            'outdent', 'indent', '|',
                            'link', 'blockquote', 'imageUpload', 'insertTable', '|',
                            'undo', 'redo'
                        ]
                    },
                    image: {
                        toolbar: [
                            'imageTextAlternative', 'imageStyle:full', 'imageStyle:side'
                        ]
                    },
                    table: {
                        contentToolbar: [
                            'tableColumn', 'tableRow', 'mergeTableCells'
                        ]
                    }
                })
                .then(editor => {
                    descriptionEditor = editor;
                    
                    // Set maximum height for the editor
                    editor.editing.view.change(writer => {
                        writer.setStyle('height', '400px', editor.editing.view.document.getRoot());
                        writer.setStyle('max-height', '500px', editor.editing.view.document.getRoot());
                        writer.setStyle('overflow-y', 'auto', editor.editing.view.document.getRoot());
                    });
                    
                    editor.model.document.on('change:data', () => {
                        // Update the hidden textarea if needed
                        $('#description').val(editor.getData());
                    });
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                    // Fallback to regular textarea if CKEditor fails - ensure it's visible with 15 rows
                    $('#description').show().css('visibility', 'visible');
                });
        }
    }
    
    // Initialize CKEditor when modal is shown
    $('#productModal').on('shown.bs.modal', function() {
        if (!descriptionEditor) {
            setTimeout(initCKEditor, 100); // Small delay to ensure modal is fully rendered
        }
    });
    
    // Destroy CKEditor when modal is hidden to avoid conflicts
    $('#productModal').on('hidden.bs.modal', function() {
        if (descriptionEditor) {
            descriptionEditor.destroy().then(() => {
                descriptionEditor = null;
            }).catch(error => {
                console.error('Error destroying CKEditor:', error);
            });
        }
    });
    // Initialize DataTable
    var table = $('#productsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.products.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { 
                data: 'category', 
                name: 'category.name',
                render: function(data) {
                    return data ? data.name : 'N/A';
                }
            },
            { 
                data: 'price', 
                name: 'price',
                render: function(data, type, row) {
                    var price = '₹' + parseFloat(data).toFixed(2);
                    if (row.discount_price) {
                        price += '<br><small class="text-danger">₹' + parseFloat(row.discount_price).toFixed(2) + '</small>';
                    }
                    return price;
                }
            },
            { 
                data: 'stock', 
                name: 'stock',
                render: function(data) {
                    var badgeClass = data > 0 ? 'bg-success' : 'bg-danger';
                    var text = data > 0 ? 'In Stock (' + data + ')' : 'Out of Stock';
                    return '<span class="badge ' + badgeClass + '">' + text + '</span>';
                }
            },
            { 
                data: 'image', 
                name: 'image',
                render: function(data) {
                    return '<img src="' + (data ? '/storage/' + data : '/placeholder.jpg') + 
                           '" class="img-thumbnail" style="max-width: 80px; max-height: 60px;">';
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    var checked = data === 'active' ? 'checked' : '';
                    var disabled = row.category && row.category.status === 'inactive' ? 'disabled' : '';
                    var title = row.category && row.category.status === 'inactive' ? 'Category is inactive - cannot change product status' : '';
                    
                    return `
                        <div class="form-check form-switch">
                            <input class="form-check-input product-status-toggle" type="checkbox" 
                                   data-id="${row.id}" ${checked} ${disabled} title="${title}">
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
                        <button class="btn btn-sm btn-primary edit-product" data-id="${row.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-product" 
                                data-id="${row.id}" data-name="${row.name}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Reset modal when closed
    $('#productModal').on('hidden.bs.modal', function() {
        $('#productForm')[0].reset();
        $('#product_id').val('');
        $('#imagePreview').html('');
        $('#productModalTitle').text('Add Product');
        $('#submitBtnText').text('Save Product');
        $('#product_image').prop('required', true);
    });

    // Image preview
    $('#product_image').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">');
            };
            reader.readAsDataURL(file);
        }
    });

    // Edit product
    $(document).on('click', '.edit-product', function() {
        var id = $(this).data('id');
        
        $.get('{{ route("admin.products.edit", ":id") }}'.replace(':id', id))
            .done(function(data) {
                $('#product_id').val(data.id);
                $('#category_id').val(data.category_id);
                $('#product_name').val(data.name);
                $('#description').val(data.description);
                $('#price').val(data.price);
                $('#discount_price').val(data.discount_price);
                $('#stock_quantity').val(data.stock);
                $('#status').val(data.status);
                
                if (data.image) {
                    $('#imagePreview').html('<img src="/storage/' + data.image + '" class="img-thumbnail" style="max-width: 200px;">');
                }
                
                $('#productModalTitle').text('Edit Product');
                $('#submitBtnText').text('Update Product');
                $('#product_image').prop('required', false);
                
                // Show modal first, then set CKEditor content
                $('#productModal').modal('show');
                
                // Set CKEditor content after modal is shown and editor is ready
                $('#productModal').one('shown.bs.modal', function() {
                    setTimeout(function() {
                        if (descriptionEditor && data.description) {
                            descriptionEditor.setData(data.description);
                        }
                    }, 200);
                });
            });
    });

    // Delete product
    $(document).on('click', '.delete-product', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('Are you sure you want to delete the product "' + name + '"? This action can be undone.')) {
            $.ajax({
                url: '{{ route("admin.products.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload();
                    alert(response.success);
                },
                error: function() {
                    alert('Error occurred while deleting product.');
                }
            });
        }
    });

    // Toggle status with switch
    $(document).on('change', '.product-status-toggle', function() {
        var id = $(this).data('id');
        var checkbox = $(this);
        
        // Prevent toggle if disabled
        if (checkbox.prop('disabled')) {
            checkbox.prop('checked', !checkbox.prop('checked'));
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.products.toggle-status", ":id") }}'.replace(':id', id),
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
            error: function(xhr) {
                // Revert checkbox on error
                checkbox.prop('checked', !checkbox.prop('checked'));
                
                if (xhr.status === 422 && xhr.responseJSON.error) {
                    alert(xhr.responseJSON.error);
                } else {
                    alert('Error occurred while updating status.');
                }
            }
        });
    });

    // Submit form
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get CKEditor content before submitting
        if (descriptionEditor) {
            $('#description').val(descriptionEditor.getData());
        }
        
        var formData = new FormData(this);
        var id = $('#product_id').val();
        var url = id ? '{{ route("admin.products.update", ":id") }}'.replace(':id', id) 
                  : '{{ route("admin.products.store") }}';
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
                $('#productModal').modal('hide');
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
                    alert('Error occurred while saving product.');
                }
            }
        });
    });
});
</script>
@endpush
