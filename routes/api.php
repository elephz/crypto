<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TranferController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('currencies', [CurrencyController::class,'index'])->name('currencies.index');

Route::get('tranfer', [TranferController::class, 'index'])->name('tranfer.index');

Route::get('orders', [OrderController::class, 'index'])->name('order.show');
