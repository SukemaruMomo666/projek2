<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BimbinganController;
use App\Models\Bimbingan; // Diperlukan untuk Dashboard
use App\Http\Controllers\DokumenController; // Diperlukan untuk Dokumen
use App\Http\Controllers\JadwalController; // Diperlukan untuk Jadwal
use App\Http\Controllers\DosenController; // Diperlukan untuk Dashboard Dosen

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. RUTE PUBLIK & OTENTIKASI (HANYA UNTUK TAMU) ---
Route::middleware('guest')->group(function () {
    
    Route::get('/', function () {
        return view('auth.login');
    });

    // Login Microsoft
    Route::get('/auth/microsoft/redirect', [SocialiteController::class, 'microsoftRedirect'])->name('login.microsoft.redirect');
    Route::get('/auth/microsoft/callback', [SocialiteController::class, 'microsoftCallback'])->name('login.microsoft.callback');

});


// --- 2. RUTE GLOBAL (SEMUA YANG SUDAH LOGIN) ---
Route::middleware(['auth'])->group(function () {

    /**
     * Rute Dashboard Cerdas
     * Mengambil data dinamis untuk Mahasiswa
     * Mengarahkan Dosen ke dashboard mereka
     */
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        if ($user->role === 'dosen') {
            return redirect()->route('dosen.dashboard');
        }

        // --- Logika untuk Mahasiswa ---
        $dosen = $user->dosenPembimbing; // Relasi dari Model User
        
        // Ambil semua logbook
        $logbooks = Bimbingan::where('mahasiswa_id', $user->id)
                            ->orderBy('tanggal_bimbingan', 'desc')
                            ->get();
        
        // Siapkan data untuk statistik
        $logbooksTerkini = $logbooks->take(3);
        $totalBimbingan = $logbooks->count();
        $statusTerkini = $logbooks->first()->status ?? 'Belum Ada';
        
        // Kirim semua data ini ke view 'dashboard'
        return view('dashboard', [
            'dosen' => $dosen,
            'logbooksTerkini' => $logbooksTerkini,
            'totalBimbingan' => $totalBimbingan,
            'statusTerkini' => $statusTerkini,
        ]);

    })->name('dashboard');

    // Profil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// --- 3. RUTE KHUSUS MAHASISWA ---
// Hanya bisa diakses jika role = mahasiswa
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {

    // Logbook Bimbingan (Menampilkan & Menyimpan)
    Route::get('/bimbingan', [BimbinganController::class, 'index'])->name('bimbingan.index');
    Route::post('/bimbingan', [BimbinganController::class, 'store'])->name('bimbingan.store');

    // Upload Dokumen Skripsi (Menampilkan & Menyimpan)
    Route::get('/bimbingan/upload', [DokumenController::class, 'index'])->name('bimbingan.upload');
    Route::post('/bimbingan/upload', [DokumenController::class, 'store'])->name('bimbingan.upload.store');

    // Jadwal & Booking Dosen (Menampilkan & Menyimpan)
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');

});


// --- 4. RUTE KHUSUS DOSEN ---
// Hanya bisa diakses jika role = dosen
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    
    // Dashboard Dosen (Menampilkan data dinamis)
    Route::get('/dashboard', [DosenController::class, 'index'])->name('dashboard');

    // Validasi Logbook
    Route::get('/validasi-logbook', [DosenController::class, 'showLogbookValidasi'])->name('validasi.logbook.index');
    Route::post('/validasi-logbook', [DosenController::class, 'storeLogbookValidasi'])->name('validasi.logbook.store');
    
    // Validasi Dokumen
    Route::get('/validasi-dokumen', [DosenController::class, 'showDokumenValidasi'])->name('validasi.dokumen.index');
    Route::post('/validasi-dokumen', [DosenController::class, 'storeDokumenValidasi'])->name('validasi.dokumen.store');

    // Data Mahasiswa
    Route::get('/mahasiswa', [DosenController::class, 'showMahasiswaList'])->name('mahasiswa.index');

    // === RUTE BARU YANG DITAMBAHKAN ===
    // Kelola Jadwal
    Route::get('/kelola-jadwal', [DosenController::class, 'showJadwalValidasi'])->name('jadwal.index');
    Route::post('/kelola-jadwal', [DosenController::class, 'storeJadwalValidasi'])->name('jadwal.store');

    // Arsip Skripsi (Placeholder)
    Route::get('/arsip', function() {
        // Nanti kita buat view-nya
        return "Halaman Arsip Skripsi (Dalam Pengerjaan)";
    })->name('arsip.index');
    // === AKHIR RUTE BARU ===

});

require __DIR__.'/auth.php';
