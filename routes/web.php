<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('albums.index');
});

Route::prefix('albums')->name('albums.')->group(function () {
    Route::get('/', [AlbumController::class, 'index'])->name('index');
    Route::get('/search', [AlbumController::class, 'search'])->name('search');
    Route::get('/fetch-librefm', [AlbumController::class, 'fetchFromLastFm'])->name('fetch-librefm');

    Route::middleware(['auth'])->group(function () {
        Route::get('/create', [AlbumController::class, 'create'])->name('create');
        Route::post('/', [AlbumController::class, 'store'])->name('store');
        Route::get('/{album}/edit', [AlbumController::class, 'edit'])->name('edit');
        Route::put('/{album}', [AlbumController::class, 'update'])->name('update');
        Route::delete('/{album}', [AlbumController::class, 'destroy'])->name('destroy');
    });
});


Route::name('auth.')->group(function () {
    Route::prefix('login')->group(function () {
        Route::get('/', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/', [AuthController::class, 'login']);
    });

    Route::prefix('register')->group(function () {
        Route::post('/', [AuthController::class, 'register']);
        Route::get('/', [AuthController::class, 'showRegister'])->name('register');
    });


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
