<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\Dokumen;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DosenController extends Controller
{
    /**
     * Dashboard Utama Dosen (Data Real-Time)
     */
    public function index()
    {
        $dosenId = Auth::id();

        // 1. STATISTIK UTAMA
        $totalMahasiswa = User::where('role', 'mahasiswa')->where('dosen_pembimbing_id', $dosenId)->count();
        $menungguReview = Bimbingan::where('dosen_id', $dosenId)->where('status', 'Menunggu')->count();
        $jadwalHariIni = Jadwal::where('dosen_id', $dosenId)->where('status', 'Disetujui')->whereDate('tanggal_pertemuan', Carbon::today())->count();
        
        // Logika Siap Sidang: Mahasiswa yang Logbook Bab 5-nya sudah ACC
        $siapSidang = Bimbingan::where('dosen_id', $dosenId)
                               ->where('materi', 'like', '%Bab 5%')
                               ->where('status', 'Disetujui')
                               ->distinct('mahasiswa_id')
                               ->count();

        // 2. TABEL: LOGBOOK PERLU VALIDASI (5 Teratas)
        $latestValidations = Bimbingan::with('mahasiswa')
                                      ->where('dosen_id', $dosenId)
                                      ->where('status', 'Menunggu')
                                      ->orderBy('created_at', 'desc')
                                      ->take(5)
                                      ->get();

        // 3. TABEL: JADWAL MENUNGGU KONFIRMASI (Agar muncul di dashboard)
        $jadwalPending = Jadwal::with('mahasiswa')
                               ->where('dosen_id', $dosenId)
                               ->whereIn('status', ['Menunggu', 'Reschedule']) // Tampilkan juga yang sedang Reschedule (nunggu mhs)
                               ->orderBy('tanggal_pertemuan', 'asc')
                               ->get();

        return view('dosen.dashboard', compact(
            'totalMahasiswa', 
            'menungguReview', 
            'jadwalHariIni', 
            'siapSidang', 
            'latestValidations',
            'jadwalPending'
        ));
    }

    // ========================================================================
    // VALIDASI LOGBOOK
    // ========================================================================

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

    // ========================================================================
    // VALIDASI DOKUMEN
    // ========================================================================

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

    // ========================================================================
    // DATA MAHASISWA
    // ========================================================================
    
    public function showMahasiswaList()
    {
        $dosen = Auth::user();
        
        $mahasiswas = User::where('dosen_pembimbing_id', $dosen->id)
                           ->where('role', 'mahasiswa')
                           ->orderBy('semester', 'desc')
                           ->orderBy('name', 'asc')
                           ->get();

        return view('dosen.data-mahasiswa', [
            'mahasiswas' => $mahasiswas,
        ]);
    }

    // ========================================================================
    // KELOLA JADWAL (FITUR TERIMA / TOLAK / RESCHEDULE)
    // ========================================================================
    
    public function showJadwalValidasi()
    {
        $dosen = Auth::user();
        
        $jadwals = Jadwal::where('dosen_id', $dosen->id)
                         ->with('mahasiswa')
                         ->orderBy('tanggal_pertemuan', 'desc')
                         ->get();

        $jadwalMenunggu = $jadwals->whereIn('status', ['Menunggu', 'Reschedule']); 
        $jadwalSelesai = $jadwals->whereIn('status', ['Disetujui', 'Ditolak']);

        return view('dosen.kelola-jadwal', [
            'jadwalMenunggu' => $jadwalMenunggu,
            'jadwalSelesai' => $jadwalSelesai,
        ]);
    }

    public function storeJadwalValidasi(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'aksi'      => 'required|in:terima,tolak,reschedule',
            'pesan'     => 'nullable|string',
            'tanggal_baru' => 'required_if:aksi,reschedule|nullable|date',
            'jam_baru'     => 'required_if:aksi,reschedule|nullable',
        ]);

        $jadwal = Jadwal::find($request->jadwal_id);

        if ($jadwal->dosen_id != Auth::id()) {
            abort(403);
        }

        // LOGIKA UTAMA
        if ($request->aksi == 'terima') {
            $jadwal->status = 'Disetujui';
            if($request->pesan) $jadwal->catatan_dosen = $request->pesan;
        
        } elseif ($request->aksi == 'tolak') {
            $jadwal->status = 'Ditolak';
            $jadwal->catatan_dosen = $request->pesan ?? 'Jadwal tidak cocok.';
        
        } elseif ($request->aksi == 'reschedule') {
            $jadwal->status = 'Reschedule';
            $jadwal->waktu_reschedule = $request->tanggal_baru . ' ' . $request->jam_baru;
            $jadwal->catatan_dosen = $request->pesan ?? 'Saya mengajukan waktu pengganti.';
        }

        $jadwal->save();

        // Redirect kembali (bisa ke dashboard atau halaman kelola jadwal)
        return back()->with('success', 'Status jadwal berhasil diperbarui.');
    }
    
    // --- Arsip ---
    public function showArsip() {
        return view('dosen.arsip');
    }
}