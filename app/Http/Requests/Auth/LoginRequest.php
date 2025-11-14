<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan membuat request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Validasi input 'login_id' (nama input baru kita)
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Mencoba mengotentikasi kredensial request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // --- INI LOGIKA BARU YANG PENTING ---
        
        // 1. Ambil input dari form (yang namanya 'login_id')
        $loginInput = $this->input('login_id');

        // 2. Tentukan apakah inputnya Email atau NIM
        // filter_var cek apakah formatnya email. Jika ya, $field = 'email'.
        // Jika tidak, kita anggap itu 'nim'.
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';

        // 3. Coba login menggunakan field yang sudah ditentukan
        if (! Auth::attempt([
                $field => $loginInput, 
                'password' => $this->input('password')
            ], $this->boolean('remember'))) {
            
            // Jika gagal...
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                // 4. Ganti pesan error agar merujuk ke input 'login_id'
                'login_id' => trans('auth.failed'),
            ]);
        }
        
        // --- AKHIR LOGIKA BARU ---

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Memastikan request login tidak di rate limit (terlalu banyak percobaan).
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            // 5. Ganti pesan error agar merujuk ke input 'login_id'
            'login_id' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Dapatkan kunci throttle untuk request.
     */
    public function throttleKey(): string
    {
        // 6. Ganti 'nim' menjadi 'login_id'
        return Str::transliterate(Str::lower($this->input('login_id')).'|'.$this->ip());
    }
}