<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'tanggal_pertemuan',
        'waktu_mulai',
        'topik',
        'status',
        'catatan_dosen',
    ];

    /**
     * Tipe data (casting) untuk kolom tanggal.
     */
    protected $casts = [
        'tanggal_pertemuan' => 'date',
    ];

    /**
     * Relasi: Satu Jadwal dimiliki oleh satu Mahasiswa (User).
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    /**
     * Relasi: Satu Jadwal dimiliki oleh satu Dosen (User).
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}