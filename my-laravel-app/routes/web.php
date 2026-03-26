<?php


use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DesignController;
use Illuminate\Support\Facades\Route;

// // --- Specific API / controller routes ---
// Route::get('/dizaini', [DesignController::class, 'index']);
// Route::post('/dizaini', [DesignController::class, 'store']);
// Route::get('/dizaini/{id}', [DesignController::class, 'show']);

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('web')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('web')->get('/user', [AuthController::class, 'user']);

// --- SPA fallback route (must be last!) ---
Route::get('/{any}', function () {
    return view('app');
})->where('any',  '^(?!api).*$');

// use App\Http\Controllers\UserController;
// use App\Http\Controllers\AuthController;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\DesignController;


// Route::get('/{any}', function() {
//     return view('app');
// })->where('any', '.*');

// Route::get('/', function () {
//     return view('pages.home');
// });

// Route::get('/about', function () {
//     return view('pages.about');
// });

// Route::get('/register', function () {
//     return view('pages.register');
// });

// Route::get('/login', function () {
//     return view('pages.login');
// });

// Route::get('/profils', function () {
//     return view('pages.profile');
// });

// Route::get('/dizaini', function () {
//     return view('pages.designs');
// });

// Route::get('/dizaini/{id}', function () {
//     return view('pages.designs');
// });



// Route::post('/register', [UserController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login'])->name('login');
// Route::middleware('web')->post('/logout', [AuthController::class, 'logout']);

// Route::middleware('web')->get('/user', [AuthController::class, 'user']);


// Route::get('/dizaini', [DesignController::class, 'index']);
// Route::post('/designs', [DesignController::class, 'store']);

// Route::get('/dizaini/{id}', [DesignController::class, 'show']);
