<?php

use App\Http\Controllers\AlbumController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('albums.index');
});

Route::get('/albums', [AlbumController::class, 'index'])->name('albums.index');
Route::middleware('auth')->group(function () {
    Route::get('/albums/create', [AlbumController::class, 'create'])->name('albums.create');
    Route::post('/albums', [AlbumController::class, 'store'])->name('albums.store');
    Route::get('/albums/{album}/edit', [AlbumController::class, 'edit'])->name('albums.edit');
    Route::put('/albums/{album}', [AlbumController::class, 'update'])->name('albums.update');
    Route::delete('/albums/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');
});

Route::get('/albums/search', [AlbumController::class, 'search'])->name('albums.search');
Route::get('/albums/fetch-librefm', [AlbumController::class, 'fetchFromLastFm'])->name('albums.fetch-librefm');

Route::get('/login', function () {
    return view('auth.login');
})->name('auth.login');

Route::get('/register', function () {
    return view('auth.register');
})->name('auth.register');

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'name' => 'required|min:3|max:255',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->route('albums.index');
});

Route::post('/login', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6'
    ]);

    if (Auth::attempt($validated)) {
        $request->session()->regenerate();

        return redirect()->route('albums.index');
    }

    return back()->withErrors([
        'email' => "Неверные учётные данные."
    ])->onlyInput('email');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('auth.logout');
