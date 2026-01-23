@extends('admin.layouts.app')

@section('title', 'Static Pages Management')
@section('page-title', 'Static Pages Management')
@section('page-subtitle', 'Manage informational pages with rich text editor')

@section('content')
<div class="card">
    <div class="card-body">
        <table id="pagesTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Edit Page Modal -->
<div class="modal fade" id="editPageModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPageForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="page_id" name="page_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Page Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="published">Published</option>
                                    <option value="unpublished">Unpublished</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_updated" class="form-label">Last Updated</label>
                                <input type="text" class="form-control" id="last_updated" readonly>
                                <div class="form-text">Automatically updated when page is saved</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="content" class="form-label">Content *</label>
                                <textarea class="form-control" id="content" name="content" rows="20" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Page</button>
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
    let contentEditor;
    
    function initCKEditor() {
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#content'), {
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
                    contentEditor = editor;
                    
                    // Set maximum height for the editor
                    editor.editing.view.change(writer => {
                        writer.setStyle('height', '400px', editor.editing.view.document.getRoot());
                        writer.setStyle('max-height', '500px', editor.editing.view.document.getRoot());
                        writer.setStyle('overflow-y', 'auto', editor.editing.view.document.getRoot());
                    });
                    
                    editor.model.document.on('change:data', () => {
                        // Update the hidden textarea if needed
                        $('#content').val(editor.getData());
                    });
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                    // Fallback to regular textarea if CKEditor fails
                    $('#content').show().css('visibility', 'visible');
                });
        }
    }
    
    // Initialize CKEditor when modal is shown
    $('#editPageModal').on('shown.bs.modal', function() {
        if (!contentEditor) {
            setTimeout(initCKEditor, 100);
        }
    });
    
    // Destroy CKEditor when modal is hidden to avoid conflicts
    $('#editPageModal').on('hidden.bs.modal', function() {
        if (contentEditor) {
            contentEditor.destroy().then(() => {
                contentEditor = null;
            }).catch(error => {
                console.error('Error destroying CKEditor:', error);
            });
        }
    });

    // Initialize DataTable
    var table = $('#pagesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.static-pages.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    var checked = data === 'published' ? 'checked' : '';
                    return `
                        <div class="form-check form-switch">
                            <input class="form-check-input page-status-toggle" type="checkbox" 
                                   data-id="${row.id}" ${checked}>
                        </div>
                    `;
                }
            },
            { 
                data: 'last_updated', 
                name: 'last_updated',
                render: function(data) {
                    return data ? new Date(data).toLocaleString() : 'Never';
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary edit-page" data-id="${row.id}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    `;
                }
            }
        ]
    });

    // Reset modal when closed
    $('#editPageModal').on('hidden.bs.modal', function() {
        $('#editPageForm')[0].reset();
        $('#page_id').val('');
    });

    // Edit page
    $(document).on('click', '.edit-page', function() {
        var id = $(this).data('id');
        
        $.get('{{ route("admin.static-pages.edit", ":id") }}'.replace(':id', id))
            .done(function(data) {
                $('#page_id').val(data.id);
                $('#title').val(data.title);
                $('#status').val(data.status);
                $('#last_updated').val(data.last_updated ? new Date(data.last_updated).toLocaleString() : 'Never');
                
                // Show modal first, then set CKEditor content
                $('#editPageModal').modal('show');
                
                // Set CKEditor content after modal is shown and editor is ready
                $('#editPageModal').one('shown.bs.modal', function() {
                    setTimeout(function() {
                        if (contentEditor && data.content) {
                            contentEditor.setData(data.content);
                        }
                    }, 200);
                });
            });
    });

    // Toggle page status
    $(document).on('change', '.page-status-toggle', function() {
        var id = $(this).data('id');
        var checkbox = $(this);
        
        $.ajax({
            url: '{{ route("admin.static-pages.toggle-status", ":id") }}'.replace(':id', id),
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
                alert('Error occurred while updating page status.');
            }
        });
    });

    // Submit form
    $('#editPageForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get CKEditor content before submitting
        if (contentEditor) {
            $('#content').val(contentEditor.getData());
        }
        
        var formData = new FormData(this);
        var id = $('#page_id').val();
        
        $.ajax({
            url: '{{ route("admin.static-pages.update", ":id") }}'.replace(':id', id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#editPageModal').modal('hide');
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
                    alert('Error occurred while saving page.');
                }
            }
        });
    });
});
</script>
@endpush
