<?php

use App\Http\Controllers\DesignController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;


Route::get('/dizaini', [DesignController::class, 'index']);
Route::get('/dizaini/{id}', [DesignController::class, 'show']);
Route::delete('/dizaini/{id}', [DesignController::class, 'destroy']);


// This allows Vue to fetch all categories and add new ones
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);