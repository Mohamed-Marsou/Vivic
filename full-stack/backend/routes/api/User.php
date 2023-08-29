<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// Register User
Route::post('/user/register', [UserController::class, 'register'])->name('user.register');
// login User
Route::post('/user/login', [UserController::class, 'login'])->name('user.login');