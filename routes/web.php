<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;

Route::get('/auth/microsoft/redirect', [SocialiteController::class, 'microsoftRedirect'])->name('login.microsoft.redirect');

// Rute untuk menangani data balikan dari Microsoft
Route::get('/auth/microsoft/callback', [SocialiteController::class, 'microsoftCallback'])->name('login.microsoft.callback');

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/charts', function () {
        // Ini akan memanggil: resources/views/charts.blade.php
        return view('charts');
    })->name('charts'); // <-- Ini yang dicari oleh error Anda

    Route::get('/tables', function () {
        // Ini akan memanggil: resources/views/tables.blade.php
        return view('tables');
    })->name('tables');
});

require __DIR__.'/auth.php';
