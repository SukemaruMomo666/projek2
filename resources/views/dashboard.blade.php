@extends('layouts.admin')

@section('title', 'Dashboard Mahasiswa')

@push('styles')
<style>
    .welcome-card { background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); color: white; border: none; border-radius: 15px; position: relative; overflow: hidden; }
    .welcome-card::after { content: ''; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; }
    
    .stat-card { border: none; border-radius: 15px; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
    .stat-icon { opacity: 0.2; font-size: 3.5rem; position: absolute; right: 20px; top: 15px; }
    
    .card-header-custom { background-color: white; border-bottom: 1px solid #f0f0f0; font-weight: 600; padding: 1rem 1.5rem; border-radius: 15px 15px 0 0 !important; }
    
    /* Revisi Alert Box */
    .revisi-alert { border-left: 5px solid #dc3545; background-color: #fff5f5; color: #842029; }
    
    /* Schedule Box */
    .schedule-box { background: #f8f9fa; border-radius: 10px; border: 1px solid #e9ecef; transition: all 0.3s; }
    .schedule-box:hover { border-color: #0d6efd; background: #fff; box-shadow: 0 5px 15px rgba(13, 110, 253, 0.1); }
    
    .avatar-circle { width: 60px; height: 60px; background-color: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #495057; }
</style>
@endpush

@section('content')

{{-- LOGIKA CEK LEVEL --}}
@php
    $user = Auth::user();
    $prodi = strtolower($user->prodi ?? '');
    $semester = $user->semester ?? 0;
    
    $isSkripsi = false; 
    if (str_contains($prodi, 'sistem informasi') && $semester >= 5) $isSkripsi = true;
    elseif (str_contains($prodi, 'rekayasa perangkat lunak') && $semester >= 7) $isSkripsi = true;
@endphp

<div class="container-fluid px-4 pb-5">
    
    {{-- 1. HEADER WELCOME --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card welcome-card shadow-sm mb-4">
                <div class="card-body p-4 d-flex align-items-center justify-content-between position-relative" style="z-index: 1;">
                    <div>
                        <h2 class="fw-bold mb-1">Selamat Datang, {{ explode(' ', $user->name)[0] }}! 👋</h2>
                        <p class="mb-0 op-8">
                            {{ $isSkripsi ? 'Fokus tuntaskan Skripsi semester ini!' : 'Jangan lupa penuhi target perwalianmu.' }}
                        </p>
                    </div>
                    <div class="d-none d-md-block text-end">
                        <h5 class="mb-0 fw-bold">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</h5>
                        <small class="badge bg-white text-primary fw-bold mt-2">Semester {{ $semester }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. ALERT REVISI (HANYA MUNCUL JIKA ADA REVISI) --}}
    @if($revisiTerakhir)
    <div class="alert revisi-alert shadow-sm d-flex align-items-center mb-4" role="alert">
        <div class="me-3 display-4 text-danger">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div>
            <h5 class="alert-heading fw-bold mb-1">Perhatian: Ada Revisi yang Harus Diperbaiki!</h5>
            <p class="mb-0">
                Pada bimbingan tanggal <strong>{{ $revisiTerakhir->tanggal_bimbingan->format('d M Y') }}</strong> ({{ Str::before($revisiTerakhir->materi, ':') }}):
                <br>
                <span class="fst-italic text-dark">"{{ $revisiTerakhir->catatan_dosen }}"</span>
            </p>
            <a href="{{ route('bimbingan.index') }}" class="btn btn-sm btn-danger mt-2">Lihat Detail & Perbaiki</a>
        </div>
    </div>
    @endif

    {{-- 3. JADWAL MENDATANG & TARGET --}}
    <div class="row g-4 mb-4">
        
        {{-- Jadwal Bimbingan Selanjutnya --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-header card-header-custom text-primary">
                    <i class="fas fa-calendar-alt me-2"></i> Jadwal Bimbingan Selanjutnya
                </div>
                <div class="card-body">
                    @if($jadwalMendatang)
                        <div class="schedule-box p-3 text-center mb-3">
                            <h4 class="fw-bold text-dark mb-0">{{ \Carbon\Carbon::parse($jadwalMendatang->tanggal_pertemuan)->format('d F') }}</h4>
                            <span class="badge bg-primary rounded-pill mb-2">
                                {{ \Carbon\Carbon::parse($jadwalMendatang->waktu_mulai)->format('H:i') }} WIB
                            </span>
                            <p class="text-muted small mb-0">{{ $jadwalMendatang->topik }}</p>
                        </div>
                        <div class="d-flex align-items-center justify-content-center bg-light p-2 rounded">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($jadwalMendatang->dosen->name) }}&size=30" class="rounded-circle me-2">
                            <small class="fw-bold text-dark">Dosen: {{ explode(' ', $jadwalMendatang->dosen->name)[0] }}</small>
                        </div>
                        <div class="mt-3 text-center">
                            @if($jadwalMendatang->status == 'Menunggu')
                                <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                            @elseif($jadwalMendatang->status == 'Disetujui')
                                <span class="badge bg-success">Disetujui (ACC)</span>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <img src="https://img.icons8.com/ios/100/cccccc/calendar.png" width="50" class="mb-3 opacity-50">
                            <h6 class="text-muted">Tidak Ada Jadwal</h6>
                            <p class="small text-muted mb-3">Segera ajukan pertemuan dengan dosen.</p>
                            <a href="{{ route('jadwal.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-plus me-1"></i> Ajukan Jadwal
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Statistik Target / Progress --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-header card-header-custom text-success">
                    <i class="fas fa-chart-line me-2"></i> 
                    {{ $isSkripsi ? 'Progres Skripsi' : 'Target Perwalian Semester' }}
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    
                    @if(!$isSkripsi)
                        {{-- TAMPILAN JUNIOR: COUNTER SISA --}}
                        <div class="mb-2">
                            <span class="display-4 fw-bold {{ $sisaPerwalian > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $sisaPerwalian }}
                            </span>
                            <span class="text-muted fs-5">kali lagi</span>
                        </div>
                        <p class="text-muted mb-3">
                            Anda sudah melakukan <strong>{{ $jumlahPerwalian }}</strong> dari <strong>3</strong> perwalian wajib semester ini.
                        </p>
                        <div class="progress" style="height: 10px;">
                            @php $persen = ($jumlahPerwalian / 3) * 100; @endphp
                            <div class="progress-bar bg-{{ $persen >= 100 ? 'success' : 'warning' }}" role="progressbar" style="width: {{ $persen }}%"></div>
                        </div>
                    @else
                        {{-- TAMPILAN SENIOR: PROGRESS BAB --}}
                        <h5 class="fw-bold mb-3">{{ $logbooksTerkini->first()->materi ?? 'Belum Mulai' }}</h5>
                        <div class="progress mb-3" style="height: 15px;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 25%">25%</div>
                        </div>
                        <small class="text-muted">Target Sidang: <strong>{{ date('Y') + 1 }}</strong></small>
                    @endif

                </div>
            </div>
        </div>

        {{-- Info Dosen Pembimbing --}}
        <div class="col-md-12 col-xl-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-header card-header-custom text-info">
                    <i class="fas fa-chalkboard-teacher me-2"></i> Dosen Pembimbing
                </div>
                <div class="card-body text-center p-4">
                    <div class="avatar-circle mx-auto mb-3 bg-light text-primary border border-primary">
                        @if($dosen)
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($dosen->name) }}&background=random" class="rounded-circle w-100 h-100">
                        @else
                            <i class="fas fa-user-slash"></i>
                        @endif
                    </div>
                    
                    @if ($dosen)
                        <h5 class="fw-bold mb-1">{{ $dosen->name }}</h5>
                        <p class="text-muted mb-3 small">NIDN: {{ $dosen->nidn ?? '-' }}</p>
                        <div class="d-grid gap-2">
                            <a href="https://wa.me/" target="_blank" class="btn btn-outline-success btn-sm rounded-pill">
                                <i class="fab fa-whatsapp me-1"></i> WhatsApp
                            </a>
                            <a href="mailto:{{ $dosen->email }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                                <i class="fas fa-envelope me-1"></i> Email
                            </a>
                        </div>
                    @else
                        <h5 class="fw-bold mb-1 text-danger">Belum Ditentukan</h5>
                        <p class="text-muted small mb-3">Hubungi admin prodi untuk plotting.</p>
                        <button class="btn btn-secondary btn-sm disabled w-100">Hubungi Dosen</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 4. TABEL AKTIVITAS TERAKHIR --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-history me-2 text-muted"></i>Aktivitas Bimbingan Terakhir</h6>
            <a href="{{ route('bimbingan.index') }}" class="btn btn-sm btn-link text-decoration-none">Lihat Semua &rarr;</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Topik</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logbooksTerkini as $log)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">{{ \Carbon\Carbon::parse($log->tanggal_bimbingan)->format('d M Y') }}</td>
                            <td>
                                <div class="text-dark fw-bold">{{ Str::limit($log->materi, 50) }}</div>
                                <small class="text-muted">{{ Str::limit($log->catatan_mahasiswa, 40) }}</small>
                            </td>
                            <td class="text-center">
                                @if ($log->status == 'Disetujui')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill">ACC</span>
                                @elseif ($log->status == 'Revisi')
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 rounded-pill">REVISI</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 rounded-pill">PENDING</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Belum ada aktivitas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection