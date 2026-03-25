<?php


use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('pages.home');
});

Route::get('/about', function () {
    return view('pages.about');
});

Route::get('/register', function () {
    return view('pages.register');
});

Route::get('/login', function () {
    return view('pages.login');
});


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('web')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('web')->get('/user', [AuthController::class, 'user']);


// Route::post('/logout', [AuthController::class, 'logout']);
// Route::post('/logout', function () {
//     Auth::logout();
//     request()->session()->invalidate();
//     request()->session()->regenerateToken();

//     return response()->json(['message' => 'Logged out']);
// });


// Route::get('/user', [AuthController::class, 'user']); // Get current logged-in user
