<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class VerificationController extends Controller
{
    /**
     * Tampilkan halaman verifikasi awal (Ganti Password & Verifikasi Data).
     */
    public function show()
    {
        $user = Auth::user();

        // Jika user sudah ganti password (dan email verified), langsung ke dashboard
        if ($user->is_password_changed && $user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        return view('auth.first-login', compact('user'));
    }

    /**
     * Proses simpan password baru dan verifikasi akun.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            // Tambahkan validasi lain jika ingin user melengkapi data (misal No HP)
        ]);

        // Update data user
        $user->forceFill([
            'password' => Hash::make($request->password),
            'is_password_changed' => true, // Tandai sudah ganti password
            'email_verified_at' => now(), // Tandai email sudah verified (otomatis saat login pertama)
        ])->save();

        return redirect()->route('dashboard')->with('success', 'Akun berhasil diaktifkan! Selamat datang.');
    }
}