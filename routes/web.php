<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\KilterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MultimediaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/kilter', [KilterController::class, 'index'])->name('kilter');
Route::get('/sailkapena', [RankingController::class, 'index'])->name('ranking');
Route::get('/eguraldia', function () {
    $locations = \App\Models\WeatherLocation::query()->orderBy('name')->get();
    return view('weather.index', ['weatherLocations' => $locations]);
})->name('weather');
Route::get('/kilter/blokea/{block}', [KilterController::class, 'show'])->name('kilter.show');
Route::get('/usuarios/{user}', [UserController::class, 'showPublic'])->name('users.public');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::put('/settings', [UserController::class, 'updateSettings'])->name('settings.update');
    Route::get('/multimedia', [MultimediaController::class, 'index'])->name('multimedia');
    Route::post('/multimedia', [MultimediaController::class, 'store'])->name('multimedia.store');

    Route::get('/kilter/create', [KilterController::class, 'create'])->name('kilter.create');
    Route::get('/kilter/blokea/{block}/edit', [KilterController::class, 'edit'])->name('kilter.edit');
    Route::post('/kilter/maps', [KilterController::class, 'storeMap'])->name('kilter.maps.store');
    Route::post('/kilter', [KilterController::class, 'store'])->name('kilter.store');
    Route::put('/kilter/blokea/{block}', [KilterController::class, 'update'])->name('kilter.update');
    Route::post('/kilter/blokea/{block}/toggle-completed', [KilterController::class, 'toggleCompleted'])->name('kilter.toggleCompleted');
    Route::post('/kilter/blokea/{block}/vote', [KilterController::class, 'vote'])->name('kilter.vote');
    Route::post('/kilter/blokea/{block}/recote', [KilterController::class, 'recote'])->name('kilter.recote');
    Route::post('/kilter/blokea/{block}/recote/resolve', [KilterController::class, 'resolveRecote'])->name('kilter.recote.resolve');
    Route::delete('/kilter/blokea/{block}', [KilterController::class, 'destroy'])->name('kilter.destroy');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::put('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::delete('/admin/maps/{map}', [AdminController::class, 'deleteMap'])->name('admin.maps.delete');
    Route::post('/admin/locations', [AdminController::class, 'storeLocation'])->name('admin.locations.store');
    Route::delete('/admin/locations/{location}', [AdminController::class, 'deleteLocation'])->name('admin.locations.delete');
});
