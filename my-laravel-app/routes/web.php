<?php

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

// Route::post('/register', [UserController::class, 'register']);