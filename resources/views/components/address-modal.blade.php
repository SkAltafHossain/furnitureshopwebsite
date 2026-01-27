<!-- Add Address Modal Component -->
<div class="modal fade" id="{{ $modalId ?? 'addAddressModal' }}" tabindex="-1" aria-labelledby="{{ $modalId ?? 'addAddressModal' }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="{{ $modalId ?? 'addAddressModal' }}Label">
                    <i class="fas fa-map-marker-alt me-2"></i>{{ $title ?? 'Add New Address' }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="{{ $formId ?? 'addAddressForm' }}">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address Type</label>
                            <select class="form-select form-select-lg" name="type" id="{{ $modalId ?? 'addAddressModal' }}_type" required>
                                <option value="">Select Type</option>
                                <option value="home">Home</option>
                                <option value="office">Office</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Postal Code</label>
                            <input type="text" class="form-control form-control-lg" name="postal_code" id="{{ $modalId ?? 'addAddressModal' }}_postal_code" required>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="form-label fw-semibold">Address Line 1 *</label>
                        <input type="text" class="form-control form-control-lg" name="address_line1" id="{{ $modalId ?? 'addAddressModal' }}_address_line1" required>
                    </div>
                    
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Address Line 2 (Optional)</label>
                        <input type="text" class="form-control form-control-lg" name="address_line2" id="{{ $modalId ?? 'addAddressModal' }}_address_line2">
                    </div>
                    
                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">City *</label>
                            <input type="text" class="form-control form-control-lg" name="city" id="{{ $modalId ?? 'addAddressModal' }}_city" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">State *</label>
                            <input type="text" class="form-control form-control-lg" name="state" id="{{ $modalId ?? 'addAddressModal' }}_state" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Country *</label>
                            <input type="text" class="form-control form-control-lg" name="country" id="{{ $modalId ?? 'addAddressModal' }}_country" value="India" required>
                        </div>
                    </div>
                    
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_default" id="{{ $modalId ?? 'addAddressModal' }}_is_default">
                        <label class="form-check-label fw-semibold" for="{{ $modalId ?? 'addAddressModal' }}_is_default">
                            Set as default address
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light p-4">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-lg px-5" style="min-height: 48px;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-lg px-5" style="min-height: 48px;">
                            <i class="fas fa-save me-2"></i>{{ $submitText ?? 'Save Address' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Component-specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const formId = '{{ $formId ?? "addAddressForm" }}';
    const modalId = '{{ $modalId ?? "addAddressModal" }}';
    
    // Handle form submission
    $('#' + formId).submit(function(e) {
        e.preventDefault();
        
        const formData = {
            type: $('#' + modalId + '_type').val(),
            address_line1: $('#' + modalId + '_address_line1').val(),
            address_line2: $('#' + modalId + '_address_line2').val(),
            city: $('#' + modalId + '_city').val(),
            state: $('#' + modalId + '_state').val(),
            postal_code: $('#' + modalId + '_postal_code').val(),
            country: $('#' + modalId + '_country').val(),
            is_default: $('#' + modalId + '_is_default').is(':checked') ? 1 : 0
        };
        
        // Debug: Log form data
        console.log('Address form data:', formData);
        console.log('Checkbox checked:', $('#' + modalId + '_is_default').is(':checked'));
        
        // Basic validation
        if (!formData.type || !formData.address_line1 || !formData.city || !formData.state || !formData.postal_code) {
            showToast('Please fill in all required address fields', 'error');
            return;
        }
        
        // Disable submit button and show loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');
        
        $.ajax({
            url: '{{ $submitUrl ?? route("addresses.store") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#' + modalId).modal('hide');
                    // Reset form
                    $('#' + formId)[0].reset();
                    // Custom callback or reload
                    @if(isset($callback))
                        {{ $callback }}(response);
                    @else
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    @endif
                } else {
                    showToast(response.message || 'Failed to add address', 'error');
                }
            },
            error: function(xhr) {
                let message = 'Failed to add address';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                showToast(message, 'error');
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Reset modal form when modal is hidden
    $('#' + modalId).on('hidden.bs.modal', function() {
        $('#' + formId)[0].reset();
        // Remove any validation errors
        $('.is-invalid').removeClass('is-invalid');
    });
});
</script>
