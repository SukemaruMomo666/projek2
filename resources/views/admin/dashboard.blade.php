@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid px-4">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-0 text-dark">Dashboard Administrator</h1>
            <p class="text-muted mb-0">Panel kontrol utama sistem bimbingan.</p>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row g-4">
        <!-- Card Total Dosen -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold">Total Dosen</div>
                            <div class="h1 mb-0 fw-bold">{{ $totalDosen }}</div>
                        </div>
                        <i class="fas fa-chalkboard-teacher fa-3x opacity-25"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between border-0 bg-primary bg-opacity-10">
                    <a class="small text-white stretched-link text-decoration-none" href="#">Kelola Dosen</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Card Total Mahasiswa -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold">Total Mahasiswa</div>
                            <div class="h1 mb-0 fw-bold">{{ $totalMahasiswa }}</div>
                        </div>
                        <i class="fas fa-user-graduate fa-3x opacity-25"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between border-0 bg-success bg-opacity-10">
                    <a class="small text-white stretched-link text-decoration-none" href="#">Kelola Mahasiswa</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Card Perlu Pembimbing -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold">Belum Ada Pembimbing</div>
                            <div class="h1 mb-0 fw-bold">{{ $mahasiswaTanpaPembimbing }}</div>
                        </div>
                        <i class="fas fa-user-slash fa-3x opacity-25"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between border-0 bg-warning bg-opacity-10">
                    <a class="small text-white stretched-link text-decoration-none" href="#">Plotting Pembimbing</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Card Total Aktivitas -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-dark text-white h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold">Total Bimbingan</div>
                            <div class="h1 mb-0 fw-bold">{{ $totalBimbingan }}</div>
                        </div>
                        <i class="fas fa-file-signature fa-3x opacity-25"></i>
                    </div>
                </div>
                 <div class="card-footer d-flex align-items-center justify-content-between border-0 bg-dark bg-opacity-25">
                    <span class="small text-white">Data Realtime</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Seksi Tambahan (Opsional): Grafik atau Log Aktivitas -->
    <div class="row mt-4">
        <div class="col-xl-6">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white">
                    <i class="fas fa-chart-area me-1 text-primary"></i>
                    Statistik Bimbingan Bulanan
                </div>
                <div class="card-body text-center py-5 text-muted">
                    <i class="fas fa-chart-bar fa-4x mb-3 text-light"></i>
                    <p>Grafik akan muncul di sini setelah data bimbingan cukup banyak.</p>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white">
                    <i class="fas fa-bullhorn me-1 text-primary"></i>
                    Pengumuman Sistem
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-0 shadow-sm mb-0">
                        <strong>Info:</strong> Batas akhir pengajuan judul skripsi adalah tanggal 30 November 2025. Harap informasikan kepada mahasiswa.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection