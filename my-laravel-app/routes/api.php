<?php

use App\Http\Controllers\DesignController;
use Illuminate\Support\Facades\Route;

Route::get('/dizaini', [DesignController::class, 'index']);
Route::get('/dizaini/{id}', [DesignController::class, 'show']);

use App\Http\Controllers\CategoryController;

// This allows Vue to fetch all categories and add new ones
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);