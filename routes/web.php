<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');
        Route::post('/login', [AuthController::class, 'login'])
            ->name('login.process');
                Route::post('/logout', [AuthController::class, 'logout'])
                    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::prefix('manager')->name('manager.')->group(function () {

        // Menampilkan dashboard Manager.
        Route::get('/dashboard', function () {
            return view('manager.dashboard');
        })->name('dashboard');
    });
    Route::prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/dashboard', function () {
            return view('cashier.dashboard');
        })->name('dashboard');
    });
});