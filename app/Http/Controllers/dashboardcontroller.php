<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Bimbingan;
use App\Models\Jadwal;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil User yang sedang Login
        $user = Auth::user();

        // 2. Ambil Data Bimbingan (Logbook)
        // Mengambil 5 data terakhir untuk ditampilkan di tabel
        $logbooksTerkini = Bimbingan::where('mahasiswa_id', $user->id)
                            ->orderBy('tanggal_bimbingan', 'desc')
                            ->take(5)
                            ->get();

        // Hitung total semua bimbingan
        $totalBimbingan = Bimbingan::where('mahasiswa_id', $user->id)->count();

        // 3. LOGIKA PROGRESS & TAHAPAN (Otomatis berdasarkan jumlah ACC)
        // Kita hitung berapa kali bimbingan yang statusnya 'Disetujui'
        $bimbinganDisetujui = Bimbingan::where('mahasiswa_id', $user->id)
                                ->where('status', 'Disetujui')
                                ->count();
        
        // Rumus Progress: Anggap butuh 10x ACC untuk selesai (100%)
        // Jika baru 2x ACC, berarti 20%. Maksimal mentok di 100%.
        $progressPercent = min($bimbinganDisetujui * 10, 100); 

        // Rumus Tahapan (Stepper 1-5)
        $currentStep = 1; // Default: Masih tahap pengajuan Judul
        if ($bimbinganDisetujui >= 1) $currentStep = 2; // Judul ACC -> Lanjut Bab 1
        if ($bimbinganDisetujui >= 3) $currentStep = 3; // Bab 1 ACC -> Lanjut Bab 2-3
        if ($bimbinganDisetujui >= 6) $currentStep = 4; // Bab 2-3 ACC -> Lanjut Bab 4-5
        if ($bimbinganDisetujui >= 9) $currentStep = 5; // Siap Sidang

        // 4. Ambil Jadwal Sidang Terakhir
        // Kita ambil data jadwal paling baru milik mahasiswa ini
        $jadwalSidang = Jadwal::where('mahasiswa_id', $user->id)
                        ->orderBy('tanggal_pertemuan', 'desc')
                        ->first(); 
        // Note: Variabel ini bisa bernilai NULL jika belum ada jadwal, itu normal.

        // 5. Ambil Data Dosen Pembimbing
        // Mengambil data user berdasarkan ID yang ada di kolom dosen_pembimbing_id
        $dosen = null;
        if ($user->dosen_pembimbing_id) {
            $dosen = User::find($user->dosen_pembimbing_id);
        }

        // 6. Tentukan Status Terkini untuk ditampilkan di Card Kuning
        $statusTerkini = "Belum Mengajukan";
        if ($logbooksTerkini->isNotEmpty()) {
            // Ambil status dari logbook paling atas (paling baru)
            $lastStatus = $logbooksTerkini->first()->status;
            $statusTerkini = "Status: " . $lastStatus;
        }

        // 7. KIRIM SEMUA DATA KE VIEW
        // Pastikan semua nama variabel di sini sama dengan yang dipanggil di Blade
        return view('mahasiswa.dashboard', compact(
            'user',
            'logbooksTerkini', 
            'totalBimbingan', 
            'progressPercent',   // Ini untuk progress bar
            'currentStep',       // Ini untuk stepper bulat-bulat
            'jadwalSidang',      // Ini untuk card jadwal sidang
            'dosen',             // Ini untuk card dosen pembimbing
            'statusTerkini'      // Ini untuk card status
        ));
    }
}