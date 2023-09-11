<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('category.index');

// Category Products
Route::get('/products/category/{id}', [CategoryController::class, 'getCategoryProducts'])->name('category.getCategoryProducts');

// All Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// New Arrivals Products
Route::get('/products/new-arrivals', [ProductController::class, 'newProducts'])->name('products.newProducts');

// High Rating Products
Route::get('/products/high-rating', [ProductController::class, 'getHighRating'])->name('products.getHighRating');

// Product Details
Route::get('/product/{slug}', [ProductController::class, 'getProduct'])->name('products.getProduct');

// Products Based on Price Range
Route::get('/products/range/{minPrice}', [ProductController::class, 'getRange'])->name('products.getRange');

// Filtered Products
Route::get('/products/filterd', [ProductController::class, 'getFilteredProducts'])->name('products.getFilteredProducts');

// Add Product to Wishlist
Route::post('/product/wishlist', [ProductController::class, 'addToWishlist'])->name('products.addToWishlist');

// Get User Wishlist Products
Route::get('/get/wishlist-products/{id}', [ProductController::class, 'getUserWishListProducts'])->name('products.getUserWishListProducts');

// Add Product to User Cart
Route::post('product/cart', [ProductController::class, 'addToCart'])->name('products.addToCart');

// Get Wishlist Products
Route::get('/get/products', [ProductController::class, 'getProducts'])->name('products.getProducts');

// Get User InCart Products
Route::get('/get/cart-products/{id}', [ProductController::class, 'getInCartProducts'])->name('products.getInCartProducts');

// decrase User InCart Product quantity
Route::put('/product/cart/decrease/{userId}/{id}', [ProductController::class, 'decreaseCartProductQuantity'])->name('products.decreaseCartProductQuantity');
// incrase User InCart Product quantity
Route::put('/product/cart/increase/{userId}/{id}', [ProductController::class, 'increaseCartProductQuantity'])->name('products.increaseCartProductQuantity');

// Remove User InCart Product
Route::delete('/product/cart/{userId}/{productId}', [ProductController::class, 'removeInCartProducts'])->name('products.removeInCartProducts');
// Remove User wishlist Product
Route::delete('/product/wishlist/{userId}/{productId}', [ProductController::class, 'removeWishlistProducts'])->name('products.removeWishlistProducts');

// clear user Cart 
Route::delete('/user/cart/clear/{id}', [ProductController::class, 'clearUserCart'])->name('Cart.clearUserCart');




