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
use App\Http\Controllers\Manager\ImportCsvController;
use App\Http\Controllers\Cashier\CashierController;
use App\Http\Controllers\Cashier\CashierOrderController;


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

    Route::get('/dashboard', [ManagerController::class, 'index'])
        ->name('dashboard');

    Route::get('/menus', [MenuController::class, 'index'])
        ->name('menus.index');

    Route::get('/bahans', [BahanController::class, 'index'])
        ->name('bahans.index');

    Route::post('/bahans/update-harga', [BahanController::class, 'updateHarga'])
        ->name('bahans.update-harga');

    Route::get('/bahans/create', [BahanController::class, 'create'])
        ->name('bahans.create');

    Route::post('/bahans', [BahanController::class, 'store'])
        ->name('bahans.store');

    Route::get('/bahans/{bahan}/edit', [BahanController::class, 'edit'])
        ->name('bahans.edit');

    Route::put('/bahans/{bahan}', [BahanController::class, 'update'])
        ->name('bahans.update');

    Route::delete('/bahans/{bahan}', [BahanController::class, 'destroy'])
        ->name('bahans.destroy');

    Route::get('/menus/create', [MenuController::class, 'create'])
        ->name('menus.create');

    Route::post('/menus', [MenuController::class, 'store'])
        ->name('menus.store');

    Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])
        ->name('menus.edit');

    Route::put('/menus/{menu}', [MenuController::class, 'update'])
        ->name('menus.update');

    Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])
        ->name('menus.destroy');

    Route::patch('/menus/{menu}/status', [MenuController::class, 'toggleStatus'])
        ->name('menus.toggle-status');

    Route::patch('/bahans/{bahan}/minimum-stock', [BahanController::class, 'updateMinimumStock'])
        ->name('bahans.update-minimum-stock');

    Route::get('/recipes', [RecipeController::class, 'index'])
        ->name('recipes.index');

    Route::get('/recipes/create', [RecipeController::class, 'create'])
        ->name('recipes.create');

    Route::post('/recipes', [RecipeController::class, 'store'])
        ->name('recipes.store');

    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])
        ->name('recipes.edit');

    Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])
        ->name('recipes.update');

    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])
        ->name('recipes.destroy');

    Route::get('/stock-entries', [StockEntryController::class, 'index'])
        ->name('stock-entries.index');

    Route::get('/stock-entries/create', [StockEntryController::class, 'create'])
        ->name('stock-entries.create');

    Route::post('/stock-entries', [StockEntryController::class, 'store'])
        ->name('stock-entries.store');

    Route::resource('suppliers', SupplierController::class)
        ->except(['show']);

    Route::get('/import-csv', function () {
        return view('manager.import-csv.index');
        })->name('import-csv.index');

    Route::post('/import-csv', [ImportCsvController::class, 'store'])
        ->name('import-csv.store');

    Route::get('/reports/sales', [ReportController::class, 'sales'])
        ->name('reports.sales');

    Route::get('/reports/low-stock', [ReportController::class, 'lowStock'])
        ->name('reports.low-stock');

    Route::get('/reports/margin', [ReportController::class, 'margin'])
        ->name('reports.margin');

    });

Route::middleware(['auth', 'role:cashier'])
    ->prefix('cashier')
    ->name('cashier.')
    ->group(function () {

        Route::get('/dashboard', [CashierController::class, 'index'])
        ->name('dashboard');
        
        Route::get('/pesanan-offline', [CashierOrderController::class, 'create'])
            ->name('orders.create');

        Route::post('/pesanan-offline', [CashierOrderController::class, 'storeOffline'])
            ->name('orders.store-offline');

        Route::get('/pesanan-masuk', [CashierOrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/pesanan/{id}', [CashierOrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('/pesanan/{id}/status', [CashierOrderController::class, 'updateStatus'])
            ->name('orders.update-status');

        Route::patch('/pesanan/{id}/batal', [CashierOrderController::class, 'cancel'])
            ->name('orders.cancel');
            

        Route::get('/daftar-pesanan', [CashierOrderController::class, 'list'])
            ->name('orders.list');


    });