<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\Manager\MenuController;
use App\Http\Controllers\Manager\BahanController;
use App\Http\Controllers\Manager\RecipeController;
use App\Http\Controllers\Manager\StockEntryController;
use App\Http\Controllers\Manager\SupplierController;
use App\Http\Controllers\Manager\ReportController;


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

    // Menampilkan formulir edit bahan.
    Route::get('/bahans/{bahan}/edit', [BahanController::class, 'edit'])
        ->name('bahans.edit');

    // Menyimpan perubahan data bahan.
    Route::put('/bahans/{bahan}', [BahanController::class, 'update'])
        ->name('bahans.update');

    // Menghapus bahan.
    Route::delete('/bahans/{bahan}', [BahanController::class, 'destroy'])
        ->name('bahans.destroy');

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

    Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])
        ->name('menus.destroy');

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

    //Menghapus resep menu.
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])
        ->name('recipes.destroy');

    // Menampilkan daftar riwayat stok masuk.
    Route::get('/stock-entries', [StockEntryController::class, 'index'])
        ->name('stock-entries.index');

    // Menampilkan formulir pencatatan stok masuk.
    Route::get('/stock-entries/create', [StockEntryController::class, 'create'])
        ->name('stock-entries.create');

    // Menyimpan pencatatan stok masuk.
    Route::post('/stock-entries', [StockEntryController::class, 'store'])
        ->name('stock-entries.store');

    // Route untuk mengelola data supplier.
    Route::resource('suppliers', SupplierController::class)
        ->except(['show']);

    // Menampilkan halaman Import CSV.
    Route::get('/import-csv', function () {
        return view('manager.import-csv.index');
        })->name('import-csv.index');

    // Menampilkan laporan penjualan per menu.
    Route::get('/reports/sales', [ReportController::class, 'sales'])
        ->name('reports.sales');

    // Menampilkan laporan stok bahan menipis.
    Route::get('/reports/low-stock', [ReportController::class, 'lowStock'])
        ->name('reports.low-stock');

    // Margin report
    Route::get('/reports/margin', [ReportController::class, 'margin'])
        ->name('reports.margin');

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