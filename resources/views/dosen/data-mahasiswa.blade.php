@extends('layouts.admin')

@section('title', 'Data Mahasiswa Bimbingan')

@push('styles')
<style>
    .student-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #e9ecef; }
    .table-mahasiswa th { background-color: #f8f9fa; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; color: #6c757d; border-bottom: 2px solid #e9ecef; }
    .table-mahasiswa td { vertical-align: middle; font-size: 0.9rem; }
    .modal-header-custom { background-color: #0d6efd; color: white; }
    .timeline-item { border-left: 2px solid #e9ecef; padding-left: 20px; padding-bottom: 20px; position: relative; }
    .timeline-item::before { content: ''; width: 12px; height: 12px; background: #0d6efd; border-radius: 50%; position: absolute; left: -7px; top: 0; }
    .timeline-item:last-child { border-left: 0; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <h1 class="mt-4 fw-bold text-dark">Data Mahasiswa Bimbingan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dosen.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Data Mahasiswa</li>
    </ol>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <div class="small fw-bold text-primary mb-1">TOTAL MAHASISWA</div>
                    <div class="h3 mb-0">{{ $mahasiswas->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-body">
                    <div class="small fw-bold text-danger mb-1">KURANG BIMBINGAN (< 3x)</div>
                    @php 
                        $kurangBimbingan = $mahasiswas->filter(function($m) { 
                            return $m->bimbingans->count() < 3; 
                        })->count(); 
                    @endphp
                    <div class="h3 mb-0">{{ $kurangBimbingan }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow border-0 rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Daftar Mahasiswa Aktif</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-mahasiswa align-middle mb-0" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th class="ps-4">Nama Mahasiswa</th>
                            <th>Kontak</th>
                            <th class="text-center">Semester</th>
                            <th class="text-center">Total Bimbingan</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswas as $mahasiswa)
                        @php
                            $totalLog = $mahasiswa->bimbingans->count();
                            $statusAman = $totalLog >= 3;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mahasiswa->name) }}&background=random&color=fff" class="student-avatar me-3">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $mahasiswa->name }}</div>
                                        <div class="small text-muted">NIM: {{ $mahasiswa->nim }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column small">
                                    <a href="mailto:{{ $mahasiswa->email }}" class="text-decoration-none text-muted mb-1"><i class="fas fa-envelope me-1"></i> {{ $mahasiswa->email }}</a>
                                    <span class="text-muted"><i class="fas fa-phone me-1"></i> - </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $mahasiswa->semester }}</span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold {{ $statusAman ? 'text-success' : 'text-danger' }}">{{ $totalLog }}</span> 
                                <span class="text-muted small">/ 3</span>
                            </td>
                            <td class="text-center">
                                @if($statusAman)
                                    <span class="badge bg-success rounded-pill">Aman</span>
                                @else
                                    <span class="badge bg-danger rounded-pill">Kurang</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal{{ $mahasiswa->id }}">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="detailModal{{ $mahasiswa->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header modal-header-custom">
                                        <h5 class="modal-title fw-bold"><i class="fas fa-user-graduate me-2"></i>Riwayat Bimbingan: {{ $mahasiswa->name }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4 bg-light">
                                        
                                        <div class="card mb-4 border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="row text-center">
                                                    <div class="col-4 border-end">
                                                        <small class="text-muted d-block text-uppercase">Total Bimbingan</small>
                                                        <span class="h4 fw-bold text-primary">{{ $totalLog }}</span>
                                                    </div>
                                                    <div class="col-4 border-end">
                                                        <small class="text-muted d-block text-uppercase">Terakhir Bimbingan</small>
                                                        <span class="fw-bold text-dark">
                                                            {{ $mahasiswa->bimbingans->first() ? $mahasiswa->bimbingans->first()->tanggal_bimbingan->format('d M Y') : '-' }}
                                                        </span>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted d-block text-uppercase">Status</small>
                                                        @if($statusAman)
                                                            <span class="badge bg-success">Memenuhi Syarat</span>
                                                        @else
                                                            <span class="badge bg-danger">Belum Memenuhi</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold text-muted mb-3 text-uppercase small">Timeline Aktivitas</h6>
                                        
                                        <div class="ps-2">
                                            @forelse($mahasiswa->bimbingans as $log)
                                            <div class="timeline-item">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="fw-bold text-dark">{{ $log->materi }}</span>
                                                    <span class="small text-muted">{{ $log->tanggal_bimbingan->format('d M Y') }}</span>
                                                </div>
                                                <div class="p-3 bg-white rounded border">
                                                    <p class="mb-1 small text-muted fst-italic">"{{ $log->catatan_mahasiswa }}"</p>
                                                    
                                                    @if($log->status == 'Disetujui')
                                                        <span class="badge bg-success bg-opacity-10 text-success"><i class="fas fa-check me-1"></i>Disetujui</span>
                                                    @elseif($log->status == 'Revisi')
                                                        <span class="badge bg-warning bg-opacity-10 text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Revisi</span>
                                                        @if($log->catatan_dosen)
                                                            <div class="mt-2 small text-danger border-top pt-2">
                                                                <strong>Catatan Anda:</strong> {{ $log->catatan_dosen }}
                                                            </div>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Menunggu</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @empty
                                            <div class="text-center py-4 text-muted">
                                                <i class="fas fa-history fa-2x mb-2 opacity-50"></i>
                                                <p>Belum ada riwayat bimbingan.</p>
                                            </div>
                                            @endforelse
                                        </div>

                                    </div>
                                    <div class="modal-footer bg-white">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center p-5">
                                <h5 class="text-muted">Data Kosong</h5>
                                <p class="text-muted small">Belum ada mahasiswa yang ditugaskan.</p>
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