<?php

namespace App\Http\Controllers;

use App\Models\Dokumen; // 1. Panggil Model Dokumen
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DokumenController extends Controller
{
    /**
     * Menampilkan halaman upload dan riwayat dokumen.
     */
    public function index()
    {
        // 1. Ambil semua dokumen milik user yang sedang login
        $dokumens = Dokumen::where('mahasiswa_id', Auth::id())
                            ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
                            ->get();

        // 2. Kirim data $dokumens ke view
        return view('bimbingan.upload', [
            'dokumens' => $dokumens
        ]);
    }

    /**
     * Menyimpan file dokumen baru dari form upload.
     */
    public function store(Request $request)
    {
        // 1. Validasi data
        $request->validate([
            'kategori' => 'required|string',
            'file_skripsi' => 'required|file|mimes:pdf,doc,docx|max:10240', // Maks 10MB
            'keterangan' => 'nullable|string|max:500',
        ]);

        // 2. Ambil data user
        $mahasiswa = Auth::user();
        $file = $request->file('file_skripsi');

        // 3. Simpan file ke storage
        // Folder: storage/app/public/dokumen_skripsi
        $path = $file->store('dokumen_skripsi', 'public');
        
        // 4. Siapkan data untuk disimpan ke database
        $data = [
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $mahasiswa->dosen_pembimbing_id,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'nama_file_asli' => $file->getClientOriginalName(), // Nama file asli
            'file_path' => $path,
            'status' => 'Menunggu',
        ];

        // 5. Simpan ke database
        Dokumen::create($data);

        // 6. Kembalikan ke halaman upload dengan pesan sukses
        return redirect()->route('bimbingan.upload')->with('success', 'Dokumen berhasil di-upload!');
    }
}