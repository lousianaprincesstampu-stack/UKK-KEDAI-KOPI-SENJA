<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\CustomerMenuController;
use App\Http\Controllers\Api\CustomerTableController;
use App\Http\Controllers\Api\CustomerOrderController;

Route::get('/tes', function () {
    return response()->json([
        'pesan' => 'API Kedai Kopi Senja berhasil terhubung.',
    ]);
});

Route::post('/register', [CustomerAuthController::class, 'register']);
Route::post('/login', [CustomerAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/profile', [CustomerAuthController::class, 'profile']);
    Route::put('/change-password', [CustomerAuthController::class, 'changePassword']);

    Route::middleware('customer.password')->group(function () {
        Route::post('/orders', [CustomerOrderController::class, 'store']);
        Route::get('/orders', [CustomerOrderController::class, 'index']);
        Route::get('/orders/{id}', [CustomerOrderController::class, 'show']);
        Route::get('/orders/{id}/receipt', [CustomerOrderController::class, 'receipt']);
    });
});

Route::get('/menus', [CustomerMenuController::class, 'index']);
Route::get('/tables', [CustomerTableController::class, 'index']);
Route::get('/menus/{id}', [CustomerMenuController::class, 'show']);