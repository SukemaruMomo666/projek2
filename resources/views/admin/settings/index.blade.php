@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container-fluid px-4 pb-5">
    <h1 class="mt-4 fw-bold text-dark">Pengaturan Sistem</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Konfigurasi</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        
        <!-- KOLOM KIRI: Pengaturan Umum -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-cogs me-2"></i>Konfigurasi Umum</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nama Aplikasi</label>
                            <input type="text" class="form-control" name="app_name" value="{{ config('app.name') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Tahun Ajaran Aktif</label>
                            <select class="form-select" name="academic_year">
                                <option value="2024/2025" selected>2024/2025 - Ganjil</option>
                                <option value="2024/2025_genap">2024/2025 - Genap</option>
                                <option value="2025/2026">2025/2026 - Ganjil</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Batas Upload File (MB)</label>
                            <input type="number" class="form-control" name="upload_limit" value="10">
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="maintenanceMode">
                            <label class="form-check-label" for="maintenanceMode">Mode Maintenance (Tutup Akses)</label>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Backup Database -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-database me-2"></i>Database & Backup</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Unduh salinan database terbaru untuk keamanan data.</p>
                    <button class="btn btn-outline-dark w-100 mb-2">
                        <i class="fas fa-download me-2"></i> Download Backup SQL
                    </button>
                    <button class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash-restore me-2"></i> Reset Database (Hati-hati!)
                    </button>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Log Aktivitas -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-secondary"><i class="fas fa-history me-2"></i>Log Aktivitas Sistem</h6>
                    <span class="badge bg-secondary">Terbaru</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <!-- Mockup Log Data -->
                        <div class="list-group-item border-0 border-bottom py-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 text-primary">Login Admin</h6>
                                <small class="text-muted">Baru saja</small>
                            </div>
                            <small class="text-muted">Administrator berhasil login.</small>
                        </div>
                        <div class="list-group-item border-0 border-bottom py-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 text-success">Mahasiswa Baru Ditambahkan</h6>
                                <small class="text-muted">10 menit lalu</small>
                            </div>
                            <small class="text-muted">Admin menambahkan data mahasiswa: <strong>Qisty</strong>.</small>
                        </div>
                        <div class="list-group-item border-0 border-bottom py-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 text-warning">Plotting Dosen</h6>
                                <small class="text-muted">1 jam lalu</small>
                            </div>
                            <small class="text-muted">Admin mengubah pembimbing untuk <strong>Qisty</strong> menjadi <strong>Dr. Prabu</strong>.</small>
                        </div>
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 text-danger">Gagal Login</h6>
                                <small class="text-muted">2 jam lalu</small>
                            </div>
                            <small class="text-muted">Percobaan login gagal dengan email <strong>unknown@polsub.ac.id</strong>.</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="#" class="small text-decoration-none">Lihat Semua Log</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection