<?php 
 
use App\Http\Controllers\ProfileController; 
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\Auth\SocialiteController; 
use Illuminate\Support\Facades\Auth; 
 
/* 
|-------------------------------------------------------------------------- 
| Web Routes 
|-------------------------------------------------------------------------- 
*/ 
 
// --- 1. RUTE PUBLIK & OTENTIKASI --- 
 
Route::get('/', function () { 
    return view('auth.login'); 
}); 
 
// Login Microsoft 
Route::get('/auth/microsoft/redirect', [SocialiteController::class, 
'microsoftRedirect'])->name('login.microsoft.redirect'); 
Route::get('/auth/microsoft/callback', [SocialiteController::class, 
'microsoftCallback'])->name('login.microsoft.callback'); 
 
 
// --- 2. RUTE GLOBAL (SEMUA YANG SUDAH LOGIN) --- 
Route::middleware(['auth'])->group(function () { 
 
    // Dashboard Redirect 
    Route::get('/dashboard', function () { 
        if (Auth::user()->role === 'dosen') { 
            return view('dosen.dashboard'); 
        } 
        return view('dashboard'); 
    })->name('dashboard'); 
 
    // Profil 
    Route::get('/profile', [ProfileController::class, 
'edit'])->name('profile.edit'); 
    Route::patch('/profile', [ProfileController::class, 
'update'])->name('profile.update'); 
    Route::delete('/profile', [ProfileController::class, 
'destroy'])->name('profile.destroy'); 
}); 
 
 
// --- 3. RUTE KHUSUS MAHASISWA --- 
Route::middleware(['auth', 'role:mahasiswa'])->group(function () { 
 
    Route::get('/bimbingan', function () { 
        return view('bimbingan/logbook'); 
    })->name('logbook'); 
 
    Route::get('/bimbingan/upload', function () { 
        return view('bimbingan/upload'); 
    })->name('upload'); 
 
    Route::get('/jadwal', function () { 
        return view('bimbingan/jadwal'); 
    })->name('jadwal'); 
 
}); 
 
 
// --- 4. RUTE KHUSUS DOSEN --- 
Route::middleware(['auth', 
'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () { 
     
    Route::get('/dashboard', function () { 
        return view('dosen.dashboard'); 
    })->name('dashboard'); 
 
}); 
 
require __DIR__.'/auth.php'; 
 
 