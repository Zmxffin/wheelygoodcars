<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotableSellerController;
use App\Http\Controllers\Admin\TagStatsController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\PublicCarController;
use App\Http\Controllers\RdwController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicCarController::class, 'home'])->name('home');

// Public offer pages (no login required) — F2, F3, F4, F5, F7, F8, F9, F12.
Route::get('/aanbod', [PublicCarController::class, 'index'])->name('public.index');
Route::get('/aanbod/{car}', [PublicCarController::class, 'show'])->name('public.show');
Route::get('/aanbod/{car}/views', [PublicCarController::class, 'views'])->name('public.views');

Route::middleware('auth')->group(function () {
    // Offerer car management — A1, A2, A3, B3, B7, B8, F1, F6, F10, F11.
    Route::get('/mijn-aanbod', [CarController::class, 'index'])->name('cars.index');
    Route::get('/aanbod-plaatsen', [CarController::class, 'create'])->name('cars.create');
    Route::post('/aanbod-plaatsen', [CarController::class, 'store'])->name('cars.store');
    Route::get('/mijn-aanbod/{car}/bewerken', [CarController::class, 'edit'])->name('cars.edit');
    Route::put('/mijn-aanbod/{car}', [CarController::class, 'update'])->name('cars.update');
    Route::patch('/mijn-aanbod/{car}/status', [CarController::class, 'toggleStatus'])->name('cars.toggleStatus');
    Route::delete('/mijn-aanbod/{car}', [CarController::class, 'destroy'])->name('cars.destroy');
    Route::get('/mijn-aanbod/{car}/pdf', [CarController::class, 'pdf'])->name('cars.pdf');

    // RDW licence-plate lookup (B1).
    Route::get('/rdw/{plate}', [RdwController::class, 'lookup'])->name('rdw.lookup');

    // Beheerder pages — B4, B5, B6.
    Route::prefix('beheer')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
        Route::get('/tags', [TagStatsController::class, 'index'])->name('tags');
        Route::get('/opvallend', [NotableSellerController::class, 'index'])->name('notable');
    });
});

require __DIR__.'/auth.php';
