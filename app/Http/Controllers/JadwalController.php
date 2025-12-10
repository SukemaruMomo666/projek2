<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * Menampilkan halaman jadwal & booking.
     */
    public function index()
    {
        $mahasiswa = Auth::user();

        // 1. Ambil SEMUA Dosen (agar bisa booking siapa saja, misal Kaprodi/Dosen Lomba)
        // Kita urutkan berdasarkan nama agar rapi
        $dosens = User::where('role', 'dosen')
                      ->orderBy('name', 'asc')
                      ->get();

        // 2. Ambil jadwal milik mahasiswa ini (urutkan dari yang terbaru)
        $jadwalSaya = Jadwal::where('mahasiswa_id', $mahasiswa->id)
                            ->with('dosen') // Eager load relasi dosen biar cepat
                            ->orderBy('tanggal_pertemuan', 'desc')
                            ->get();

        // PERBAIKAN: Arahkan ke folder 'bimbingan' karena file ada di resources/views/bimbingan/jadwal.blade.php
        return view('bimbingan.jadwal', [ 
            'dosens'     => $dosens,
            'jadwalSaya' => $jadwalSaya
        ]);
    }

    /**
     * Menyimpan pengajuan jadwal baru.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'dosen_id'          => 'required|exists:users,id',
            'tanggal_pertemuan' => 'required|date|after_or_equal:today', // Tidak boleh tanggal lampau
            'waktu_mulai'       => 'required|string',
            'topik'             => 'required|string|max:255',
        ]);

        $mahasiswa = Auth::user();

        // 2. Simpan ke Database
        Jadwal::create([
            'mahasiswa_id'      => $mahasiswa->id,
            'dosen_id'          => $request->dosen_id,
            'tanggal_pertemuan' => $request->tanggal_pertemuan,
            'waktu_mulai'       => $request->waktu_mulai,
            'topik'             => $request->topik,
            'status'            => 'Menunggu', // Default status awal
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Pengajuan jadwal berhasil dikirim! Menunggu konfirmasi dosen.');
    }

    /**
     * Menghapus atau Membatalkan Jadwal.
     */
    public function destroy($id)
    {
        // Cari jadwal, pastikan milik user yang sedang login (Security Check)
        $jadwal = Jadwal::where('id', $id)->where('mahasiswa_id', Auth::id())->firstOrFail();

        // Hanya boleh hapus jika status BUKAN 'Disetujui'
        if ($jadwal->status == 'Disetujui') {
            return back()->withErrors(['error' => 'Jadwal yang sudah disetujui tidak dapat dibatalkan.']);
        }

        $jadwal->delete();

        return redirect()->route('jadwal.index')->with('success', 'Pengajuan jadwal berhasil dibatalkan/dihapus.');
    }

    /**
     * [FITUR RESCHEDULE] Menyetujui waktu baru dari Dosen.
     */
    public function approveReschedule($id)
    {
        $jadwal = Jadwal::where('id', $id)->where('mahasiswa_id', Auth::id())->firstOrFail();

        // Pastikan statusnya memang sedang 'Reschedule' dan ada data waktu barunya
        if ($jadwal->status == 'Reschedule' && $jadwal->waktu_reschedule) {
            
            // 1. Ambil waktu baru yang ditawarkan dosen
            $waktuBaru = Carbon::parse($jadwal->waktu_reschedule);
            
            // 2. Update data jadwal asli dengan data baru
            $jadwal->tanggal_pertemuan = $waktuBaru->format('Y-m-d');
            $jadwal->waktu_mulai       = $waktuBaru->format('H:i');
            
            // 3. Ubah status jadi 'Disetujui' (Deal)
            $jadwal->status = 'Disetujui';
            
            // 4. Bersihkan kolom reschedule (reset)
            $jadwal->waktu_reschedule = null;
            
            $jadwal->save();

            return redirect()->route('jadwal.index')->with('success', 'Jadwal reschedule berhasil disetujui! Pertemuan telah dijadwalkan.');
        }

        return back()->withErrors(['error' => 'Gagal memproses persetujuan reschedule.']);
    }
}