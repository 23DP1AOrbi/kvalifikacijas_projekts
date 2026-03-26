<?php

use App\Http\Controllers\DesignController;
use Illuminate\Support\Facades\Route;

Route::get('/dizaini', [DesignController::class, 'index']);
Route::get('/dizaini/{id}', [DesignController::class, 'show']);
Route::post('/dizaini', [DesignController::class, 'store']);