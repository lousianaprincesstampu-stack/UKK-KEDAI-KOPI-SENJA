<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\Manager\MenuController;

// Halaman utama diarahkan ke login.
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');
        Route::post('/login', [AuthController::class, 'login'])
            ->name('login.process');
                Route::post('/logout', [AuthController::class, 'logout'])
                    ->name('logout');

// Route khusus Manager. Pengguna hrs login dan memiliki role manager.
Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        // Halaman Dashboard Manager.
        Route::get('/dashboard', [ManagerController::class, 'index'])
            ->name('dashboard');

        // Halaman Data Menu Manager.
        Route::get('/menus', [MenuController::class, 'index'])
            ->name('menus.index');

         // Halaman formulir tambah menu.
        Route::get('/menus/create', [MenuController::class, 'create'])
            ->name('menus.create');

    });

    // Route khusus Kasir. Pengguna hrs login dan memiliki role cashier.
Route::middleware(['auth', 'role:cashier'])
    ->prefix('cashier')
    ->name('cashier.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('cashier.dashboard');
        })->name('dashboard');

    });