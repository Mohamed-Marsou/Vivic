<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;



//
Route::get('/orders', [OrderController::class, 'index'])->name('order.index');

//
Route::get('/user/orders/history/{userId}', [OrderController::class, 'getUserOrders'])->name('order.getUserOrders');

//
Route::get('/order/products/{id}', [OrderController::class, 'getOrderProducts'])->name('order.getOrderProducts');
// Fill Order products DATA
Route::post('/orders/add', [OrderController::class, 'addOrderProducts'])->name('order.addOrderProducts');
