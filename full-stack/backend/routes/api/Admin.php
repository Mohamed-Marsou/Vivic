<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;


Route::get('/admins', [AdminController::class, 'index'])->name('admin.index');

Route::post('/admin', [AdminController::class, 'store'])->name('admin.store');


Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');

Route::get('/woo/products', [ProductController::class, 'syncAllProductsFromWooCommerce'])->name('products.syncAllProductsFromWooCommerce');

Route::get('/woo/filter/products', [ProductController::class, 'syncProductsFromWooCommerce'])->name('products.syncProductsFromWooCommerce');

Route::get('/dashboard/info', [AdminController::class, 'GetDashInfo'])->name('admin.GetDashInfo');

Route::get('/dashboard/orders', [AdminController::class, 'getOrders'])->name('admin.getOrders');

Route::get('/dashboard/orders/recent', [AdminController::class, 'getRecentOrders'])->name('admin.getRecentOrders');

Route::get('/dashboard/product/search', [AdminController::class, 'ProductSearch'])->name('admin.ProductSearch');

// Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');