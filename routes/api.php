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

//Endpoint untuk pendaftaran akun pelanggan.
Route::post('/register', [CustomerAuthController::class, 'register']);
Route::post('/login', [CustomerAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [CustomerAuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/profile', [CustomerAuthController::class, 'profile']);
Route::middleware('auth:sanctum')->put('/change-password', [CustomerAuthController::class, 'changePassword']);
Route::get('/menus', [CustomerMenuController::class, 'index']);
Route::get('/tables', [CustomerTableController::class, 'index']);
Route::get('/menus/{id}', [CustomerMenuController::class, 'show']);
Route::middleware('auth:sanctum')->post('/orders', [CustomerOrderController::class, 'store']);
Route::middleware('auth:sanctum')->get('/orders', [CustomerOrderController::class, 'index']);
Route::middleware('auth:sanctum')->get('/orders/{id}', [CustomerOrderController::class, 'show']);
Route::middleware('auth:sanctum')->get('/orders/{id}/receipt', [CustomerOrderController::class, 'receipt']);