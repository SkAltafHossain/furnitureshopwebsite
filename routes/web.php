<?php

use Illuminate\Support\Facades\Route;

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes for admin
    Route::middleware('guest')->group(function () {
        Route::get('/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'login']);
    });

    // Authenticated admin routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('logout');

        // Banner Management
        Route::get('/banners', [App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banners.index');
        Route::get('/banners/data', [App\Http\Controllers\Admin\BannerController::class, 'getData'])->name('banners.data');
        Route::post('/banners', [App\Http\Controllers\Admin\BannerController::class, 'store'])->name('banners.store');
        Route::get('/banners/{id}/edit', [App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('banners.edit');
        Route::put('/banners/{id}', [App\Http\Controllers\Admin\BannerController::class, 'update'])->name('banners.update');
        Route::patch('/banners/{id}/toggle-status', [App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])->name('banners.toggle-status');

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

Route::get('/', function () {
    return redirect()->route('admin.login');
});
