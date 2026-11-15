<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\Manager\MenuController;
use App\Http\Controllers\Manager\BahanController;
use App\Http\Controllers\Manager\RecipeController;

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

// Route khusus manager. Pengguna hrs login dan memiliki role manager.
Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

       
    // Halaman Dashboard Manager.
    Route::get('/dashboard', [ManagerController::class, 'index'])
        ->name('dashboard');

    // Menampilkan daftar menu.
    Route::get('/menus', [MenuController::class, 'index'])
        ->name('menus.index');

    // Menampilkan halaman Data Bahan.
    Route::get('/bahans', [BahanController::class, 'index'])
        ->name('bahans.index');

    // Menampilkan formulir tambah bahan.
    Route::get('/bahans/create', [BahanController::class, 'create'])
        ->name('bahans.create');

    // Menyimpan bahan baru.
    Route::post('/bahans', [BahanController::class, 'store'])
        ->name('bahans.store');

    // Menampilkan formulir tambah menu.
    Route::get('/menus/create', [MenuController::class, 'create'])
        ->name('menus.create');

    // Menyimpan menu baru.
    Route::post('/menus', [MenuController::class, 'store'])
        ->name('menus.store');

    // Menampilkan formulir edit menu.
    Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])
        ->name('menus.edit');

    // Menyimpan perubahan menu.
    Route::put('/menus/{menu}', [MenuController::class, 'update'])
        ->name('menus.update');

    // Mengubah status aktif atau nonaktif menu.
    Route::patch('/menus/{menu}/status', [MenuController::class, 'toggleStatus'])
        ->name('menus.toggle-status');

    // Memperbarui batas minimum stok bahan.
    Route::patch('/bahans/{bahan}/minimum-stock', [BahanController::class, 'updateMinimumStock'])
        ->name('bahans.update-minimum-stock');

    // Menampilkan dafftar resep menu
    Route::get('/recipes', [RecipeController::class, 'index'])
        ->name('recipes.index');

    // Menampilkan formulir tambah resep.
    Route::get('/recipes/create', [RecipeController::class, 'create'])
        ->name('recipes.create');

    // Menyimpan resep baru.
    Route::post('/recipes', [RecipeController::class, 'store'])
        ->name('recipes.store');

    // Route untuk menampilkan halaman edit resep.
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])
        ->name('recipes.edit');

    // Route untuk menyimpan perubahan resep.
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])
        ->name('recipes.update');

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