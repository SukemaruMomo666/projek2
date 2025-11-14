<?php

namespace App\Http\Controllers;

use App\Models\Jadwal; // 1. Panggil Model Jadwal
use App\Models\User;   // 2. Panggil Model User (untuk data Dosen)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    /**
     * Menampilkan halaman jadwal & booking.
     */
    public function index()
    {
        $mahasiswa = Auth::user();

        // 1. Ambil data dosen pembimbing mahasiswa
        // Kita gunakan 'with' untuk mengambil relasi (jika ada)
        $dosenPembimbing = User::where('id', $mahasiswa->dosen_pembimbing_id)->first();
        
        // (Opsional: Nanti bisa diganti ambil semua dosen)
        $dosens = $dosenPembimbing ? [$dosenPembimbing] : [];

        // 2. Ambil data jadwal yang sudah di-booking oleh mahasiswa ini
        $jadwalSaya = Jadwal::where('mahasiswa_id', $mahasiswa->id)
                            ->orderBy('tanggal_pertemuan', 'desc')
                            ->get();

        // 3. Kirim kedua data itu ke view
        return view('bimbingan.jadwal', [
            'dosens' => $dosens,
            'jadwalSaya' => $jadwalSaya
        ]);
    }

    /**
     * Menyimpan pengajuan jadwal baru dari form modal.
     */
    public function store(Request $request)
    {
        // 1. Validasi data
        $request->validate([
            'dosen_id' => 'required|exists:users,id',
            'tanggal_pertemuan' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|string',
            'topik' => 'required|string|max:255',
        ]);

        // 2. Ambil data mahasiswa
        $mahasiswa = Auth::user();

        // 3. Siapkan data untuk disimpan
        $data = [
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $request->dosen_id,
            'tanggal_pertemuan' => $request->tanggal_pertemuan,
            'waktu_mulai' => $request->waktu_mulai,
            'topik' => $request->topik,
            'status' => 'Menunggu', // Status default saat booking
        ];

        // 4. Simpan ke database
        Jadwal::create($data);

        // 5. Kembalikan ke halaman jadwal dengan pesan sukses
        return redirect()->route('jadwal.index')->with('success', 'Pengajuan jadwal berhasil dikirim!');
    }
}