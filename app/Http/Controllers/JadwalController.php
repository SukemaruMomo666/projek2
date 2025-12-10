<?php

namespace App\Http\Controllers;

use App\Models\Bimbingan;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BimbinganController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil Logbook
        $logbooks = Bimbingan::where('mahasiswa_id', $user->id)
                             ->orderBy('tanggal_bimbingan', 'desc')
                             ->get();

        // 2. Hitung Perwalian
        $jumlahPerwalian = $logbooks->filter(function ($item) {
            return str_contains($item->materi, 'Perwalian');
        })->count();

        // 3. Ambil Jadwal ACC (Untuk Dropdown)
        $jadwalDisetujui = Jadwal::where('mahasiswa_id', $user->id)
                                 ->where('status', 'Disetujui')
                                 ->orderBy('tanggal_pertemuan', 'desc')
                                 ->get();

        return view('bimbingan.index', [
            'logbooks' => $logbooks,
            'jumlahPerwalian' => $jumlahPerwalian,
            'jadwalDisetujui' => $jadwalDisetujui
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_bimbingan' => 'required|date|before_or_equal:today',
            'tahapan'           => 'required|string',
            'detail_materi'     => 'required|string|max:200',
            'catatan_mahasiswa' => 'required|string',
            'file'              => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $mahasiswa = Auth::user();
        $materiGabungan = $request->tahapan . ': ' . $request->detail_materi;

        $data = [
            'mahasiswa_id'      => $mahasiswa->id,
            'dosen_id'          => $mahasiswa->dosen_pembimbing_id,
            'tanggal_bimbingan' => $request->tanggal_bimbingan,
            'materi'            => $materiGabungan,
            'catatan_mahasiswa' => $request->catatan_mahasiswa,
            'status'            => 'Menunggu',
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $mahasiswa->nim . '_' . date('Ymd_His') . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('bimbingan', $filename, 'public');
            $data['file_path'] = $path;
        }

        Bimbingan::create($data);

        return redirect()->route('bimbingan.index')->with('success', 'Logbook berhasil dicatat!');
    }
    
    public function destroy($id)
    {
        $logbook = Bimbingan::where('id', $id)->where('mahasiswa_id', Auth::id())->firstOrFail();
        
        if($logbook->status != 'Menunggu') {
            return back()->withErrors(['error' => 'Data validasi tidak bisa dihapus.']);
        }

        if($logbook->file_path && Storage::disk('public')->exists($logbook->file_path)) {
            Storage::disk('public')->delete($logbook->file_path);
        }

        $logbook->delete();
        return redirect()->route('bimbingan.index')->with('success', 'Data berhasil dihapus.');
    }

    public function cetak()
    {
        $logbooks = Bimbingan::where('mahasiswa_id', Auth::id())
                             ->orderBy('tanggal_bimbingan', 'asc')
                             ->get();
                             
        $mahasiswa = Auth::user();

        return view('bimbingan.cetak', compact('logbooks', 'mahasiswa'));
    }
}