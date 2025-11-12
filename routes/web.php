<?php
 
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;
 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
 
// --- 1. RUTE PUBLIK & LOGIN ---
 
// Halaman Utama langsung ke Login
Route::get('/', function () {
    return view('auth.login');
});
 
// Socialite (Login Microsoft) - Ditaruh di luar middleware auth
Route::get('/auth/microsoft/redirect', [SocialiteController::class, 'microsoftRedirect'])->name('login.microsoft.redirect');
Route::get('/auth/microsoft/callback', [SocialiteController::class, 'microsoftCallback'])->name('login.microsoft.callback');
 
 
// --- 2. RUTE YANG PERLU LOGIN ---
Route::middleware(['auth', 'verified'])->group(function () {
 
    // Dashboard Utama
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
 
    // --- MENU BIMBINGAN (MAHASISWA) ---
    
    // 1. Logbook Bimbingan (List Riwayat)
    Route::get('/bimbingan', function () {
        // Mengarah ke file: resources/views/bimbingan/index.blade.php
        return view('logbook');
    })->name('logbook');
 
    // 2. Upload Dokumen Skripsi
    Route::get('/bimbingan/upload', function () {
        // Mengarah ke file: resources/views/bimbingan/upload.blade.php
        return view('upload');
    })->name('upload');

    Route::get('/jadwal', function () { 
    return view('jadwal'); 
})->name('jadwal'); 

    Route::get('/profil', function () { 
    return view('profil'); 
})->name('profil'); 
 
 
    // --- PROFIL USER ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
 
});
 
require __DIR__.'/auth.php';