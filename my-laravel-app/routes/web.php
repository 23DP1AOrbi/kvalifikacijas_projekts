<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/app', function () {
    return view('app');
});

// Route::get('/home', function () {
//     return view('pages.home');
// });

Route::get('/about', function () {
    return view('pages.about');
});