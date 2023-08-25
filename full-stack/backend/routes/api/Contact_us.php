<?php

use App\Http\Controllers\ContactUsController;
use Illuminate\Support\Facades\Route;

// get Categories
Route::post('/contact-us', [ContactUsController::class, 'store'])->name('contact.store');