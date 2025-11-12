<?php 
 
namespace App\Models; 
 
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable; 
 
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
        'role',                 // Baru 
        'nim',                  // Baru 
        'nidn',                 // Baru 
        'prodi',                // Baru 
        'semester',             // Baru 
        'dosen_pembimbing_id',  // Baru 
        'email_verified_at'     // Agar bisa diisi manual via 
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
            'email_verified_at' => 'datetime', 
            'password' => 'hashed', 
        ]; 
    } 
 
    /** 
     * Relasi: Mahasiswa memiliki satu Dosen Pembimbing 
     */ 
    public function dosenPembimbing() 
    { 
        return $this->belongsTo(User::class, 'dosen_pembimbing_id'); 
    } 
 
    /** 
     * Relasi: Dosen memiliki banyak Mahasiswa Bimbingan 
     */ 
    public function mahasiswaBimbingan() 
    { 
        return $this->hasMany(User::class, 'dosen_pembimbing_id'); 
    } 
     
    // Helper untuk cek role (opsional, biar gampang di view nanti) 
    public function isMahasiswa() { return $this->role === 
'mahasiswa'; } 
    public function isDosen() { return $this->role === 'dosen'; } 
    public function isAdmin() { return $this->role === 'admin'; } 
}