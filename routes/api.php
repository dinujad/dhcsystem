<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('store')->middleware('store.api')->group(function () {
    Route::get('ping', [\App\Http\Controllers\Api\StoreBridgeController::class, 'ping']);
    Route::get('catalog', [\App\Http\Controllers\Api\StoreBridgeController::class, 'catalog']);
    Route::post('orders', [\App\Http\Controllers\Api\StoreBridgeController::class, 'storeOrder']);
});
