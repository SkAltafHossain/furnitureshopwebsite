# Local Shop E-commerce Admin Panel (Laravel)

## Purpose

This document describes **ONLY the Admin Panel** for a **single local shop e-commerce system**.
It is strictly limited to **admin-side features and UI**.

🚫 No public APIs
🚫 No website logic
🚫 No mobile app logic

---

## Tech Stack (Admin Only)

* Laravel 10+
* PHP 8.2+
* MySQL
* Blade Templates
* Bootstrap 5
* Bootstrap Modals (Create / Edit)
* jQuery
* DataTables (server-side)

---

## Admin Modules

### 1. Admin Authentication

* Admin login/logout
* Session-based authentication
* Admin only

---

### 2. Banner Management (Modal Based)

**Fields**

* id
* title
* subtitle
* banner_image
* status (active/inactive)

**Admin Features**

* Banner list (DataTable)
* Add banner (Bootstrap modal)
* Edit banner (same modal)
* Activate / Deactivate banner

---

### 3. Product Category Management (Modal Based)

**Fields**

* id
* category_name (category slug use name for slug)
* category_image (tag use for name)
* status

**Admin Features**

* List categories
* Create category (modal)
* Edit category (modal)
* Soft delete

---

### 4. Product Management (Modal Based)

**Fields**

* id
* category_id
* product_name (for slug use product name)
* description
* price
* discount_price
* stock_quantity
* product_image
* status

**Admin Features**

* Product list with search
* Add product (modal)
* Edit product (modal)
* Image preview
* Stock control

---

### 5. Order Management (Admin View)

**Fields**

* order_id
* customer_name
* total_amount
* payment_type (COD / Online)
* order_status
* created_at

**Admin Features**

* View order list
* View order details (modal)
* Update order status

---

### 6. User Management

**Fields**

* user_id
* name
* email
* phone
* status (active / blocked)

**Admin Features**

* View users
* Block / Unblock user
* View user order history

---

### 7. Static Pages Management and admin panel add this thing using ck editor (CMS)

Manage all informational pages from Admin Panel.

#### Pages Included

* About Us
* Terms & Conditions
* Privacy Policy

**Fields**

* title
* content (Rich Text)
* last_updated
* status

**Admin Features**

* Edit content using WYSIWYG editor
* Publish / Unpublish

---

## UI & UX Rules

* All Create / Edit actions must use **Bootstrap Modals**
* No separate create pages
* Validation errors shown inside modal

---

## AI Prompt Instruction

"Create a Laravel-based admin panel for a single local shop e-commerce system, limited strictly to admin functionality, using Bootstrap 5 modals for managing banners, categories, products, orders, users, and static pages (About, Terms, Privacy)."
