<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\Dokumen;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DosenController extends Controller
{
    /**
     * Menampilkan data dinamis untuk Dashboard Dosen.
     */
    public function index()
    {
        $dosen = Auth::user();
        $mahasiswaIds = $dosen->mahasiswaBimbingan->pluck('id');

        // Data untuk Statistik
        $logbookMenunggu = Bimbingan::whereIn('mahasiswa_id', $mahasiswaIds)
                                    ->where('status', 'Menunggu')
                                    ->with('mahasiswa')
                                    ->get();

        $dokumenMenunggu = Dokumen::whereIn('mahasiswa_id', $mahasiswaIds)
                                    ->where('status', 'Menunggu')
                                    ->with('mahasiswa')
                                    ->get();
        
        $totalMahasiswa = $mahasiswaIds->count();
        $totalReview = $logbookMenunggu->count() + $dokumenMenunggu->count();
        $totalJadwal = Jadwal::where('dosen_id', $dosen->id)
                                ->where('status', 'Menunggu')
                                ->count();
        
        // Data untuk Tabel Validasi Terbaru
        $validasiTerbaru = $logbookMenunggu->map(function ($item) {
            $item->tipe = 'Logbook';
            $item->judul = $item->materi;
            return $item;
        })->merge($dokumenMenunggu->map(function ($item) {
            $item->tipe = 'Dokumen';
            $item->judul = $item->kategori;
            return $item;
        }))->sortByDesc('created_at')->take(5);

        return view('dosen.dashboard', [
            'totalMahasiswa' => $totalMahasiswa,
            'totalReview' => $totalReview,
            'totalJadwal' => $totalJadwal,
            'validasiTerbaru' => $validasiTerbaru,
        ]);
    }

    // --- Validasi Logbook ---

    public function showLogbookValidasi()
    {
        $dosen = Auth::user();
        $logbooks = Bimbingan::where('dosen_id', $dosen->id)
                            ->with('mahasiswa')
                            ->orderBy('created_at', 'desc')
                            ->get();

        $logbookMenunggu = $logbooks->where('status', 'Menunggu');
        $logbookSelesai = $logbooks->whereIn('status', ['Disetujui', 'Revisi']);

        return view('dosen.validasi-logbook', [
            'logbookMenunggu' => $logbookMenunggu,
            'logbookSelesai' => $logbookSelesai,
        ]);
    }

    public function storeLogbookValidasi(Request $request)
    {
        $request->validate([
            'logbook_id' => 'required|exists:bimbingans,id',
            'catatan_dosen' => 'required|string',
            'status' => 'required|in:Disetujui,Revisi',
        ]);

        $logbook = Bimbingan::find($request->logbook_id);

        if ($logbook->dosen_id != Auth::id()) {
            abort(403);
        }

        $logbook->catatan_dosen = $request->catatan_dosen;
        $logbook->status = $request->status;
        $logbook->save();

        return redirect()->route('dosen.validasi.logbook.index')
                         ->with('success', 'Logbook berhasil direview!');
    }

    // --- Validasi Dokumen ---

    public function showDokumenValidasi()
    {
        $dosen = Auth::user();
        $dokumens = Dokumen::where('dosen_id', $dosen->id)
                            ->with('mahasiswa')
                            ->orderBy('created_at', 'desc')
                            ->get();

        $dokumenMenunggu = $dokumens->where('status', 'Menunggu');
        $dokumenSelesai = $dokumens->whereIn('status', ['Disetujui', 'Revisi']);

        return view('dosen.validasi-dokumen', [
            'dokumenMenunggu' => $dokumenMenunggu,
            'dokumenSelesai' => $dokumenSelesai,
        ]);
    }

    public function storeDokumenValidasi(Request $request)
    {
        $request->validate([
            'dokumen_id' => 'required|exists:dokumens,id',
            'catatan_dosen' => 'required|string',
            'status' => 'required|in:Disetujui,Revisi',
        ]);

        $dokumen = Dokumen::find($request->dokumen_id);

        if ($dokumen->dosen_id != Auth::id()) {
            abort(403);
        }

        $dokumen->catatan_dosen = $request->catatan_dosen;
        $dokumen->status = $request->status;
        $dokumen->save();

        return redirect()->route('dosen.validasi.dokumen.index')
                         ->with('success', 'Dokumen berhasil direview!');
    }

    // --- Data Mahasiswa ---
    
    public function showMahasiswaList()
    {
        $dosen = Auth::user();
        
        $mahasiswas = $dosen->mahasiswaBimbingan()
                           ->orderBy('semester', 'desc')
                           ->orderBy('name', 'asc')
                           ->get();

        return view('dosen.data-mahasiswa', [
            'mahasiswas' => $mahasiswas,
        ]);
    }

    // --- FUNGSI BARU UNTUK KELOLA JADWAL ---
    
    public function showJadwalValidasi()
    {
        $dosen = Auth::user();
        
        // Ambil semua jadwal yang ditujukan ke dosen ini
        $jadwals = Jadwal::where('dosen_id', $dosen->id)
                         ->with('mahasiswa')
                         ->orderBy('tanggal_pertemuan', 'desc')
                         ->get();

        // Pisahkan berdasarkan status
        $jadwalMenunggu = $jadwals->where('status', 'Menunggu');
        $jadwalSelesai = $jadwals->whereIn('status', ['Disetujui', 'Ditolak']);

        return view('dosen.kelola-jadwal', [ // Kita akan buat view ini
            'jadwalMenunggu' => $jadwalMenunggu,
            'jadwalSelesai' => $jadwalSelesai,
        ]);
    }

    public function storeJadwalValidasi(Request $request)
    {
        // 1. Validasi (Sederhana, karena hanya tombol)
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'status' => 'required|in:Disetujui,Ditolak',
        ]);

        // 2. Cari jadwalnya
        $jadwal = Jadwal::find($request->jadwal_id);

        // 3. Pastikan dosen ini berhak
        if ($jadwal->dosen_id != Auth::id()) {
            abort(403);
        }

        // 4. Update status
        $jadwal->status = $request->status;
        // Jika ditolak, tambahkan catatan (bisa dikembangkan nanti)
        if ($request->status == 'Ditolak') {
            $jadwal->catatan_dosen = "Ditolak oleh dosen.";
        }
        $jadwal->save();

        // 5. Kembalikan ke halaman kelola jadwal
        return redirect()->route('dosen.jadwal.index')
                         ->with('success', 'Jadwal berhasil di-update!');
    }
}