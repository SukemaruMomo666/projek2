@extends('layouts.admin')

@section('title', 'Dashboard Mahasiswa')

@push('styles')
<style>
    .welcome-card { background: linear-gradient(45deg, #2937f0, #9f1ae2); color: white; border: none; border-radius: 15px; }
    .stat-card { border: none; border-radius: 15px; transition: transform 0.3s ease, box-shadow 0.3s ease; overflow: hidden; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .stat-icon { opacity: 0.3; font-size: 3rem; position: absolute; right: 20px; top: 20px; }
    .card-header-custom { background-color: white; border-bottom: 1px solid #f0f0f0; font-weight: 600; padding: 1.2rem; border-radius: 15px 15px 0 0 !important; }
    .avatar-circle { width: 60px; height: 60px; background-color: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #495057; }
    
    /* Step Progress (Hanya untuk Senior) */
    .step-progress { display: flex; justify-content: space-between; margin: 20px 0; position: relative; }
    .step-item { text-align: center; position: relative; z-index: 1; width: 100%; }
    .step-circle { width: 35px; height: 35px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #6c757d; font-weight: bold; transition: all 0.3s; }
    .step-item.active .step-circle { background: #198754; color: white; box-shadow: 0 0 0 5px rgba(25, 135, 84, 0.2); }
    .step-item.completed .step-circle { background: #198754; color: white; }
    .step-label { font-size: 0.8rem; color: #6c757d; font-weight: 600; }
    .progress-line { position: absolute; top: 17px; left: 0; width: 100%; height: 2px; background: #e9ecef; z-index: 0; }
    .progress-line-fill { height: 100%; background: #198754; transition: width 0.5s ease; }
</style>
@endpush

@section('content')

{{-- LOGIKA CEK LEVEL MAHASISWA --}}
@php
    $user = Auth::user();
    $prodi = $user->prodi;
    $semester = $user->semester;
    
    $isSkripsi = false; // Apakah dia semester akhir?

    if (str_contains(strtolower($prodi), 'sistem informasi') && $semester >= 5) {
        $isSkripsi = true;
    } elseif (str_contains(strtolower($prodi), 'rekayasa perangkat lunak') && $semester >= 7) {
        $isSkripsi = true;
    }
@endphp

<div class="container-fluid px-4 pb-4">
    
    {{-- 1. WELCOME CARD --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card welcome-card shadow-sm mb-4">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="fw-bold mb-1">Halo, {{ Auth::user()->name }}! 👋</h2>
                        <p class="mb-0 op-8">
                            @if($isSkripsi)
                                Semangat mengerjakan Skripsi! Jangan lupa catat progresmu.
                            @else
                                Jangan lupa catat bimbingan akademik / wali kelasmu di sini ya!
                            @endif
                        </p>
                    </div>
                    <div class="d-none d-md-block text-end">
                        <h5 class="mb-0">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</h5>
                        <small class="badge bg-light text-primary">Semester {{ $semester }} - {{ $prodi }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. STATISTIK CARDS --}}
    <div class="row g-4 mb-4">
        {{-- Card 1: Materi Terakhir --}}
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-book"></i></div>
                    <h6 class="text-uppercase mb-2 opacity-75">
                        {{ $isSkripsi ? 'Progres Skripsi' : 'Bimbingan Terakhir' }}
                    </h6>
                    <h3 class="fw-bold fs-4">{{ $logbooksTerkini->first()->materi ?? 'Belum Ada' }}</h3>
                    
                    @if($isSkripsi)
                    <div class="progress mt-3" style="height: 5px; background-color: rgba(255,255,255,0.3);">
                        <div class="progress-bar bg-white" role="progressbar" style="width: {{ $progressPercent ?? 0 }}%"></div>
                    </div>
                    <small class="d-block mt-2">Est. Progres {{ $progressPercent ?? 0 }}%</small>
                    @else
                    <small class="d-block mt-3 opacity-75">Tetap rajin konsultasi ya!</small>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 2: Total Bimbingan --}}
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-comments"></i></div>
                    <h6 class="text-uppercase mb-2 opacity-75">Total Logbook</h6>
                    <h3 class="fw-bold">{{ $totalBimbingan ?? 0 }} Kali</h3>
                    <small class="d-block mt-4">Tercatat di sistem</small>
                </div>
            </div>
        </div>

        {{-- Card 3: Status Terkini --}}
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <h6 class="text-uppercase mb-2 opacity-75">Status Validasi</h6>
                    <h3 class="fw-bold fs-4">{{ $statusTerkini ?? 'Menunggu' }}</h3>
                    <small class="d-block mt-4">Respon Dosen</small>
                </div>
            </div>
        </div>

        {{-- Card 4: Dinamis (Jadwal Sidang vs Info) --}}
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card bg-danger text-white h-100">
                <div class="card-body">
                    @if($isSkripsi)
                        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                        <h6 class="text-uppercase mb-2 opacity-75">Jadwal Sidang</h6>
                        <h3 class="fw-bold fs-4">
                            {{ ($jadwalSidang ?? null) ? \Carbon\Carbon::parse($jadwalSidang->tanggal_pertemuan)->format('d M') : '--' }}
                        </h3>
                        <small class="d-block mt-4">
                            {{ ($jadwalSidang ?? null) ? $jadwalSidang->waktu_mulai . ' WIB' : 'Belum dijadwalkan' }}
                        </small>
                    @else
                        <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                        <h6 class="text-uppercase mb-2 opacity-75">Target Skripsi</h6>
                        <h3 class="fw-bold fs-4">Sem {{ str_contains(strtolower($prodi), 'sistem') ? '5' : '7' }}</h3>
                        <small class="d-block mt-4">Persiapkan dirimu!</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- BAGIAN KIRI: CONTENT UTAMA --}}
        <div class="col-lg-8">
            
            {{-- FITUR EKSKLUSIF SENIOR: ESTIMASI TAHAPAN --}}
            @if($isSkripsi)
            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-header card-header-custom">
                    <i class="fas fa-tasks me-2 text-primary"></i> Tahapan Skripsi
                </div>
                <div class="card-body">
                    <div class="position-relative">
                        <div class="progress-line">
                            <div class="progress-line-fill" style="width: {{ $progressPercent ?? 0 }}%"></div>
                        </div>
                        <div class="step-progress">
                            @php 
                                $steps = ['Mulai', 'Judul', 'Bab 1', 'Bab 2-3', 'Bab 4-5']; 
                                $curStep = $currentStep ?? 1;
                            @endphp
                            @foreach($steps as $index => $label)
                                @php $stepNum = $index + 1; @endphp
                                <div class="step-item {{ $curStep > $stepNum ? 'completed' : ($curStep == $stepNum ? 'active' : '') }}">
                                    <div class="step-circle">
                                        @if($curStep > $stepNum) <i class="fas fa-check"></i> @else {{ $stepNum }} @endif
                                    </div>
                                    <div class="step-label">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- TABEL AKTIVITAS (MUNCUL UNTUK SEMUA) --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-history me-2 text-primary"></i> Logbook Bimbingan Terakhir</span>
                    <a href="{{ route('bimbingan.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Topik / Materi</th>
                                    <th>Status Validasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logbooksTerkini as $log)
                                <tr>
                                    <td class="ps-4">{{ \Carbon\Carbon::parse($log->tanggal_bimbingan)->format('d M Y') }}</td>
                                    <td>
                                        <span class="fw-bold">{{ $log->materi }}</span><br>
                                        <small class="text-muted">{{ Str::limit($log->catatan_mahasiswa, 40) }}</small>
                                    </td>
                                    <td>
                                        @if ($log->status == 'Disetujui')
                                            <span class="badge bg-success rounded-pill px-3">Disetujui</span>
                                        @elseif ($log->status == 'Revisi')
                                            <span class="badge bg-warning text-dark rounded-pill px-3">Revisi</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill px-3">Menunggu</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center p-4 text-muted">Belum ada riwayat bimbingan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: SIDEBAR --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-header card-header-custom">
                    <i class="fas fa-chalkboard-teacher me-2 text-primary"></i> 
                    {{ $isSkripsi ? 'Dosen Pembimbing' : 'Dosen Wali / Pembimbing' }}
                </div>
                <div class="card-body text-center p-4">
                    <div class="avatar-circle mx-auto mb-3 bg-primary text-white"><i class="fas fa-user-tie"></i></div>
                    @if ($dosen ?? null)
                        <h5 class="fw-bold mb-1">{{ $dosen->name }}</h5>
                        <p class="text-muted mb-3">NIDN: {{ $dosen->nidn ?? '-' }}</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm rounded-pill">Hubungi</button>
                        </div>
                    @else
                        <h5 class="fw-bold mb-1 text-danger">Belum Ditentukan</h5>
                        <p class="text-muted small">Silakan hubungi admin prodi.</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header card-header-custom">
                    <i class="fas fa-bolt me-2 text-primary"></i> Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('bimbingan.index') }}" class="btn btn-light text-start border hover-shadow">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded p-2 me-3"><i class="fas fa-plus"></i></div>
                                <div>
                                    <div class="fw-bold">Tambah Logbook</div>
                                    <small class="text-muted">
                                        {{ $isSkripsi ? 'Catat bimbingan skripsi' : 'Catat bimbingan akademik/lomba' }}
                                    </small>
                                </div>
                            </div>
                        </a>
                        
                        {{-- TOMBOL UPLOAD HANYA UNTUK SENIOR --}}
                        @if($isSkripsi)
                        <a href="{{ route('bimbingan.upload') }}" class="btn btn-light text-start border hover-shadow">
                            <div class="d-flex align-items-center">
                                <div class="bg-success text-white rounded p-2 me-3"><i class="fas fa-upload"></i></div>
                                <div><div class="fw-bold">Upload Berkas</div><small class="text-muted">Kirim file revisi</small></div>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection