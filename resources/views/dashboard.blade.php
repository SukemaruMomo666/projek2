@extends('layouts.admin')

@section('title', 'Dashboard Mahasiswa')

@push('styles')
<style>
    .welcome-card { background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); color: white; border: none; border-radius: 15px; position: relative; overflow: hidden; }
    .welcome-card::after { content: ''; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; }
    
    .stat-card { border: none; border-radius: 15px; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
    
    .card-header-custom { background-color: white; border-bottom: 1px solid #f0f0f0; font-weight: 600; padding: 1rem 1.5rem; border-radius: 15px 15px 0 0 !important; }
    
    /* Revisi Alert Box */
    .revisi-alert { border-left: 5px solid #dc3545; background-color: #fff5f5; color: #842029; }
    
    /* STEP PROGRESS BAR (Khusus Skripsi) */
    .step-progress { display: flex; justify-content: space-between; position: relative; margin: 20px 0; }
    .step-progress::before { content: ''; position: absolute; top: 15px; left: 0; width: 100%; height: 3px; background: #e9ecef; z-index: 0; }
    .step-item { position: relative; z-index: 1; text-align: center; width: 100%; }
    .step-circle { width: 35px; height: 35px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #6c757d; font-weight: bold; border: 3px solid #fff; box-shadow: 0 0 0 2px #e9ecef; transition: all 0.3s; }
    .step-item.active .step-circle { background: #0d6efd; color: white; box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2); }
    .step-item.completed .step-circle { background: #198754; color: white; box-shadow: 0 0 0 2px #198754; }
    .step-label { font-size: 0.8rem; color: #6c757d; font-weight: 600; }
    .step-item.active .step-label { color: #0d6efd; }
    .step-item.completed .step-label { color: #198754; }

    .avatar-circle { width: 60px; height: 60px; background-color: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #495057; }
</style>
@endpush

@section('content')

{{-- LOGIKA CEK LEVEL & PROGRESS SKRIPSI --}}
@php
    $user = Auth::user();
    $prodi = strtolower($user->prodi ?? '');
    $semester = $user->semester ?? 0;
    
    $isSkripsi = false; 
    if (str_contains($prodi, 'sistem informasi') && $semester >= 5) $isSkripsi = true;
    elseif (str_contains($prodi, 'rekayasa perangkat lunak') && $semester >= 7) $isSkripsi = true;

    // --- PERBAIKAN LOGIKA PROGRESS BAR ---
    // Kita cek apakah mahasiswa SUDAH PERNAH mencatat bab tertentu
    // Logic: Jika ada logbook yang mengandung kata "Bab 1", maka step Bab 1 dianggap selesai/aktif
    
    $tahapanSkripsi = ['Judul', 'Bab 1', 'Bab 2', 'Bab 3', 'Bab 4', 'Bab 5', 'Sidang'];
    $currentStepIndex = 0; // Default di awal

    // Cek riwayat dari logbook yang ada
    foreach ($tahapanSkripsi as $index => $tahap) {
        // Cek apakah ada logbook yang materinya mengandung nama tahapan (misal "Bab 1")
        $sudahAda = $logbooks->filter(function ($item) use ($tahap) {
            return str_contains($item->materi, $tahap);
        })->isNotEmpty();

        if ($sudahAda) {
            $currentStepIndex = $index + 1; // Maju ke step ini
        }
    }
@endphp

<div class="container-fluid px-4 pb-5">
    
    {{-- 1. HEADER WELCOME --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card welcome-card shadow-sm mb-4">
                <div class="card-body p-4 d-flex align-items-center justify-content-between position-relative" style="z-index: 1;">
                    <div>
                        <h2 class="fw-bold mb-1">Halo, {{ explode(' ', $user->name)[0] }}! 👋</h2>
                        <p class="mb-0 op-8">
                            {{ $isSkripsi ? 'Pantau terus progres skripsimu di sini.' : 'Jangan lupa penuhi target perwalianmu.' }}
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

    {{-- 2. ALERT REVISI --}}
    @if($revisiTerakhir)
    <div class="alert revisi-alert shadow-sm d-flex align-items-center mb-4" role="alert">
        <div class="me-3 display-4 text-danger"><i class="fas fa-exclamation-circle"></i></div>
        <div>
            <h5 class="alert-heading fw-bold mb-1">Perhatian: Ada Revisi!</h5>
            <p class="mb-0">
                Catatan Dosen pada <strong>{{ $revisiTerakhir->tanggal_bimbingan->format('d M') }}</strong>: 
                <span class="fst-italic">"{{ Str::limit($revisiTerakhir->catatan_dosen, 80) }}"</span>
            </p>
            <a href="{{ route('bimbingan.index') }}" class="btn btn-sm btn-danger mt-2">Perbaiki Sekarang</a>
        </div>
    </div>
    @endif

    {{-- 3. PROGRESS BAR SKRIPSI (KHUSUS SENIOR) --}}
    @if($isSkripsi)
    <div class="card shadow-sm border-0 mb-4 rounded-3">
        <div class="card-header card-header-custom text-dark">
            <i class="fas fa-route me-2 text-primary"></i> Timeline Skripsi Saya
        </div>
        <div class="card-body">
            <div class="step-progress">
                @foreach($tahapanSkripsi as $index => $label)
                    @php 
                        $statusClass = '';
                        $icon = $index + 1;
                        
                        // Logic: Jika index lebih kecil dari currentStep, berarti SUDAH LEWAT (Completed)
                        if ($index < $currentStepIndex) {
                            $statusClass = 'completed'; 
                            $icon = '<i class="fas fa-check"></i>';
                        } 
                        // Jika index sama dengan currentStep, berarti SEDANG BERLANGSUNG (Active)
                        elseif ($index == $currentStepIndex) { // Ubah logika agar step saat ini menyala
                            $statusClass = 'active'; 
                        }
                        // Jika logbook Bab 1 ada tapi statusnya 'Disetujui', maka dia completed, current step maju ke bab 2
                    @endphp
                    <div class="step-item {{ $statusClass }}">
                        <div class="step-circle">{!! $icon !!}</div>
                        <div class="step-label">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- 4. KARTU UTAMA --}}
    <div class="row g-4 mb-4">
        
        {{-- Jadwal Bimbingan --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-header card-header-custom text-primary">
                    <i class="fas fa-calendar-alt me-2"></i> Jadwal Mendatang
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
                        <div class="text-center">
                            @if($jadwalMendatang->status == 'Menunggu') <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                            @elseif($jadwalMendatang->status == 'Disetujui') <span class="badge bg-success">Disetujui (ACC)</span> @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <h6 class="text-muted">Tidak Ada Jadwal</h6>
                            <a href="{{ route('jadwal.index') }}" class="btn btn-sm btn-outline-primary rounded-pill mt-2">Ajukan Jadwal</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Statistik Bimbingan --}}
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-header card-header-custom text-success">
                    <i class="fas fa-chart-pie me-2"></i> Statistik Bimbingan
                </div>
                <div class="card-body">
                    <div class="row text-center h-100 align-items-center">
                        <div class="col-6 border-end">
                            <h2 class="fw-bold text-primary mb-0">{{ $totalBimbingan }}</h2>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem">Total Logbook</small>
                        </div>
                        <div class="col-6">
                            <h2 class="fw-bold text-success mb-0">{{ $logbooks->where('status', 'Disetujui')->count() }}</h2>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem">Sudah ACC</small>
                        </div>
                        @if($isSkripsi)
                        <div class="col-12 mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between small px-2">
                                <span>Bab 1: <strong>{{ $logbooks->filter(fn($l)=>str_contains($l->materi, 'Bab 1'))->count() }}x</strong></span>
                                <span>Bab 2: <strong>{{ $logbooks->filter(fn($l)=>str_contains($l->materi, 'Bab 2'))->count() }}x</strong></span>
                                <span>Bab 3: <strong>{{ $logbooks->filter(fn($l)=>str_contains($l->materi, 'Bab 3'))->count() }}x</strong></span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Dosen --}}
        <div class="col-md-12 col-xl-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-header card-header-custom text-info">
                    <i class="fas fa-chalkboard-teacher me-2"></i> Dosen Pembimbing
                </div>
                <div class="card-body text-center p-3">
                    @if($dosen)
                        <div class="avatar-circle mx-auto mb-2 bg-light text-primary border border-primary">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($dosen->name) }}&background=random" class="rounded-circle w-100 h-100">
                        </div>
                        <h6 class="fw-bold mb-0">{{ $dosen->name }}</h6>
                        <small class="text-muted d-block mb-3">NIDN: {{ $dosen->nidn ?? '-' }}</small>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="https://wa.me/" class="btn btn-outline-success btn-sm rounded-pill"><i class="fab fa-whatsapp"></i> Chat</a>
                            <a href="mailto:{{ $dosen->email }}" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fas fa-envelope"></i> Email</a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                            <p class="small">Belum ditentukan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 5. TABEL AKTIVITAS TERAKHIR --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-history me-2 text-muted"></i>Logbook Terakhir</h6>
            <a href="{{ route('bimbingan.index') }}" class="btn btn-sm btn-link text-decoration-none">Lihat Semua &rarr;</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Materi / Topik</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logbooksTerkini as $log)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">{{ \Carbon\Carbon::parse($log->tanggal_bimbingan)->format('d M Y') }}</td>
                            <td>
                                <div class="text-dark fw-bold">{{ Str::limit($log->materi, 50) }}</div>
                                <small class="text-muted">{{ Str::limit($log->catatan_mahasiswa, 60) }}</small>
                            </td>
                            <td class="text-center">
                                @if ($log->status == 'Disetujui') <span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill">ACC</span>
                                @elseif ($log->status == 'Revisi') <span class="badge bg-danger bg-opacity-10 text-danger px-3 rounded-pill">REVISI</span>
                                @else <span class="badge bg-warning bg-opacity-10 text-warning px-3 rounded-pill">PENDING</span> @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada aktivitas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection