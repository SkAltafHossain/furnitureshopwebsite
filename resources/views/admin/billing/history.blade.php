@extends('admin.layouts.app')

@section('title', 'Billing Management')
@section('page-title', 'Billing Management')
@section('page-subtitle', 'View and manage customer bills')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBillModal">
            <i class="fas fa-plus"></i> Create Bill
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table id="billsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Bill Number</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Create Bill Modal -->
<div class="modal fade" id="createBillModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Bill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column - Product Search and Cart -->
                    <div class="col-lg-8">
                        <!-- Product Search -->
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-search"></i> Search Products</h6>
                            </div>
                            <div class="card-body">
                                <div class="position-relative">
                                    <input type="text" 
                                           class="form-control" 
                                           id="productSearch" 
                                           placeholder="Search products by name..." 
                                           autocomplete="off">
                                    <div id="searchResults" class="search-dropdown d-none"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Items -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-shopping-cart"></i> Cart Items</h6>
                                <span class="badge bg-primary" id="cartCount">0 items</span>
                            </div>
                            <div class="card-body">
                                <div id="cartItems">
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-shopping-basket fa-2x mb-2"></i>
                                        <p class="mb-0">No items in cart. Search and add products above.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Customer Details and Bill Summary -->
                    <div class="col-lg-4">
                        <!-- Customer Details -->
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-user"></i> Customer Details</h6>
                            </div>
                            <div class="card-body">
                                <form id="billingForm">
                                    <div class="mb-2">
                                        <label class="form-label small">Customer Name *</label>
                                        <input type="text" 
                                               class="form-control form-control-sm" 
                                               id="customerName" 
                                               name="customer_name" 
                                               required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Phone Number *</label>
                                        <input type="tel" 
                                               class="form-control form-control-sm" 
                                               id="customerPhone" 
                                               name="customer_phone" 
                                               required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Email (Optional)</label>
                                        <input type="email" 
                                               class="form-control form-control-sm" 
                                               id="customerEmail" 
                                               name="customer_email">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Payment Method *</label>
                                        <select class="form-select form-select-sm" id="paymentMethod" name="payment_method" required>
                                            <option value="cash">Cash</option>
                                            <option value="card">Card</option>
                                            <option value="upi">UPI</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Notes (Optional)</label>
                                        <textarea class="form-control form-control-sm" 
                                                  id="notes" 
                                                  name="notes" 
                                                  rows="2"></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Bill Summary -->
                        <div class="card bill-summary">
                            <div class="card-body">
                                <h6 class="card-title mb-3"><i class="fas fa-receipt"></i> Bill Summary</h6>
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span>MRP Total:</span>
                                    <span id="subtotal">₹0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 text-danger small">
                                    <span>Total Discount:</span>
                                    <span id="totalDiscount">0.00</span>
                                </div>
                                <hr class="bg-light">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold fs-5" id="totalAmount">₹0.00</span>
                                </div>
                                <button type="button" 
                                        class="btn btn-light btn-sm w-100" 
                                        id="createBillBtn"
                                        disabled>
                                    <i class="fas fa-file-invoice"></i> Create Bill
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Send Bill Modal -->
<div class="modal fade" id="sendBillModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Bill to Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Choose how you want to send the bill:</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="sendBillMethod('email')">
                        <i class="fas fa-envelope"></i> Send via Email
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="sendBillMethod('whatsapp')">
                        <i class="fab fa-whatsapp"></i> Send via WhatsApp
                    </button>
                    <button type="button" class="btn btn-outline-info" onclick="sendBillMethod('sms')">
                        <i class="fas fa-sms"></i> Send via SMS
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send Bill Modal -->
<div class="modal fade" id="sendBillModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Bill to Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Choose how you want to send the bill:</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="sendBillMethod('email')">
                        <i class="fas fa-envelope"></i> Send via Email
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="sendBillMethod('whatsapp')">
                        <i class="fab fa-whatsapp"></i> Send via WhatsApp
                    </button>
                    <button type="button" class="btn btn-outline-info" onclick="sendBillMethod('sms')">
                        <i class="fas fa-sms"></i> Send via SMS
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-search-result {
        cursor: pointer;
        transition: all 0.2s;
    }
    .product-search-result:hover {
        background-color: #f8f9fa;
    }
    .cart-item {
        transition: all 0.3s;
    }
    .quantity-btn {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    .quantity-btn:hover {
        background: #e9ecef;
    }
    .quantity-input {
        width: 40px;
        text-align: center;
        border: 1px solid #dee2e6;
        border-left: none;
        border-right: none;
        height: 30px;
    }
    .stock-warning {
        color: #dc3545;
        font-size: 0.75rem;
    }
    .bill-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .search-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0 0 0.375rem 0.375rem;
        max-height: 250px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .search-dropdown .dropdown-item {
        padding: 0.5rem 1rem;
        border-bottom: 1px solid #f8f9fa;
    }
    .search-dropdown .dropdown-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<script>
let currentBillId = null;
let cart = [];

// DataTable initialization
var table;

$(document).ready(function() {
    table = $('#billsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.billing.data") }}',
        columns: [
            { 
                data: 'bill_number', 
                name: 'bill_number',
                render: function(data) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { 
                data: 'customer_name', 
                name: 'customer_name',
                render: function(data, type, row) {
                    return '<div>' + data + '</div><small class="text-muted">' + row.customer_phone + '</small>';
                }
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleString();
                }
            },
            { 
                data: 'items_count', 
                name: 'items_count',
                render: function(data) {
                    return data + ' item(s)';
                }
            },
            { 
                data: 'total_amount', 
                name: 'total_amount',
                render: function(data) {
                    return '<strong>₹' + parseFloat(data).toFixed(2) + '</strong>';
                }
            },
            { 
                data: 'payment_method', 
                name: 'payment_method',
                render: function(data) {
                    var badgeClass = data === 'cash' ? 'bg-success' : (data === 'card' ? 'bg-primary' : 'bg-info');
                    return '<span class="badge ' + badgeClass + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                }
            },
            { 
                data: 'is_sent', 
                name: 'is_sent',
                render: function(data) {
                    if (data) {
                        return '<span class="badge bg-success"><i class="fas fa-check"></i> Sent</span>';
                    } else {
                        return '<span class="badge bg-secondary"><i class="fas fa-clock"></i> Pending</span>';
                    }
                }
            },
            { 
                data: 'created_by', 
                name: 'created_by',
                render: function(data) {
                    return '<small>' + (data || 'System') + '</small>';
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var actions = '<a href="/admin/billing/' + row.id + '" class="btn btn-sm btn-info" title="View Details"><i class="fas fa-eye"></i></a>';
                    actions += '<a href="/admin/billing/' + row.id + '/download" class="btn btn-sm btn-primary" title="Download PDF" target="_blank"><i class="fas fa-download"></i></a>';
                    
                    if (!row.is_sent) {
                        actions += '<button class="btn btn-sm btn-success send-bill" data-id="' + row.id + '" title="Send Bill"><i class="fas fa-paper-plane"></i></button>';
                    }
                    
                    return actions;
                }
            }
        ],
        order: [[2, 'desc']],
        pageLength: 25
    });

    // Reset modal when closed
    $('#createBillModal').on('hidden.bs.modal', function() {
        resetBillingForm();
    });

    // Send bill button
    $(document).on('click', '.send-bill', function() {
        var id = $(this).data('id');
        sendBill(id);
    });
});

// Billing Modal Functions
const productSearch = document.getElementById('productSearch');
const searchResults = document.getElementById('searchResults');
let searchTimeout;

productSearch.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
        searchResults.classList.add('d-none');
        return;
    }
    
    searchTimeout = setTimeout(() => {
        searchProducts(query);
    }, 300);
});

function searchProducts(query) {
    fetch(`/admin/billing/search?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(products => {
            displaySearchResults(products);
        })
        .catch(error => console.error('Error searching products:', error));
}

function displaySearchResults(products) {
    if (products.length === 0) {
        searchResults.innerHTML = '<div class="dropdown-item text-muted">No products found</div>';
        searchResults.classList.remove('d-none');
        return;
    }
    
    const html = products.map(product => {
        const price = parseFloat(product.discount_price || product.price);
        const originalPrice = parseFloat(product.price);
        const discount = originalPrice - price;
        const discountPercent = originalPrice > 0 ? ((discount / originalPrice) * 100).toFixed(1) : 0;
        
        return `
        <div class="dropdown-item product-search-result" onclick="addToCart(${product.id})">
            <div class="d-flex align-items-center">
                ${product.image ? `<img src="/storage/${product.image}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">` : '<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-image text-muted"></i></div>'}
                <div class="flex-grow-1">
                    <h6 class="mb-0 small">${product.name}</h6>
                    <small class="text-muted">Stock: ${product.stock}</small>
                </div>
                <div class="text-end">
                    ${discount > 0 ? `<div class="text-decoration-line-through text-muted small">₹${originalPrice.toFixed(2)}</div>` : ''}
                    <strong class="${discount > 0 ? 'text-success' : ''} small">₹${price.toFixed(2)}</strong>
                    ${discount > 0 ? `<div class="badge bg-success small">${discountPercent}% OFF</div>` : ''}
                </div>
            </div>
        </div>
        `;
    }).join('');
    
    searchResults.innerHTML = html;
    searchResults.classList.remove('d-none');
}

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    if (!productSearch.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.classList.add('d-none');
    }
});

// Add to Cart
function addToCart(productId) {
    fetch(`/admin/billing/product/${productId}`)
        .then(response => response.json())
        .then(product => {
            const existingItem = cart.find(item => item.id === product.id);
            
            if (existingItem) {
                if (existingItem.quantity < product.stock) {
                    existingItem.quantity++;
                } else {
                    alert('Maximum stock reached for this product');
                    return;
                }
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.discount_price || product.price),
                    originalPrice: parseFloat(product.price),
                    stock: product.stock,
                    quantity: 1,
                    image: product.image
                });
            }
            
            updateCart();
            productSearch.value = '';
            searchResults.classList.add('d-none');
        })
        .catch(error => console.error('Error adding to cart:', error));
}

// Update Cart
function updateCart() {
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    
    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="text-center text-muted py-3">
                <i class="fas fa-shopping-basket fa-2x mb-2"></i>
                <p class="mb-0">No items in cart. Search and add products above.</p>
            </div>
        `;
        cartCount.textContent = '0 items';
        document.getElementById('createBillBtn').disabled = true;
        updateBillSummary();
        return;
    }
    
    const html = cart.map((item, index) => {
        const discount = item.originalPrice - item.price;
        const discountPercent = item.originalPrice > 0 ? ((discount / item.originalPrice) * 100).toFixed(1) : 0;
        const itemTotal = item.price * item.quantity;
        const mrpTotal = item.originalPrice * item.quantity;
        const totalDiscount = discount * item.quantity;
        
        return `
        <div class="cart-item card mb-2">
            <div class="card-body p-2">
                <div class="d-flex align-items-center">
                    ${item.image ? `<img src="/storage/${item.image}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">` : '<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-image text-muted"></i></div>'}
                    <div class="flex-grow-1">
                        <h6 class="mb-0 small">${item.name}</h6>
                        <div class="text-muted small">
                            <span class="text-decoration-line-through">₹${item.originalPrice.toFixed(2)}</span> 
                            <span class="text-success fw-bold">₹${item.price.toFixed(2)}</span>
                            ${discount > 0 ? `<span class="badge bg-success ms-1 small">${discountPercent}% OFF</span>` : ''}
                        </div>
                        ${item.quantity >= item.stock ? '<div class="stock-warning"><i class="fas fa-exclamation-triangle"></i> Max stock reached</div>' : ''}
                    </div>
                    <div class="d-flex align-items-center">
                        <button type="button" class="quantity-btn" onclick="updateQuantity(${index}, -1)">-</button>
                        <input type="number" class="quantity-input" value="${item.quantity}" readonly>
                        <button type="button" class="quantity-btn" onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                    <div class="ms-2 text-end">
                        <div class="text-decoration-line-through text-muted small">₹${mrpTotal.toFixed(2)}</div>
                        <strong class="text-success small">₹${itemTotal.toFixed(2)}</strong>
                        ${totalDiscount > 0 ? `<div class="text-danger small">Save ${totalDiscount.toFixed(2)}</div>` : ''}
                        <button type="button" class="btn btn-sm btn-outline-danger ms-1" onclick="removeFromCart(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;
    }).join('');
    
    cartItems.innerHTML = html;
    cartCount.textContent = `${cart.length} item${cart.length > 1 ? 's' : ''}`;
    document.getElementById('createBillBtn').disabled = false;
    updateBillSummary();
}

// Update Quantity
function updateQuantity(index, change) {
    const item = cart[index];
    const newQuantity = item.quantity + change;
    
    if (newQuantity < 1) {
        removeFromCart(index);
        return;
    }
    
    if (newQuantity > item.stock) {
        alert('Maximum stock reached for this product');
        return;
    }
    
    item.quantity = newQuantity;
    updateCart();
}

// Remove from Cart
function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

// Update Bill Summary
function updateBillSummary() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const mrpTotal = cart.reduce((sum, item) => sum + (item.originalPrice * item.quantity), 0);
    const totalDiscount = mrpTotal - subtotal;
    const total = subtotal;
    
    document.getElementById('subtotal').textContent = `₹${mrpTotal.toFixed(2)}`;
    document.getElementById('totalDiscount').textContent = `${totalDiscount.toFixed(2)}`;
    document.getElementById('totalAmount').textContent = `₹${total.toFixed(2)}`;
}

// Reset Billing Form
function resetBillingForm() {
    document.getElementById('billingForm').reset();
    cart = [];
    updateCart();
    productSearch.value = '';
    searchResults.classList.add('d-none');
}

// Create Bill
document.getElementById('createBillBtn').addEventListener('click', function() {
    const form = document.getElementById('billingForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    if (cart.length === 0) {
        alert('Please add items to cart');
        return;
    }
    
    const formData = new FormData(form);
    const items = cart.map(item => ({
        product_id: item.id,
        quantity: item.quantity,
        price: item.originalPrice
    }));
    
    const billData = {
        customer_name: formData.get('customer_name'),
        customer_phone: formData.get('customer_phone'),
        customer_email: formData.get('customer_email'),
        items: items,
        payment_method: formData.get('payment_method'),
        notes: formData.get('notes')
    };
    
    fetch(`{{ route('admin.billing.store') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(billData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('createBillModal'));
            modal.hide();
            
            // Reload table
            table.ajax.reload();
            
            // Reset form
            resetBillingForm();
            
            alert('Bill created successfully!');
        } else {
            alert('Error creating bill: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error creating bill:', error);
        alert('Error creating bill. Please try again.');
    });
});

// Send Bill Functions
function sendBill(billId) {
    currentBillId = billId;
    const modal = new bootstrap.Modal(document.getElementById('sendBillModal'));
    modal.show();
}

function sendBillMethod(method) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('sendBillModal'));
    modal.hide();
    
    fetch(`/admin/billing/${currentBillId}/send`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            send_method: method
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Bill sent successfully via ${method}!`);
            table.ajax.reload();
        } else {
            alert('Error sending bill: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error sending bill:', error);
        alert('Error sending bill. Please try again.');
    });
}
</script>
@endpush