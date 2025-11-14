<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bimbingan extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal (lewat form).
     */
    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'tanggal_bimbingan',
        'materi',
        'catatan_mahasiswa',
        'file_path',
        'status',
    ];

    /**
     * Memberitahu Laravel bahwa 'tanggal_bimbingan' adalah objek Tanggal (Carbon).
     */
    protected $casts = [
        'tanggal_bimbingan' => 'date',
    ];

    /**
     * Relasi: Satu Bimbingan dimiliki oleh satu Mahasiswa (User).
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    /**
     * Relasi: Satu Bimbingan dimiliki oleh satu Dosen (User).
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}
