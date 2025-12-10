<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\User;
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

        // 1. Ambil data dosen pembimbing
        // Kita jadikan array agar bisa di-loop di view (persiapan jika nanti ada dosen 2)
        $dosenPembimbing = User::find($mahasiswa->dosen_pembimbing_id);
        $dosens = $dosenPembimbing ? [$dosenPembimbing] : [];

        // 2. Ambil data jadwal mahasiswa (diurutkan dari yang terbaru)
        $jadwalSaya = Jadwal::where('mahasiswa_id', $mahasiswa->id)
                            ->with('dosen') // Eager load data dosen agar efisien
                            ->orderBy('tanggal_pertemuan', 'desc')
                            ->get();

return view('bimbingan.jadwal', [ 
            'dosens' => $dosens,
            'jadwalSaya' => $jadwalSaya
        ]);
    }

    /**
     * Menyimpan pengajuan jadwal baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dosen_id'          => 'required|exists:users,id',
            'tanggal_pertemuan' => 'required|date|after_or_equal:today', // Tidak boleh tanggal kemarin
            'waktu_mulai'       => 'required|string',
            'topik'             => 'required|string|max:255',
        ]);

        $mahasiswa = Auth::user();

        // Cek apakah dosen yang dipilih benar dosen pembimbingnya (Security Check)
        if ($request->dosen_id != $mahasiswa->dosen_pembimbing_id) {
            return back()->withErrors(['dosen' => 'Anda hanya bisa mengajukan jadwal ke dosen pembimbing Anda.']);
        }

        Jadwal::create([
            'mahasiswa_id'      => $mahasiswa->id,
            'dosen_id'          => $request->dosen_id,
            'tanggal_pertemuan' => $request->tanggal_pertemuan,
            'waktu_mulai'       => $request->waktu_mulai,
            'topik'             => $request->topik,
            'status'            => 'Menunggu',
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Pengajuan jadwal berhasil dikirim! Menunggu konfirmasi dosen.');
    }

    /**
     * Menghapus atau Membatalkan Jadwal.
     * Juga digunakan untuk MENOLAK tawaran reschedule dari dosen.
     */
    public function destroy($id)
    {
        $jadwal = Jadwal::where('id', $id)->where('mahasiswa_id', Auth::id())->firstOrFail();

        // Validasi: Hanya boleh hapus jika status Menunggu, Reschedule, atau Ditolak
        // Jika sudah Disetujui, idealnya tidak boleh dihapus sembarangan (atau butuh konfirmasi extra)
        if ($jadwal->status == 'Disetujui') {
            return back()->withErrors(['error' => 'Jadwal yang sudah disetujui tidak dapat dibatalkan dari sini.']);
        }

        $jadwal->delete();

        return redirect()->route('jadwal.index')->with('success', 'Pengajuan jadwal berhasil dibatalkan/dihapus.');
    }

    /**
     * [FITUR BARU] Menyetujui Reschedule dari Dosen.
     * Fungsi ini dipanggil ketika mahasiswa klik tombol "Setuju" pada status Reschedule.
     */
    public function approveReschedule($id)
    {
        $jadwal = Jadwal::where('id', $id)->where('mahasiswa_id', Auth::id())->firstOrFail();

        // Pastikan statusnya memang sedang Reschedule dan ada waktu barunya
        if ($jadwal->status == 'Reschedule' && $jadwal->waktu_reschedule) {
            
            // 1. Update tanggal pertemuan asli dengan tanggal saran dari dosen
            // Format waktu_reschedule biasanya "Y-m-d H:i:s" (datetime)
            // Kita perlu memisahkan tanggal dan jam karena di database terpisah (opsional tergantung struktur DB kamu)
            
            // Asumsi kolom di DB: 'tanggal_pertemuan' (Date) dan 'waktu_mulai' (Time/String)
            $waktuBaru = \Carbon\Carbon::parse($jadwal->waktu_reschedule);
            
            $jadwal->tanggal_pertemuan = $waktuBaru->format('Y-m-d');
            $jadwal->waktu_mulai       = $waktuBaru->format('H:i'); // Ambil jamnya saja
            
            // 2. Ubah status jadi Disetujui
            $jadwal->status = 'Disetujui';
            
            // 3. Bersihkan kolom reschedule agar bersih
            $jadwal->waktu_reschedule = null;
            
            $jadwal->save();

            return redirect()->route('jadwal.index')->with('success', 'Jadwal reschedule berhasil disetujui! Pertemuan telah dijadwalkan.');
        }

        return back()->withErrors(['error' => 'Gagal memproses persetujuan reschedule.']);
    }
}