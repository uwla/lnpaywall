<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LNPaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/lnpay/auth', [LNPaymentController::class, 'auth']);
Route::get('/lnpay/pay', [LNPaymentController::class, 'pay']);
Route::post('/lnpay/pay', [LNPaymentController::class, 'pay']);
Route::post('/lnpay/confirm', [LNPaymentController::class, 'confirmPayment']);
Route::post('/lnpay/confirmJSON', [LNPaymentController::class, 'confirmPaymentJSON']);
