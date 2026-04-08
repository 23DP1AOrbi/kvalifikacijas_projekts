<?php


use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DesignController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');


// --- SPA fallback route (must be last!) ---
Route::get('/{any}', function () {
    return view('app');
})->where('any',  '^(?!api).*$');
