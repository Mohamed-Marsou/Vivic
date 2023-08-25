<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// get Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('category.index');
// get Category products
Route::get('/products/category/{id}', [CategoryController::class, 'getCategoryProducts'])->name('category.getCategoryProducts');

// get all products 
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// get paginate products desc 
Route::get('/products/new-arrivals', [ProductController::class, 'newProducts'])->name('products.newProducts');

// get hot products desc 
Route::get('/products/high-rating', [ProductController::class, 'getHighRating'])->name('products.getHighRating');

// get hot product DATA 
Route::get('/product/{slug}', [ProductController::class, 'getProduct'])->name('products.getProduct');

// get products based on range 
Route::get('/products/range/{minPrice}', [ProductController::class, 'getRange'])->name('products.getRange');
// get products based on filter 
Route::get('/products/filterd', [ProductController::class, 'getFilteredProducts'])->name('products.getFilteredProducts');
