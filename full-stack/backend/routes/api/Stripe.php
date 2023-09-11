<?php

use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Route;


// stripe PaymentIntent
Route::post('/order', [StripeController::class, 'PaymentIntent'])->name('stripe.PaymentIntent');