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
use App\Http\Controllers\AdminController; // <--- PERBAIKAN DI SINI

// Model
use App\Models\Bimbingan;

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
     * Rute Dashboard Cerdas (Logic Redirector)
     * Mengarahkan User sesuai Role-nya
     */
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        // 1. Cek jika Admin
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // 2. Cek jika Dosen
        if ($user->role === 'dosen') {
            return redirect()->route('dosen.dashboard');
        }

        // 3. Sisanya adalah Mahasiswa
        // --- Logika Dashboard Mahasiswa ---
        $dosen = $user->dosenPembimbing;
        
        $logbooks = Bimbingan::where('mahasiswa_id', $user->id)
                            ->orderBy('tanggal_bimbingan', 'desc')
                            ->get();
        
        $logbooksTerkini = $logbooks->take(3);
        $totalBimbingan = $logbooks->count();
        $statusTerkini = $logbooks->first()->status ?? 'Belum Ada';
        
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
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {

    // Logbook
    Route::get('/bimbingan', [BimbinganController::class, 'index'])->name('bimbingan.index');
    Route::post('/bimbingan', [BimbinganController::class, 'store'])->name('bimbingan.store');

    // Dokumen
    Route::get('/bimbingan/upload', [DokumenController::class, 'index'])->name('bimbingan.upload');
    Route::post('/bimbingan/upload', [DokumenController::class, 'store'])->name('bimbingan.upload.store');

    // Jadwal
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
});


// --- 4. RUTE KHUSUS DOSEN ---
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DosenController::class, 'index'])->name('dashboard');

    // Validasi
    Route::get('/validasi-logbook', [DosenController::class, 'showLogbookValidasi'])->name('validasi.logbook.index');
    Route::post('/validasi-logbook', [DosenController::class, 'storeLogbookValidasi'])->name('validasi.logbook.store');
    
    // Validasi Dokumen (Perbaikan Rute)
    Route::get('/validasi-dokumen', [DosenController::class, 'showDokumenValidasi'])->name('validasi.dokumen.index');
    Route::post('/validasi-dokumen', [DosenController::class, 'storeDokumenValidasi'])->name('validasi.dokumen.store');

    // Data Mahasiswa & Jadwal
    Route::get('/mahasiswa', [DosenController::class, 'showMahasiswaList'])->name('mahasiswa.index');
    Route::get('/kelola-jadwal', [DosenController::class, 'showJadwalValidasi'])->name('jadwal.index');
    Route::post('/kelola-jadwal', [DosenController::class, 'storeJadwalValidasi'])->name('jadwal.store');

    Route::get('/arsip', function() {
        return "Halaman Arsip Skripsi (Dalam Pengerjaan)";
    })->name('arsip.index');
});


// --- 5. RUTE KHUSUS ADMIN (BARU) ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // --- Kelola Dosen ---
    Route::get('/dosen', [AdminController::class, 'indexDosen'])->name('dosen.index');
    Route::get('/dosen/create', [AdminController::class, 'createDosen'])->name('dosen.create');
    Route::post('/dosen', [AdminController::class, 'storeDosen'])->name('dosen.store');
    Route::get('/dosen/{dosen}/edit', [AdminController::class, 'editDosen'])->name('dosen.edit');
    Route::put('/dosen/{dosen}', [AdminController::class, 'updateDosen'])->name('dosen.update');
    Route::delete('/dosen/{dosen}', [AdminController::class, 'destroyDosen'])->name('dosen.destroy');

    // --- Kelola Mahasiswa ---
    Route::get('/mahasiswa', [AdminController::class, 'indexMahasiswa'])->name('mahasiswa.index');
    Route::get('/mahasiswa/create', [AdminController::class, 'createMahasiswa'])->name('mahasiswa.create');
    Route::post('/mahasiswa', [AdminController::class, 'storeMahasiswa'])->name('mahasiswa.store');
    Route::get('/mahasiswa/{mahasiswa}/edit', [AdminController::class, 'editMahasiswa'])->name('mahasiswa.edit');
    Route::put('/mahasiswa/{mahasiswa}', [AdminController::class, 'updateMahasiswa'])->name('mahasiswa.update');
    Route::delete('/mahasiswa/{mahasiswa}', [AdminController::class, 'destroyMahasiswa'])->name('mahasiswa.destroy');
Route::post('/mahasiswa/import', [AdminController::class, 'importMahasiswa'])->name('mahasiswa.import');
    // ... (Rute Kelola Mahasiswa)
    
    // --- PENGATURAN SISTEM ---
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    
});

require __DIR__.'/auth.php';