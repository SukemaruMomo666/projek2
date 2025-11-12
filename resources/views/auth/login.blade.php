<x-guest-layout>
    
    <!-- Header Login -->
    <div class="text-center mb-5">
        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-flex mb-3">
            <i class="fas fa-graduation-cap fa-3x text-primary"></i>
        </div>
        <h2 class="fw-bold text-dark">Bimbingan JTIK</h2>
        <p class="text-muted">Silakan masuk untuk melanjutkan</p>
    </div>
 
    <!-- Alert Error (Jika Ada) -->
    <x-auth-session-status class="mb-4 alert alert-success" :status="session('status')" />
    @if ($errors->any())
        <div class="alert alert-danger mb-4 border-0 shadow-sm">
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
 
    <form method="POST" action="{{ route('login') }}">
        @csrf
 
        <!-- Input NIM -->
        <div class="form-floating mb-3">
            <input type="text" class="form-control @error('nim') is-invalid @enderror" id="nim" name="nim" value="{{ old('nim') }}" placeholder="Nomor Induk Mahasiswa" required autofocus>
            <label for="nim">NIM (Nomor Induk Mahasiswa)</label>
        </div>
 
        <!-- Input Password -->
        <div class="form-floating mb-3">
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Kata Sandi" required autocomplete="current-password">
            <label for="password">Kata Sandi</label>
        </div>
 
        <!-- Remember Me & Forgot Password -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label small text-muted">Ingat Saya</label>
            </div>
            @if (Route::has('password.request'))
                <a class="small text-decoration-none fw-bold" href="{{ route('password.request') }}">
                    Lupa Sandi?
                </a>
            @endif
        </div>
 
        <!-- Tombol Login Utama -->
        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                Masuk
            </button>
        </div>
 
        <!-- Divider -->
        <div class="position-relative mb-4">
            <hr class="text-muted opacity-25">
            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 small text-muted">ATAU</span>
        </div>
 
        <!-- Tombol Login Microsoft -->
        <div class="d-grid">
            <a href="{{ route('login.microsoft.redirect') }}" class="btn btn-outline-dark btn-lg fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm hover-shadow transition">
                <img src="https://learn.microsoft.com/en-us/azure/active-directory/develop/media/howto-add-branding-in-azure-ad-apps/ms-symbollockup_mssymbol_19.png" alt="Microsoft" width="20" height="20">
                <span>Masuk dengan Microsoft</span>
            </a>
        </div>
 
    </form>
 
    <!-- Footer -->
    <div class="text-center mt-5 small text-muted">
        &copy; {{ date('Y') }} Bimbingan JTIK Polsub. All rights reserved.
    </div>
 
</x-guest-layout>