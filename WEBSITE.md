# Local Shop E-commerce Website (Laravel)

## Purpose

Customer-facing **website built fully in Laravel** for a **single local shop e-commerce system**.
This document covers **ONLY website features** (no admin panel, no mobile app).

---

## Tech Stack (Website Only)

* Laravel 10+
* PHP 8.2+
* Blade Templates
* Bootstrap 5
* jQuery
* MySQL

---

## Website Pages & Features

### 1. Home Page

* Hero banner slider
* Featured categories
* Latest products
* Shop highlights

---

### 2. Product Listing Page

* Category-wise products
* Price display (discount supported)
* Pagination

---

### 3. Product Details Page

* Product image
* Description
* Price & discount
* Stock availability
* Add to cart button

---

### 4. Cart Page

* View cart items
* Update quantity
* Remove product
* Cart total

---

### 5. Checkout Page
* Login & register Model 
* Customer details (name, phone, address)
* Order summary
* Payment method (COD / Online)
* Place order

---

### 6. Authentication Pages (If user check out then only use authentication)

* User registration
* User login
* Logout

---

### 7. My Account

* Profile details
* My orders list
* Order details

---

### 8. Static Pages

* About Us
* Terms & Conditions
* Privacy Policy

---

## Website Components

* Header with categories menu
* Footer with static page links
* Flash messages (success / error)

---

## Session & Cart Handling

* Laravel session-based cart
* Guest cart support
* Cart persists after login

---

## Security & Validation

* CSRF protection
* Server-side form validation
* Auth middleware for protected pages

---

## AI Prompt Instruction

"Create a Laravel-based e-commerce website for a single local shop using Blade templates and Bootstrap, including product browsing, cart, checkout, authentication, orders, banners, and static pages (About, Terms, Privacy). Do not include admin panel or mobile app."
