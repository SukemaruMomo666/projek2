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

// --- 1. RUTE PUBLIK & OTENTIKASI ---
Route::middleware('guest')->group(function () {
    Route::get('/', function () { return view('auth.login'); });
    Route::get('/auth/microsoft/redirect', [SocialiteController::class, 'microsoftRedirect'])->name('login.microsoft.redirect');
    Route::get('/auth/microsoft/callback', [SocialiteController::class, 'microsoftCallback'])->name('login.microsoft.callback');
});

// --- 2. RUTE AKTIVASI AKUN ---
Route::middleware(['auth'])->group(function () {
    Route::get('/aktivasi-akun', [VerificationController::class, 'show'])->name('first-login.show');
    Route::put('/aktivasi-akun', [VerificationController::class, 'update'])->name('first-login.update');
});

// --- 3. RUTE GLOBAL (DASHBOARD) ---
Route::middleware(['auth', 'password.changed'])->group(function () {

// Dashboard Cerdas
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->role === 'dosen') return redirect()->route('dosen.dashboard');

        // Logika Mahasiswa
        $dosen = $user->dosenPembimbing;
        
        // Ambil Data Bimbingan
        $logbooks = \App\Models\Bimbingan::where('mahasiswa_id', $user->id)
                             ->orderBy('tanggal_bimbingan', 'desc')
                             ->get();

        $revisiTerakhir = $logbooks->where('status', 'Revisi')->first();
        
        $jadwalMendatang = \App\Models\Jadwal::where('mahasiswa_id', $user->id)
                                             ->where('tanggal_pertemuan', '>=', now()->format('Y-m-d'))
                                             ->orderBy('tanggal_pertemuan', 'asc')
                                             ->with('dosen')
                                             ->first();

        $jumlahPerwalian = $logbooks->filter(fn($i) => str_contains($i->materi, 'Perwalian'))->count();
        $sisaPerwalian = max(0, 3 - $jumlahPerwalian);

        return view('dashboard', [
            'dosen' => $dosen,
            'logbooks' => $logbooks, // <--- INI PERBAIKANNYA (Data ini yang sebelumnya hilang)
            'logbooksTerkini' => $logbooks->take(3),
            'totalBimbingan' => $logbooks->count(),
            'statusTerkini' => $logbooks->first()->status ?? 'Belum Ada',
            'revisiTerakhir' => $revisiTerakhir,
            'jadwalMendatang' => $jadwalMendatang,
            'sisaPerwalian' => $sisaPerwalian,
            'jumlahPerwalian' => $jumlahPerwalian,
            'progressPercent' => 0, 
            'currentStep' => 1,     
            'jadwalSidang' => null  
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
        
        // --- 1. Logbook Bimbingan ---
        Route::get('/bimbingan', [BimbinganController::class, 'index'])->name('bimbingan.index');
        Route::post('/bimbingan', [BimbinganController::class, 'store'])->name('bimbingan.store');
        
        // Route UPDATE (Revisi) - Ini yang ditambahkan agar tombol "Perbaiki" jalan
        Route::put('/bimbingan/{id}', [BimbinganController::class, 'update'])->name('bimbingan.update');
        
        Route::delete('/bimbingan/{bimbingan}', [BimbinganController::class, 'destroy'])->name('bimbingan.destroy');
        Route::get('/bimbingan/cetak', [BimbinganController::class, 'cetak'])->name('bimbingan.cetak');

        // --- 2. Upload Dokumen Skripsi ---
        Route::get('/bimbingan/upload', [DokumenController::class, 'index'])->name('bimbingan.upload');
        Route::post('/bimbingan/upload', [DokumenController::class, 'store'])->name('bimbingan.upload.store');

        // --- 3. Jadwal & Booking ---
        Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
        Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
        Route::patch('/jadwal/{id}/approve-reschedule', [JadwalController::class, 'approveReschedule'])->name('jadwal.approveReschedule');
        Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
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
        
        // Data Mahasiswa
        Route::get('/mahasiswa', [DosenController::class, 'showMahasiswaList'])->name('mahasiswa.index');
        
        // Jadwal (Pastikan nama route ini sesuai view)
        Route::get('/kelola-jadwal', [DosenController::class, 'showJadwalValidasi'])->name('jadwal.index');
        Route::post('/kelola-jadwal', [DosenController::class, 'storeJadwalValidasi'])->name('kelola-jadwal');
        
        // Arsip
        Route::get('/arsip', [DosenController::class, 'showArsip'])->name('arsip.index');
    });

    // ====================================================
    // GRUP RUTE ADMIN
    // ====================================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        
        Route::get('/dosen', [AdminController::class, 'indexDosen'])->name('dosen.index');
        Route::post('/dosen', [AdminController::class, 'storeDosen'])->name('dosen.store');
        Route::get('/dosen/{dosen}/edit', [AdminController::class, 'editDosen'])->name('dosen.edit');
        Route::put('/dosen/{dosen}', [AdminController::class, 'updateDosen'])->name('dosen.update');
        Route::delete('/dosen/{dosen}', [AdminController::class, 'destroyDosen'])->name('dosen.destroy');

        Route::post('/mahasiswa/generate-kelas', [AdminController::class, 'generateKelas'])->name('mahasiswa.generate-kelas');
        Route::post('/mahasiswa/bulk-plotting', [AdminController::class, 'bulkPlotting'])->name('mahasiswa.bulk-plotting');
        Route::post('/mahasiswa/import', [AdminController::class, 'importMahasiswa'])->name('mahasiswa.import');
        Route::get('/mahasiswa/template', [AdminController::class, 'downloadTemplate'])->name('mahasiswa.template');

        Route::get('/mahasiswa', [AdminController::class, 'indexMahasiswa'])->name('mahasiswa.index');
        Route::get('/mahasiswa/create', [AdminController::class, 'createMahasiswa'])->name('mahasiswa.create');
        Route::post('/mahasiswa', [AdminController::class, 'storeMahasiswa'])->name('mahasiswa.store');
        Route::get('/mahasiswa/{mahasiswa}/edit', [AdminController::class, 'editMahasiswa'])->name('mahasiswa.edit');
        Route::put('/mahasiswa/{mahasiswa}', [AdminController::class, 'updateMahasiswa'])->name('mahasiswa.update');
        Route::delete('/mahasiswa/{mahasiswa}', [AdminController::class, 'destroyMahasiswa'])->name('mahasiswa.destroy');
        
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings.index');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });

});

require __DIR__.'/auth.php';