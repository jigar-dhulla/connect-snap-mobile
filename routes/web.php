<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Home;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes (Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth.api')->group(function () {
    // Home - My QR Code
    Route::get('/', Home::class)->name('home');

    // Scan
    Route::get('/scan', function () {
        return view('welcome');
    })->name('scan');

    // Connections
    Route::get('/connections', function () {
        return view('welcome');
    })->name('connections.index');

    // Profile
    Route::get('/profile/edit', function () {
        return view('welcome');
    })->name('profile.edit');
});
