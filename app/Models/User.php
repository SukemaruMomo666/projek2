<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Opsional, bawaan Laravel biasanya ada

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi (Mass Assignment)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nim',
        'kelas',
        'nidn',
        'prodi',
        'semester',
        'dosen_pembimbing_id',
        'email_verified_at',
        'is_password_changed', // WAJIB ADA
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi array/json
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data otomatis
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_password_changed' => 'boolean', // WAJIB ADA
    ];

    // ==========================================
    // RELASI (RELATIONSHIPS) - WAJIB ADA
    // ==========================================

    /**
     * Relasi: User (Mahasiswa) punya banyak Logbook Bimbingan.
     * -> Digunakan untuk menghitung "Total Bimbingan" di Dashboard Dosen.
     */
    public function bimbingans() {
        return $this->hasMany(Bimbingan::class, 'mahasiswa_id');
    }

    /**
     * Relasi: User (Mahasiswa) punya banyak Dokumen Skripsi.
     */
    public function dokumens() {
        return $this->hasMany(Dokumen::class, 'mahasiswa_id');
    }

    /**
     * Relasi: User (Mahasiswa) punya banyak Jadwal.
     */
    public function jadwals() {
        return $this->hasMany(Jadwal::class, 'mahasiswa_id');
    }

    /**
     * Relasi: User (Mahasiswa) "Milik" 1 Dosen Pembimbing.
     */
    public function dosenPembimbing() {
        return $this->belongsTo(User::class, 'dosen_pembimbing_id');
    }

    /**
     * Relasi: User (Dosen) "Memiliki" Banyak Mahasiswa Bimbingan.
     */
    public function mahasiswaBimbingan() {
        return $this->hasMany(User::class, 'dosen_pembimbing_id');
    }
}