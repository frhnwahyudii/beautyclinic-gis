<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KlinikController;
use App\Http\Controllers\Api\StatsController;

Route::get('/kliniks', [KlinikController::class, 'index']);
Route::get('/stats', [StatsController::class, 'index']);
Route::get('/stats/detail', [StatsController::class, 'klinikDetail']);
