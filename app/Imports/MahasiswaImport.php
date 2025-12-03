<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MahasiswaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan NIM belum ada di database untuk menghindari duplikat
        if (User::where('nim', $row['nim'])->exists()) {
            return null; 
        }

        return new User([
            'name'     => $row['nama'],
            'email'    => $row['email'],
            'nim'      => $row['nim'],
            'password' => Hash::make($row['nim']), // Default password = NIM
            'role'     => 'mahasiswa',
            'prodi'    => $row['prodi'] ?? 'D3 Teknik Informatika',
            'semester' => $row['semester'] ?? 1,
            'email_verified_at' => now(),
        ]);
    }
}