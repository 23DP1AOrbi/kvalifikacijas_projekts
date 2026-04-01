<?php

use App\Http\Controllers\DesignController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;



Route::get('/dizaini', [DesignController::class, 'index']);
Route::get('/dizaini/{id}', [DesignController::class, 'show']);
Route::delete('/dizaini/{id}', [DesignController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    // This is the one your Vue app should call: GET /api/user
    Route::get('/user', function (Request $request) { 
        return $request->user(); 
    });

    // This is the one for updating: PUT /api/user/update
    Route::put('/user/update', [UserController::class, 'update']);
});

// This allows Vue to fetch all categories and add new ones
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

Route::post('/dizaini/{design}/sync-categories', [DesignController::class, 'syncCategories']);
Route::patch('/dizaini/{design}', [DesignController::class, 'update']);