<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminKlinikController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// All admin routes require authentication and admin role
Route::middleware(['auth', 'is_admin'])->group(function () {

    // Dashboard
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Diagnosa penyimpanan foto (khusus admin) — tampilkan hasil storage:check di browser
    Route::get('/storage-check', function () {
        \Illuminate\Support\Facades\Artisan::call('storage:check');
        return response(
            '<h3 style="font-family:ui-monospace,monospace;padding:1rem 1rem 0;">Diagnosa Penyimpanan Foto</h3>'
            . '<pre style="font:13px/1.6 ui-monospace,Consolas,monospace;padding:1rem;">'
            . e(\Illuminate\Support\Facades\Artisan::output())
            . '</pre>'
        )->header('Content-Type', 'text/html');
    })->name('admin.storage-check');

    // Klinik Management
    Route::prefix('kliniks')->group(function () {
        Route::get('/', [AdminKlinikController::class, 'index'])->name('admin.kliniks.index');
        Route::get('/{klinik}/edit', [AdminKlinikController::class, 'edit'])->name('admin.kliniks.edit');
        Route::put('/{klinik}', [AdminKlinikController::class, 'update'])->name('admin.kliniks.update');
        Route::delete('/{klinik}', [AdminKlinikController::class, 'destroy'])->name('admin.kliniks.destroy');
        Route::patch('/{klinik}/status', [AdminKlinikController::class, 'updateStatus'])->name('admin.kliniks.update-status');
    });

});
