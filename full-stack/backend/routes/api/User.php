<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// Register User
Route::post('/user/register', [UserController::class, 'register'])->name('user.register');
// login User
Route::post('/user/login', [UserController::class, 'login'])->name('user.login');
// get user data 
Route::get('/user/data/{id}', [UserController::class, 'search'])->name('user.search');