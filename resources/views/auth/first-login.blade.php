<x-guest-layout>
    <div class="text-center mb-4">
        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
        <h2 class="fw-bold text-dark">Aktivasi Akun</h2>
        <p class="text-muted">Halo, <strong>{{ Auth::user()->name }}</strong>!<br>
        Demi keamanan, silakan ubah password default Anda sebelum melanjutkan.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('first-login.update') }}">
        @csrf
        @method('PUT')

        <!-- Email (Readonly) -->
        <div class="form-floating mb-3">
            <input type="text" class="form-control bg-light" value="{{ Auth::user()->email }}" readonly>
            <label>Email Kampus</label>
        </div>

        <!-- Password Baru -->
        <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password Baru" required>
            <label for="password">Password Baru</label>
        </div>

        <!-- Konfirmasi Password -->
        <div class="form-floating mb-4">
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi Password" required>
            <label for="password_confirmation">Ulangi Password Baru</label>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg fw-bold">
                <i class="fas fa-save me-2"></i> Simpan & Lanjutkan
            </button>
            
            <!-- Tombol Logout (Penting jika user batal) -->
            <button type="button" class="btn btn-link text-muted text-decoration-none btn-sm" 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Batal & Logout
            </button>
        </div>
    </form>

    <!-- Form Logout Tersembunyi -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

</x-guest-layout>