<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;

/*
|--------------------------------------------------------------------------
| Rute Otentikasi Eksternal (Biarkan Saja)
|--------------------------------------------------------------------------
*/
Route::get('/auth/microsoft/redirect', [SocialiteController::class, 'microsoftRedirect'])->name('login.microsoft.redirect');
Route::get('/auth/microsoft/callback', [SocialiteController::class, 'microsoftCallback'])->name('login.microsoft.callback');

/*
|--------------------------------------------------------------------------
| Rute Publik / Tamu
|--------------------------------------------------------------------------
*/
// Mengarahkan halaman utama ke login (Ini sudah benar jika Anda mau)
Route::get('/', function () {
    return view('auth.login');
});

/*
|--------------------------------------------------------------------------
| Rute yang Dilindungi (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Rute Dashboard (Bawaan Breeze)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rute Profile (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    // --- TAMBAHKAN RUTE TEMPLATE BARU ANDA DI SINI ---
    // Berdasarkan file di gambar Anda:

    Route::get('/tables', function () {
        // Ini akan memanggil file: resources/views/tables.blade.php
        return view('tables');
    })->name('tables');

    Route::get('/charts', function () {
        // Ini akan memanggil file: resources/views/charts.blade.php
        return view('charts');
    })->name('charts');

    // Anda bisa tambahkan halaman lain dari template Anda di sini
    // contoh:
    // Route::get('/404', function () {
    //     return view('404'); 
    // })->name('404'); // Ganti '404' dengan nama view yang sesuai

});

/*
|--------------------------------------------------------------------------
| Rute Auth Bawaan Breeze (Biarkan Saja)
|--------------------------------------------------------------------------
| Ini sudah mencakup /login, /register, /logout, dll.
*/
require __DIR__.'/auth.php';