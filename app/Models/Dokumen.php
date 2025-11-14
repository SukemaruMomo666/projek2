<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal (lewat form).
     */
    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'kategori', // Cth: "Bab 1", "Proposal"
        'keterangan', // Pesan dari mahasiswa
        'nama_file_asli',
        'file_path',
        'status',
    ];

    /**
     * Relasi: Satu Dokumen dimiliki oleh satu Mahasiswa (User).
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    /**
     * Relasi: Satu Dokumen dimiliki oleh satu Dosen (User).
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}
