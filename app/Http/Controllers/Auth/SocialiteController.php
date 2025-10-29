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
/**
 * Menerima data user dari Microsoft setelah login berhasil.
 */
/**
 * Menerima data user dari Microsoft setelah login berhasil.
 */
/**
 * Menerima data user dari Microsoft setelah login berhasil.
 */
public function microsoftCallback()
{
    try {
        // 1. HANCURKAN SESI LAMA (INI KUNCINYA)
        // Ini untuk membunuh "sesi hantu" yang mungkin nyangkut
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        // 2. Lanjutkan proses login seperti biasa
        $microsoftUser = Socialite::driver('microsoft')->user();
        $user = User::where('email', $microsoftUser->getEmail())->first();

        if ($user) {
            // 3. Cek lagi untuk keamanan, meskipun Tinker sudah bilang OK
            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified(); // Paksa verifikasi jika (entah bagaimana) belum
            }

            // 4. Loginkan user yang sudah fresh
            Auth::login($user);

            // 5. Arahkan ke dashboard
            return redirect()->intended('/dashboard');
        }

        // 6. Jika user tidak ditemukan
        return redirect('/login')->with('error', 'Email Microsoft Anda tidak terdaftar dalam sistem kami.');

    } catch (\Exception $e) {
        // 7. Tangani error lain
        return redirect('/login')->with('error', 'Login dengan Microsoft gagal. Silakan coba lagi.');
    }
}
}