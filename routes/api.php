<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KlinikController;

Route::get('/kliniks', [KlinikController::class, 'index']);
