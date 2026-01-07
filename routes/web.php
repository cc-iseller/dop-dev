<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Onboarding\SetupStoreController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::get('/auth/google/redirect', [AuthController::class, 'redirect'])
    ->name('auth.google.redirect');

Route::get('/auth/google/callback', [AuthController::class, 'callback'])
    ->name('auth.google.callback');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['web'])->group(function () {

    // halaman setup store (wajib login)
    Route::get('/setup/store', [SetupStoreController::class, 'create'])
        ->middleware('auth')
        ->name('setup.store');

    // submit create store
    Route::post('/setup/store', [SetupStoreController::class, 'store'])
        ->middleware('auth')
        ->name('setup.store.store');
});

// Route::get('/demo', [DemoController::class, 'start'])->name('demo.start');
// Route::post('/demo/reset', [DemoController::class, 'reset'])->name('demo.reset');
