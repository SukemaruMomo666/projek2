<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bimbingan;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // 1. Buat Dosen
        // Kita pakai firstOrCreate agar tidak error jika dijalankan 2x
        $dosen = User::firstOrCreate(
            ['email' => 'dosen@kampus.ac.id'],
            [
                'name' => 'Dr. Budi Santoso, M.Kom',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'nidn' => '0412345678',
            ]
        );

        // 2. Buat Mahasiswa (Kamu Login Pake Ini)
        $mhs = User::firstOrCreate(
            ['email' => 'mhs@kampus.ac.id'],
            [
                'name' => 'Ahmad Mahasiswa',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '10123456',
                'semester' => 8,
                'prodi' => 'Sistem Informasi',
                'dosen_pembimbing_id' => $dosen->id,
            ]
        );

        // 3. Buat Data Bimbingan Dummy
        Bimbingan::create([
            'mahasiswa_id' => $mhs->id,
            'dosen_id' => $dosen->id,
            'tanggal_bimbingan' => Carbon::now()->subDays(5),
            'materi' => 'Pengajuan Judul Skripsi',
            'catatan_mahasiswa' => 'Saya mengajukan judul tentang AI.',
            'catatan_dosen' => 'Judul diterima, lanjut Bab 1',
            'status' => 'Disetujui'
        ]);

        Bimbingan::create([
            'mahasiswa_id' => $mhs->id,
            'dosen_id' => $dosen->id,
            'tanggal_bimbingan' => Carbon::now()->subDays(2),
            'materi' => 'Revisi Bab 1',
            'catatan_mahasiswa' => 'Perbaikan latar belakang.',
            'status' => 'Revisi'
        ]);

        // 4. Buat Jadwal
        Jadwal::create([
            'mahasiswa_id' => $mhs->id,
            'dosen_id' => $dosen->id,
            'tanggal_pertemuan' => Carbon::tomorrow(),
            'waktu_mulai' => '10:00',
            'topik' => 'Bimbingan Bab 2',
            'status' => 'Menunggu'
        ]);
    }
}