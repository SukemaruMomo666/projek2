<?php

namespace App\Http\Controllers;

use App\Models\Bimbingan;
use App\Models\Jadwal; // Pastikan Model Jadwal di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BimbinganController extends Controller
{
    /**
     * Menampilkan halaman logbook bimbingan.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil data bimbingan milik user yang sedang login
        $logbooks = Bimbingan::where('mahasiswa_id', $user->id)
                             ->orderBy('tanggal_bimbingan', 'desc') // Urutkan dari terbaru
                             ->get();

        // 2. HITUNG JUMLAH PERWALIAN (Untuk logic "Wajib 3x" bagi mahasiswa Junior)
        // Kita cari logbook yang materinya mengandung kata "Perwalian"
        $jumlahPerwalian = $logbooks->filter(function ($item) {
            return str_contains($item->materi, 'Perwalian');
        })->count();

        // 3. [INTEGRASI JADWAL] Ambil Jadwal yang SUDAH DISETUJUI (ACC)
        // Jadwal ini akan muncul di dropdown modal "Tambah Bimbingan"
        $jadwalDisetujui = Jadwal::where('mahasiswa_id', $user->id)
                                 ->where('status', 'Disetujui')
                                 ->orderBy('tanggal_pertemuan', 'desc')
                                 ->get();

        // 4. Kirim semua data ke view
        return view('bimbingan.index', [
            'logbooks' => $logbooks,
            'jumlahPerwalian' => $jumlahPerwalian,
            'jadwalDisetujui' => $jadwalDisetujui
        ]);
    }

    /**
     * Menyimpan data logbook baru.
     */
    public function store(Request $request)
    {
        // 1. VALIDASI DATA
        $request->validate([
            'tanggal_bimbingan' => 'required|date|before_or_equal:today', // Tidak boleh tanggal besok/masa depan
            'tahapan'           => 'required|string',                      // Dari Dropdown (Jenis Kegiatan)
            'detail_materi'     => 'required|string|max:200',              // Dari Input Text (Topik Pembahasan)
            'catatan_mahasiswa' => 'required|string',
            'file'              => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // Maks 5MB, support dokumen & gambar
        ]);

        $mahasiswa = Auth::user();

        // Cek Safety: Pastikan mahasiswa punya dosen pembimbing
        if (!$mahasiswa->dosen_pembimbing_id) {
            return redirect()->back()->withErrors(['dosen' => 'Anda belum memiliki Dosen Pembimbing. Silakan hubungi admin.']);
        }

        // 2. LOGIKA PENGGABUNGAN MATERI
        // Format di Database: "Jenis Kegiatan: Topik Pembahasan"
        // Contoh: "Perwalian Ke-1: Diskusi KRS Semester 3" atau "Bab 1: Revisi Latar Belakang"
        $materiGabungan = $request->tahapan . ': ' . $request->detail_materi;

        // 3. SIAPKAN DATA
        $data = [
            'mahasiswa_id'      => $mahasiswa->id,
            'dosen_id'          => $mahasiswa->dosen_pembimbing_id,
            'tanggal_bimbingan' => $request->tanggal_bimbingan,
            'materi'            => $materiGabungan, // Masukkan hasil gabungan tadi
            'catatan_mahasiswa' => $request->catatan_mahasiswa,
            'status'            => 'Menunggu',
        ];

        // 4. HANDLE UPLOAD FILE (CUSTOM NAME)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Buat nama file unik: NIM_TANGGAL_TIMESTAMP.ext
            // Contoh: 10112001_20251210_170218.pdf
            $filename = $mahasiswa->nim . '_' . date('Ymd') . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke folder 'public/bimbingan'
            $path = $file->storeAs('bimbingan', $filename, 'public');
            
            $data['file_path'] = $path;
        }

        // 5. SIMPAN KE DATABASE
        Bimbingan::create($data);

        // 6. KEMBALI
        return redirect()->route('bimbingan.index')->with('success', 'Logbook berhasil dicatat! Menunggu validasi dosen.');
    }
    
    /**
     * Menghapus Logbook
     */
    public function destroy($id)
    {
        // Cari data logbook, pastikan milik user yang sedang login (Security Check)
        $logbook = Bimbingan::where('id', $id)->where('mahasiswa_id', Auth::id())->firstOrFail();
        
        // Cek status, hanya boleh hapus jika masih "Menunggu"
        if($logbook->status != 'Menunggu') {
            return back()->withErrors(['error' => 'Data yang sudah divalidasi atau direvisi tidak bisa dihapus.']);
        }

        // Hapus file fisik jika ada
        if($logbook->file_path && Storage::disk('public')->exists($logbook->file_path)) {
            Storage::disk('public')->delete($logbook->file_path);
        }

        $logbook->delete();

        return redirect()->route('bimbingan.index')->with('success', 'Data bimbingan berhasil dihapus.');
    }

    /**
     * Menampilkan halaman khusus cetak (Print Friendly).
     */
    public function cetak()
    {
        $logbooks = Bimbingan::where('mahasiswa_id', Auth::id())
                             ->orderBy('tanggal_bimbingan', 'asc') // Cetak urut dari awal (Kronologis)
                             ->get();
                             
        $mahasiswa = Auth::user();

        return view('bimbingan.cetak', compact('logbooks', 'mahasiswa'));
    }
}