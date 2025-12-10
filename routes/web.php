<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Auth;

// Controller
use App\Http\Controllers\BimbinganController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VerificationController;

// Model
use App\Models\Bimbingan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. RUTE PUBLIK & OTENTIKASI (HANYA UNTUK TAMU) ---
Route::middleware('guest')->group(function () {
    Route::get('/', function () { return view('auth.login'); });
    Route::get('/auth/microsoft/redirect', [SocialiteController::class, 'microsoftRedirect'])->name('login.microsoft.redirect');
    Route::get('/auth/microsoft/callback', [SocialiteController::class, 'microsoftCallback'])->name('login.microsoft.callback');
});

// --- 2. RUTE AKTIVASI AKUN (Wajib Login tapi Belum Ganti Password) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/aktivasi-akun', [VerificationController::class, 'show'])->name('first-login.show');
    Route::put('/aktivasi-akun', [VerificationController::class, 'update'])->name('first-login.update');
});

// --- 3. RUTE GLOBAL (SUDAH LOGIN + SUDAH GANTI PASSWORD) ---
Route::middleware(['auth', 'password.changed'])->group(function () {

    // Dashboard Cerdas (Logic Redirect Berdasarkan Role)
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->role === 'dosen') return redirect()->route('dosen.dashboard');

        // Logika Khusus Dashboard Mahasiswa
        $dosen = $user->dosenPembimbing;
        $logbooks = Bimbingan::where('mahasiswa_id', $user->id)->orderBy('tanggal_bimbingan', 'desc')->get();
        
        return view('dashboard', [
            'dosen' => $dosen,
            'logbooksTerkini' => $logbooks->take(3),
            'totalBimbingan' => $logbooks->count(),
            'statusTerkini' => $logbooks->first()->status ?? 'Belum Ada',
        ]);
    })->name('dashboard');

    // Profil User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ====================================================
    // GRUP RUTE MAHASISWA
    // ====================================================
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/bimbingan', [BimbinganController::class, 'index'])->name('bimbingan.index');
        Route::post('/bimbingan', [BimbinganController::class, 'store'])->name('bimbingan.store');
        Route::get('/bimbingan/upload', [DokumenController::class, 'index'])->name('bimbingan.upload');
        Route::post('/bimbingan/upload', [DokumenController::class, 'store'])->name('bimbingan.upload.store');
        Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
        Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    });

    // ====================================================
    // GRUP RUTE DOSEN
    // ====================================================
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [DosenController::class, 'index'])->name('dashboard');
        
        // Validasi
        Route::get('/validasi-logbook', [DosenController::class, 'showLogbookValidasi'])->name('validasi.logbook.index');
        Route::post('/validasi-logbook', [DosenController::class, 'storeLogbookValidasi'])->name('validasi.logbook.store');
        Route::get('/validasi-dokumen', [DosenController::class, 'showDokumenValidasi'])->name('validasi.dokumen.index');
        Route::post('/validasi-dokumen', [DosenController::class, 'storeDokumenValidasi'])->name('validasi.dokumen.store');
        
        // Data Mahasiswa Bimbingan
        Route::get('/mahasiswa', [DosenController::class, 'showMahasiswaList'])->name('mahasiswa.index');
        
        // Jadwal
        Route::get('/kelola-jadwal', [DosenController::class, 'showJadwalValidasi'])->name('jadwal.index');
        Route::post('/kelola-jadwal', [DosenController::class, 'storeJadwalValidasi'])->name('jadwal.store');
        
        // Arsip
        Route::get('/arsip', [DosenController::class, 'showArsip'])->name('arsip.index');
    });

    // ====================================================
    // GRUP RUTE ADMIN
    // ====================================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        
        // --- Manajemen Dosen ---
        Route::get('/dosen', [AdminController::class, 'indexDosen'])->name('dosen.index');
        Route::post('/dosen', [AdminController::class, 'storeDosen'])->name('dosen.store');
        Route::get('/dosen/{dosen}/edit', [AdminController::class, 'editDosen'])->name('dosen.edit');
        Route::put('/dosen/{dosen}', [AdminController::class, 'updateDosen'])->name('dosen.update');
        Route::delete('/dosen/{dosen}', [AdminController::class, 'destroyDosen'])->name('dosen.destroy');

        // --- Manajemen Mahasiswa ---
        // 1. Batch Tools (Generate Kelas & Plotting Masal) -> WAJIB ADA DISINI
        Route::post('/mahasiswa/generate-kelas', [AdminController::class, 'generateKelas'])->name('mahasiswa.generate-kelas');
        Route::post('/mahasiswa/bulk-plotting', [AdminController::class, 'bulkPlotting'])->name('mahasiswa.bulk-plotting');

        // 2. Import & Template
        Route::post('/mahasiswa/import', [AdminController::class, 'importMahasiswa'])->name('mahasiswa.import');
        Route::get('/mahasiswa/template', [AdminController::class, 'downloadTemplate'])->name('mahasiswa.template');

        // 3. CRUD Mahasiswa Standar
        Route::get('/mahasiswa', [AdminController::class, 'indexMahasiswa'])->name('mahasiswa.index');
        Route::get('/mahasiswa/create', [AdminController::class, 'createMahasiswa'])->name('mahasiswa.create');
        Route::post('/mahasiswa', [AdminController::class, 'storeMahasiswa'])->name('mahasiswa.store');
        Route::get('/mahasiswa/{mahasiswa}/edit', [AdminController::class, 'editMahasiswa'])->name('mahasiswa.edit');
        Route::put('/mahasiswa/{mahasiswa}', [AdminController::class, 'updateMahasiswa'])->name('mahasiswa.update');
        Route::delete('/mahasiswa/{mahasiswa}', [AdminController::class, 'destroyMahasiswa'])->name('mahasiswa.destroy');
        
        // --- Pengaturan Sistem ---
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings.index');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });

});

require __DIR__.'/auth.php';