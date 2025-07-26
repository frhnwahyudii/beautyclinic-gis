<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminKlinikController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// Statistics
Route::get('/statistics', [App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('admin.statistics');

// Klinik Management
Route::prefix('kliniks')->group(function () {
    Route::get('/', [AdminKlinikController::class, 'index'])->name('admin.kliniks.index');
    Route::get('/{klinik}/edit', [AdminKlinikController::class, 'edit'])->name('admin.kliniks.edit');
    Route::put('/{klinik}', [AdminKlinikController::class, 'update'])->name('admin.kliniks.update');
    Route::delete('/{klinik}', [AdminKlinikController::class, 'destroy'])->name('admin.kliniks.destroy');
    Route::patch('/{klinik}/status', [AdminKlinikController::class, 'updateStatus'])->name('admin.kliniks.update-status');
});
