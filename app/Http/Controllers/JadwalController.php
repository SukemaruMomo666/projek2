<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user();
        $dosens = User::where('role', 'dosen')->orderBy('name', 'asc')->get();
        $jadwalSaya = Jadwal::where('mahasiswa_id', $mahasiswa->id)->with('dosen')->orderBy('tanggal_pertemuan', 'desc')->get();

        // PERBAIKAN: Arahkan ke 'bimbingan.jadwal'
        return view('bimbingan.jadwal', [ 
            'dosens' => $dosens,
            'jadwalSaya' => $jadwalSaya
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|exists:users,id',
            'tanggal_pertemuan' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|string',
            'topik' => 'required|string|max:255',
        ]);

        $mahasiswa = Auth::user();
        Jadwal::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $request->dosen_id,
            'tanggal_pertemuan' => $request->tanggal_pertemuan,
            'waktu_mulai' => $request->waktu_mulai,
            'topik' => $request->topik,
            'status' => 'Menunggu',
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Pengajuan jadwal berhasil dikirim!');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::where('id', $id)->where('mahasiswa_id', Auth::id())->firstOrFail();
        if ($jadwal->status == 'Disetujui') return back()->withErrors(['error' => 'Jadwal yang sudah disetujui tidak dapat dibatalkan.']);
        $jadwal->delete();
        return redirect()->route('jadwal.index')->with('success', 'Pengajuan jadwal dibatalkan.');
    }

    public function approveReschedule($id)
    {
        $jadwal = Jadwal::where('id', $id)->where('mahasiswa_id', Auth::id())->firstOrFail();
        if ($jadwal->status == 'Reschedule' && $jadwal->waktu_reschedule) {
            $waktuBaru = Carbon::parse($jadwal->waktu_reschedule);
            $jadwal->tanggal_pertemuan = $waktuBaru->format('Y-m-d');
            $jadwal->waktu_mulai = $waktuBaru->format('H:i');
            $jadwal->status = 'Disetujui';
            $jadwal->waktu_reschedule = null;
            $jadwal->save();
            return redirect()->route('jadwal.index')->with('success', 'Jadwal reschedule berhasil disetujui!');
        }
        return back()->withErrors(['error' => 'Gagal memproses persetujuan.']);
    }
}