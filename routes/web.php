<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KlinikController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Public Routes
Route::get('/', [KlinikController::class, 'index'])->name('home');
Route::get('/klinik/create', [KlinikController::class, 'create'])->name('klinik.create');
Route::post('/klinik', [KlinikController::class, 'store'])->name('klinik.store');
Route::get('/klinik/{klinik}', [KlinikController::class, 'show'])->name('klinik.show');

// Admin Routes
Route::prefix('admin')->group(function () {
    require __DIR__.'/admin.php';
});
