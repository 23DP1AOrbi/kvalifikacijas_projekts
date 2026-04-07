<?php

use App\Http\Controllers\DesignController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;



Route::get('/dizaini', [DesignController::class, 'index']);
Route::get('/dizaini/{id}', [DesignController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);


Route::middleware(['web', 'auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) { 
        return $request->user(); 
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/user/update', [UserController::class, 'update']);
});

// Admin only routes
Route::middleware(['web', 'auth:sanctum', AdminMiddleware::class])->group(function () {

    // Category management
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Design management (Update, Sync, Delete)
    Route::post('/dizaini', [DesignController::class, 'store']);
    Route::delete('/dizaini/{id}', [DesignController::class, 'destroy']);
    Route::patch('/dizaini/{design}', [DesignController::class, 'update']);
    Route::post('/dizaini/{design}/sync-categories', [DesignController::class, 'syncCategories']);
});