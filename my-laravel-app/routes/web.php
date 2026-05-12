<?php


use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::get('/{any}', function () {
    return view('app');
})->where('any',  '^(?!api).*$');
