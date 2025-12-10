<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_password_changed' => 'boolean', // WAJIB ADA
    ];

    // Relasi
    public function dosenPembimbing() {
        return $this->belongsTo(User::class, 'dosen_pembimbing_id');
    }

    public function mahasiswaBimbingan() {
        return $this->hasMany(User::class, 'dosen_pembimbing_id');
    }
}