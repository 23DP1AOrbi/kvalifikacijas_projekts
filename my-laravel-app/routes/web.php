<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('pages.home');
});

// Route::get('/home', function () {
//     return view('pages.home');
// });

Route::get('/about', function () {
    return view('pages.about');
});

Route::get('/register', function () {
    return view('pages.register');
});

Route::get('/login', function () {
    return view('pages.login');
});

// Route::post('/register', [UserController::class, 'register']);
Route::post('/register', [UserController::class, 'register']);
// Route::post('/login', [UserController::class, 'login']);

Route::middleware('web')->post('/login', [AuthController::class, 'login']);

// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [UserController::class, 'logout']);
Route::post('/logout', [AuthController::class, 'logout']);