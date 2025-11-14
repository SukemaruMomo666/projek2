@extends('layouts.admin')

@section('title', 'Data Mahasiswa Bimbingan')

@push('styles')
<style>
    /* Kustomisasi kecil untuk foto profil di tabel */
    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e9ecef;
    }
    .table-mahasiswa th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom: 2px solid #e9ecef;
    }
    .table-mahasiswa td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <!-- Header -->
    <h1 class="mt-4 fw-bold text-dark">Data Mahasiswa Bimbingan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard Dosen</a></li>
        <li class="breadcrumb-item active">Data Mahasiswa</li>
    </ol>

    <!-- Alert (jika nanti ada) -->
    <!-- @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif -->

    <!-- Tabel Data Mahasiswa -->
    <div class="card mb-4 shadow border-0 rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Daftar Mahasiswa Aktif</h6>
            <span class="badge bg-primary rounded-pill">{{ $mahasiswas->count() }} Mahasiswa</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-mahasiswa align-middle mb-0" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th class="ps-4">Nama Mahasiswa</th>
                            <th>Email</th>
                            <th class="text-center">Semester</th>
                            <th>Program Studi</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop data mahasiswa dari Controller -->
                        @forelse ($mahasiswas as $mahasiswa)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mahasiswa->name) }}&background=random&color=fff" 
                                         class="student-avatar me-3" alt="{{ $mahasiswa->name }}">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $mahasiswa->name }}</div>
                                        <div class="small text-muted">NIM: {{ $mahasiswa->nim }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:{{ $mahasiswa->email }}">{{ $mahasiswa->email }}</a>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-75 rounded-pill">{{ $mahasiswa->semester }}</span>
                            </td>
                            <td>
                                {{ $mahasiswa->prodi }}
                            </td>
                            <td class="text-end pe-4">
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i> Lihat Progres
                                </a>
                            </td>
                        </tr>
                        @empty
                        <!-- Tampilkan jika tidak ada mahasiswa bimbingan -->
                        <tr>
                            <td colspan="5" class="text-center p-5">
                                <img src="https://placehold.co/100x100/EBF8FF/3B82F6?text=👥" class="mb-3" style="width: 80px; border-radius: 50%;">
                                <h5 class="text-muted">Data Kosong</h5>
                                <p class="text-muted small">Belum ada mahasiswa yang ditugaskan kepada Anda.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection