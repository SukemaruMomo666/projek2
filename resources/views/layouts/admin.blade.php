<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Sistem Bimbingan JTIK" />
        <meta name="author" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

        @stack('styles')
    </head>
    <body class="sb-nav-fixed">
        
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="{{ route('dashboard') }}">Bimbingan JTIK</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0"></div>
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user fa-fw me-1"></i> {{ Auth::user()->name ?? 'Guest' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil Saya</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </a>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <div id="layoutSidenav">
            
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            
                            {{-- LOGIKA PHP: CEK SEMESTER & PRODI UNTUK AKSES SKRIPSI --}}
                            @php
                                $user = Auth::user();
                                $prodi = strtolower($user->prodi ?? '');
                                $semester = $user->semester ?? 0;
                                
                                $bisaSkripsi = false; 
                                if (str_contains($prodi, 'sistem informasi') && $semester >= 5) {
                                    $bisaSkripsi = true;
                                } elseif (str_contains($prodi, 'rekayasa perangkat lunak') && $semester >= 7) {
                                    $bisaSkripsi = true;
                                }
                            @endphp

                            {{-- === 1. MENU MAHASISWA === --}}
                            @if(Auth::user()->role == 'mahasiswa')
                                
                                <div class="sb-sidenav-menu-heading">Utama</div>
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                    Dashboard
                                </a>

                                <div class="sb-sidenav-menu-heading">Akademik & Bimbingan</div>
                                
                                <a class="nav-link {{ request()->routeIs('bimbingan.index') ? 'active' : '' }}" href="{{ route('bimbingan.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-book-reader"></i></div>
                                    Logbook Bimbingan
                                </a>
                                
                                <a class="nav-link {{ request()->routeIs('jadwal.index') ? 'active' : '' }}" href="{{ route('jadwal.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                    Bimbingan & Dosen
                                </a>

                                @if($bisaSkripsi)
                                    <a class="nav-link {{ request()->routeIs('bimbingan.upload') ? 'active' : '' }}" href="{{ route('bimbingan.upload') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-file-upload"></i></div>
                                        Dokumen Skripsi
                                    </a>
                                @endif

                            {{-- === 2. MENU DOSEN (STRUKTUR BARU) === --}}
                            @elseif(Auth::user()->role == 'dosen')
                                
                                <div class="sb-sidenav-menu-heading">Utama</div>
                                <a class="nav-link {{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}" href="{{ route('dosen.dashboard') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                    Dashboard Dosen
                                </a>
                                
                                {{-- A. BIMBINGAN UMUM / AKADEMIK --}}
                                <div class="sb-sidenav-menu-heading">Bimbingan Akademik</div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseUmum" aria-expanded="false" aria-controls="collapseUmum">
                                    <div class="sb-nav-link-icon"><i class="fas fa-user-friends"></i></div>
                                    Mahasiswa Wali
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseUmum" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="{{ route('dosen.mahasiswa.index') }}">Data Mahasiswa</a>
                                        </nav>
                                </div>

                                {{-- B. PROYEK AKHIR / SKRIPSI --}}
                                <div class="sb-sidenav-menu-heading">Tugas Akhir / Skripsi</div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSkripsi" aria-expanded="false" aria-controls="collapseSkripsi">
                                    <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                                    Bimbingan Skripsi
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse {{ (request()->routeIs('dosen.validasi.*') || request()->routeIs('dosen.arsip.*')) ? 'show' : '' }}" id="collapseSkripsi" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link {{ request()->routeIs('dosen.validasi.logbook.index') ? 'active' : '' }}" href="{{ route('dosen.validasi.logbook.index') }}">
                                            Validasi Logbook
                                        </a>
                                        <a class="nav-link {{ request()->routeIs('dosen.validasi.dokumen.index') ? 'active' : '' }}" href="{{ route('dosen.validasi.dokumen.index') }}">
                                            Review Dokumen (Bab)
                                        </a>
                                        <a class="nav-link {{ request()->routeIs('dosen.arsip.index') ? 'active' : '' }}" href="{{ route('dosen.arsip.index') }}">
                                            Arsip & Riwayat
                                        </a>
                                    </nav>
                                </div>

                                {{-- C. JADWAL PERTEMUAN --}}
                                <div class="sb-sidenav-menu-heading">Agenda</div>
                                <a class="nav-link {{ request()->routeIs('dosen.jadwal.index') ? 'active' : '' }}" href="{{ route('dosen.jadwal.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-check"></i></div>
                                    Kelola Jadwal Temu
                                </a>

                            {{-- === 3. MENU ADMIN === --}}
                            @elseif(Auth::user()->role == 'admin')

                                <div class="sb-sidenav-menu-heading">Administrator</div>
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                    Dashboard Admin
                                </a>

                                <div class="sb-sidenav-menu-heading">Master Data</div>
                                <a class="nav-link {{ request()->routeIs('admin.dosen.*') ? 'active' : '' }}" href="{{ route('admin.dosen.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
                                    Kelola Dosen
                                </a>
                                <a class="nav-link {{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}" href="{{ route('admin.mahasiswa.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-user-graduate"></i></div>
                                    Kelola Mahasiswa
                                </a>

                                <div class="sb-sidenav-menu-heading">Sistem</div>
                                <a class="nav-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                                    Pengaturan Sistem
                                </a>

                            @endif

                            {{-- === MENU AKUN (SEMUA ROLE) === --}}
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
                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem">({{ Auth::user()->role }})</div>
                    </div>
                </nav>
            </div>

            <div id="layoutSidenav_content">
                <main>
                    @yield('content')
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; {{ config('app.name', 'Bimbingan JTIK') }} {{ date('Y') }}</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms & Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        
        @stack('scripts')
        
    </body>
</html>