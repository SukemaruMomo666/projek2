<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;  <-- DIHAPUS
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 'implements MustVerifyEmail' DIHAPUS dari baris di bawah
class User extends Authenticatable 
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nim', // 'nim' yang sudah kita buat
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Kita biarkan ini, tidak masalah
            'email_verified_at' => 'datetime', 
            'password' => 'hashed',
        ];
    }
}

