<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


require __DIR__.'/Api/Admin.php';

require __DIR__.'/Api/Product.php';

require __DIR__.'/Api/Subscribe.php';

require __DIR__.'/Api/User.php';

require __DIR__.'/Api/Order.php';

require __DIR__.'/Api/Stripe.php';

require __DIR__.'/Api/Paypal.php';

require __DIR__.'/Api/Contact_us.php';

require __DIR__.'/Api/Admin.php';

// get all countries
Route::get('/countries', [Controller::class, 'allCountries'])->name('Countries.allCountries');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
