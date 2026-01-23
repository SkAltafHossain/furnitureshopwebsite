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
        Route::delete('/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
    });
});

Route::get('/', function () {
    return redirect()->route('admin.login');
});
