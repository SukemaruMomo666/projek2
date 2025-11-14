<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Sistem Bimbingan JTIK" />
        <meta name="author" content="" />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <!-- Pastikan file styles.css ada di folder public/css -->
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

        <!-- CSS Spesifik Halaman -->
        @stack('styles')
    </head>
    <body class="sb-nav-fixed">
        
        <!-- NAVBAR ATAS -->
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="{{ route('dashboard') }}">Bimbingan JTIK</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            
            <!-- Spacer -->
            <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0"></div>
            
            <!-- User Menu -->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user fa-fw me-1"></i> {{ Auth::user()->name ?? 'Guest' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil Saya</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <!-- Logout Form -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                            this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </a>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- LAYOUT SIDEBAR & KONTEN -->
        <div id="layoutSidenav">
            
            <!-- SIDEBAR KIRI (DENGAN LOGIKA ROLE) -->
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            
                            <!-- Cek Role User -->
                            @if(Auth::user()->role == 'mahasiswa')
                                
                                <!-- === MENU MAHASISWA === -->
                                <div class="sb-sidenav-menu-heading">Utama</div>
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                    Dashboard
                                </a>

                                <div class="sb-sidenav-menu-heading">Akademik</div>
                                <a class="nav-link {{ request()->routeIs('bimbingan.index') ? 'active' : '' }}" href="{{ route('bimbingan.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                    Logbook Bimbingan
                                </a>
                                <a class="nav-link {{ request()->routeIs('bimbingan.upload') ? 'active' : '' }}" href="{{ route('bimbingan.upload') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-file-upload"></i></div>
                                    Dokumen Skripsi
                                </a>
                                <a class="nav-link {{ request()->routeIs('jadwal.index') ? 'active' : '' }}" href="{{ route('jadwal.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                    Jadwal Dosen
                                </a>

                            @elseif(Auth::user()->role == 'dosen')
                                
                                <!-- === MENU DOSEN === -->
                                <div class="sb-sidenav-menu-heading">Utama</div>
                                <a class="nav-link {{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}" href="{{ route('dosen.dashboard') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                    Dashboard Dosen
                                </a>
                                
                                <div class="sb-sidenav-menu-heading">Bimbingan</div>
                                <a class="nav-link" href="#">
                                    <div class="sb-nav-link-icon"><i class="fas fa-user-check"></i></div>
                                    Validasi Bimbingan
                                </a>
                                <a class="nav-link" href="#">
                                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                    Data Mahasiswa
                                </a>

                            @endif

                            <!-- === MENU AKUN (BERSAMA) === -->
                            <div class="sb-sidenav-menu-heading">Akun</div>
                            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div>
                                Profil Saya
                            </a>

                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Masuk sebagai:</div>
                        {{ Auth::user()->name ?? 'Pengguna' }}
                    </div>
                </nav>
            </div>

            <!-- KONTEN UTAMA -->
            <div id="layoutSidenav_content">
                <main>
                    <!-- Ini adalah wadah isi halaman -->
                    @yield('content')
                </main>

                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright © {{ config('app.name', 'Bimbingan JTIK') }} {{ date('Y') }}</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                ·
                                <a href="#">Terms & Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- 
        ================================================================
        INI BAGIAN PENTING YANG MEMBUAT MODAL BEKERJA
        ================================================================
        -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        
        <!-- Stack Scripts untuk Halaman Tertentu (cth: DataTables) -->
        @stack('scripts')
        
    </body>
</html>
```

### Langkah Terakhir (Wajib)

Setelah kamu menyimpan file `admin.blade.php` yang baru ini, jalankan perintah ini di **Terminal Laragon** untuk membersihkan *cache* view:

```bash
php artisan view:clear