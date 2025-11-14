<?php

namespace App\Http\Controllers;

use App\Models\Bimbingan; // 1. Panggil Model kita
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 2. Panggil helper Autentikasi

class BimbinganController extends Controller
{
    /**
     * Menampilkan halaman logbook bimbingan.
     */
    public function index()
    {
        // 1. Ambil data bimbingan milik user yang sedang login
        $logbooks = Bimbingan::where('mahasiswa_id', Auth::id())
                              ->orderBy('tanggal_bimbingan', 'desc') // Urutkan dari terbaru
                              ->get();

        // 2. Kirim data $logbooks ke view
        return view('bimbingan.index', [
            'logbooks' => $logbooks
        ]);
    }

    /**
     * Menyimpan data logbook baru dari form modal.
     */
    public function store(Request $request)
    {
        // 1. Validasi data yang masuk dari form
        $request->validate([
            'tanggal_bimbingan' => 'required|date',
            'materi' => 'required|string|max:255',
            'catatan_mahasiswa' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // Maks 5MB
        ]);

        // 2. Ambil data user yang sedang login
        $mahasiswa = Auth::user();

        // 3. Siapkan data untuk disimpan
        $data = [
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $mahasiswa->dosen_pembimbing_id, // Ambil ID Dosen dari data Mahasiswa
            'tanggal_bimbingan' => $request->tanggal_bimbingan,
            'materi' => $request->materi,
            'catatan_mahasiswa' => $request->catatan_mahasiswa,
            'status' => 'Menunggu', // Default status saat pertama kali dibuat
        ];

        // 4. Jika ada file yang di-upload
        if ($request->hasFile('file')) {
            // Simpan file di: storage/app/public/bimbingan
            // Nama file akan di-hash agar unik
            $path = $request->file('file')->store('bimbingan', 'public');
            $data['file_path'] = $path;
        }

        // 5. Simpan ke database
        Bimbingan::create($data);

        // 6. Kembalikan ke halaman logbook dengan pesan sukses
        return redirect()->route('bimbingan.index')->with('success', 'Logbook berhasil dicatat!');
    }
}