<?php

use Illuminate\Support\Facades\Route;

// Website Routes (Customer-facing)
Route::get('/', [App\Http\Controllers\Website\HomeController::class, 'index'])->name('home');
Route::get('/products', [App\Http\Controllers\Website\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [App\Http\Controllers\Website\ProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [App\Http\Controllers\Website\CategoryController::class, 'show'])->name('category.show');
Route::get('/cart', [App\Http\Controllers\Website\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [App\Http\Controllers\Website\CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update', [App\Http\Controllers\Website\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove', [App\Http\Controllers\Website\CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [App\Http\Controllers\Website\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [App\Http\Controllers\Website\CheckoutController::class, 'process'])->name('checkout.process');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Website\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\Website\AuthController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Website\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [App\Http\Controllers\Website\AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Website\AuthController::class, 'logout'])->name('logout');
    Route::get('/my-account', [App\Http\Controllers\Website\AccountController::class, 'index'])->name('account.index');
    Route::put('/my-account', [App\Http\Controllers\Website\AccountController::class, 'update'])->name('account.update');
    Route::put('/change-password', [App\Http\Controllers\Website\AccountController::class, 'changePassword'])->name('account.change-password');
    Route::get('/my-addresses', [App\Http\Controllers\Website\AccountController::class, 'addresses'])->name('account.addresses');
    Route::delete('/addresses/{id}', [App\Http\Controllers\Website\AccountController::class, 'deleteAddress'])->name('account.delete-address');
    Route::patch('/addresses/{id}/set-default', [App\Http\Controllers\Website\AccountController::class, 'setDefaultAddress'])->name('account.set-default-address');
    Route::get('/my-orders', [App\Http\Controllers\Website\AccountController::class, 'orders'])->name('account.orders');
    Route::get('/my-orders/{id}', [App\Http\Controllers\Website\AccountController::class, 'orderDetails'])->name('account.order-details');
    Route::post('/my-orders/{id}/cancel', [App\Http\Controllers\Website\AccountController::class, 'cancelOrder'])->name('account.order-cancel');
    Route::post('/addresses', [App\Http\Controllers\Website\CheckoutController::class, 'storeAddress'])->name('addresses.store');
    
    // Wishlist Routes
    Route::post('/wishlist/toggle', [App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{id}', [App\Http\Controllers\Website\AccountController::class, 'removeFromWishlist'])->name('wishlist.remove');
});

// Static Pages
Route::get('/about-us', [App\Http\Controllers\Website\PageController::class, 'about'])->name('page.about');
Route::get('/terms', [App\Http\Controllers\Website\PageController::class, 'terms'])->name('page.terms');
Route::get('/privacy', [App\Http\Controllers\Website\PageController::class, 'privacy'])->name('page.privacy');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes for admin
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'login']);
    });

    // Authenticated admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('logout');

        // Banner Management
        Route::get('/banners', [App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banners.index');
        Route::get('/banners/data', [App\Http\Controllers\Admin\BannerController::class, 'getData'])->name('banners.data');
        Route::post('/banners', [App\Http\Controllers\Admin\BannerController::class, 'store'])->name('banners.store');
        Route::get('/banners/{id}/edit', [App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('banners.edit');
        Route::put('/banners/{id}', [App\Http\Controllers\Admin\BannerController::class, 'update'])->name('banners.update');
        Route::patch('/banners/{id}/toggle-status', [App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
        Route::delete('/banners/{id}', [App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('banners.destroy');

        // Category Management
        Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/data', [App\Http\Controllers\Admin\CategoryController::class, 'getData'])->name('categories.data');
        Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
        Route::patch('/categories/{id}/toggle-status', [App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::delete('/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

        // Product Management
        Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
        Route::get('/products/data', [App\Http\Controllers\Admin\ProductController::class, 'getData'])->name('products.data');
        Route::post('/products', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{id}/toggle-status', [App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::delete('/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('products.destroy');

        // Order Management
        Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/data', [App\Http\Controllers\Admin\OrderController::class, 'getData'])->name('orders.data');
        Route::get('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{id}/update-status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
        
        // Real-time updates
        Route::get('/orders/updates', [App\Http\Controllers\Admin\OrderUpdatesController::class, 'stream'])->name('orders.updates');

        // User Management
        Route::get('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/data', [App\Http\Controllers\Admin\UserManagementController::class, 'getData'])->name('users.data');
        Route::patch('/users/{id}/toggle-status', [App\Http\Controllers\Admin\UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/users/{id}/order-history', [App\Http\Controllers\Admin\UserManagementController::class, 'showOrderHistory'])->name('users.order-history');

        // Static Pages Management
        Route::get('/static-pages', [App\Http\Controllers\Admin\StaticPageController::class, 'index'])->name('static-pages.index');
        Route::get('/static-pages/data', [App\Http\Controllers\Admin\StaticPageController::class, 'getData'])->name('static-pages.data');
        Route::get('/static-pages/{id}/edit', [App\Http\Controllers\Admin\StaticPageController::class, 'edit'])->name('static-pages.edit');
        Route::put('/static-pages/{id}', [App\Http\Controllers\Admin\StaticPageController::class, 'update'])->name('static-pages.update');
        Route::patch('/static-pages/{id}/toggle-status', [App\Http\Controllers\Admin\StaticPageController::class, 'toggleStatus'])->name('static-pages.toggle-status');
    });
});

