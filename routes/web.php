<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\KilterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/kilter', [KilterController::class, 'index'])->name('kilter');
Route::get('/usuarios/{user}', [UserController::class, 'showPublic'])->name('users.public');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');

    Route::get('/kilter/create', [KilterController::class, 'create'])->name('kilter.create');
    Route::post('/kilter/maps', [KilterController::class, 'storeMap'])->name('kilter.maps.store');
    Route::post('/kilter', [KilterController::class, 'store'])->name('kilter.store');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
