<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Penting untuk password
use Illuminate\Support\Str; // Penting untuk password
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Mengarahkan user ke halaman login Microsoft.
     */
    public function microsoftRedirect()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    /**
     * Menerima data user dari Microsoft setelah login berhasil.
     */
    public function microsoftCallback()
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();

            // Cari user di database kita berdasarkan email dari Microsoft
            // atau buat user baru jika tidak ada
            $user = User::updateOrCreate(
                [
                    'email' => $microsoftUser->getEmail(), // Kunci pencarian
                ],
                [
                    'name' => $microsoftUser->getName(),
                    'password' => Hash::make(Str::random(24)) // Buat password acak yang aman
                    // Anda juga bisa menambahkan 'nim' jika datanya ada dari microsoft
                    // 'nim' => $microsoftUser->getNickname() // (Contoh, perlu disesuaikan)
                ]
            );

            // Loginkan user yang baru dibuat atau yang sudah ada
            Auth::login($user);

            // Arahkan ke dashboard
            return redirect('/dashboard');

        } catch (\Exception $e) {
            // Jika ada error (misal user menekan 'cancel'), kembalikan ke login
            return redirect('/login')->with('error', 'Login dengan Microsoft gagal.');
        }
    }
}