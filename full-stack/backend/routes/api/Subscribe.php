<?php

use App\Http\Controllers\SubscriberController;
use Illuminate\Support\Facades\Route;

// get subs details
Route::get('/subscribe', [SubscriberController::class, 'index'])->name('subscribe.index');
// store subs details
Route::post('/subscribe/new', [SubscriberController::class, 'store'])->name('subscribe.store');
