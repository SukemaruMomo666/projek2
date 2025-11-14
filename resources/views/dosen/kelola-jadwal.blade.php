@extends('layouts.admin')

@section('title', 'Kelola Jadwal Pertemuan')

@push('styles')
<style>
    /* Style ini mirip dengan validasi-logbook */
    .jadwal-item {
        border-left: 5px solid #ffc107; /* Kuning = Menunggu */
        transition: box-shadow 0.2s;
    }
    .jadwal-item.status-Disetujui { border-left-color: #198754; }
    .jadwal-item.status-Ditolak { border-left-color: #dc3545; }
    
    .jadwal-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .jadwal-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }
    .jadwal-body .mahasiswa-note {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 10px 15px;
        font-style: italic;
        color: #495057;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-0 text-dark">Kelola Jadwal</h1>
            <p class="text-muted mb-0">Setujui atau tolak pengajuan jadwal bimbingan dari mahasiswa.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Gagal mengirim!</strong> Harap periksa error berikut:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-warning bg-opacity-10 py-3">
            <h6 class="m-0 fw-bold text-warning-emphasis"><i class="fas fa-clock me-2"></i>Menunggu Respon Anda ({{ $jadwalMenunggu->count() }})</h6>
        </div>
        <div class="card-body p-3 p-md-4">
            
            @forelse ($jadwalMenunggu as $jadwal)
            <div class="card shadow-sm border-0 jadwal-item mb-3">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($jadwal->mahasiswa->name) }}&background=random&color=fff" 
                             class="jadwal-avatar me-3" alt="{{ $jadwal->mahasiswa->name }}">
                        
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">{{ $jadwal->mahasiswa->name }}</h5>
                                    <small class="text-muted">NIM: {{ $jadwal->mahasiswa->nim }}</small>
                                </div>
                                <span class="text-muted small d-none d-md-block">{{ $jadwal->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <hr class="my-3">

                            <div class="jadwal-body">
                                <h6 class="fw-bold">Mengajukan Jadwal: <span class="text-primary">{{ $jadwal->tanggal_pertemuan->format('l, d F Y') }}</span> pukul <span class="text-primary">{{ $jadwal->waktu_mulai }}</span></h6>
                                
                                @if($jadwal->topik)
                                <p class="mahasiswa-note">
                                    <i class="fas fa-quote-left fa-xs me-1"></i>
                                    {{ $jadwal->topik }}
                                    <i class="fas fa-quote-right fa-xs ms-1"></i>
                                </p>
                                @endif
                            </div>

                            <div class="text-end mt-3">
                                
                                <form action="{{ route('dosen.jadwal.store') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                    <input type="hidden" name="status" value="Ditolak">
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="fas fa-times me-1"></i> Tolak
                                    </button>
                                </form>
                                <form action="{{ route('dosen.jadwal.store') }}" method="POST" class="d-inline ms-2">
                                    @csrf
                                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                    <input type="hidden" name="status" value="Disetujui">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check me-1"></i> Setujui
                                    </button>
                                </form>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center p-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5 class="text-muted">Luar Biasa!</h5>
                <p class="text-muted small">Tidak ada pengajuan jadwal yang perlu divalidasi.</p>
            </div>
            @endforelse
        </div>
    </div>


    <div class="card shadow border-0 rounded-3">
        <div class="card-header bg-light py-3">
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-history me-2"></i>Riwayat Jadwal Terkonfirmasi</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Mahasiswa</th>
                            <th>Tanggal</th>
                            <th>Topik</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwalSelesai as $jadwal)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $jadwal->mahasiswa->name }}</div>
                                <small class="text-muted">{{ $jadwal->mahasiswa->nim }}</small>
                            </td>
                            <td>{{ $jadwal->tanggal_pertemuan->format('d M Y') }} ({{ $jadwal->waktu_mulai }})</td>
                            <td>{{ $jadwal->topik }}</td>
                            <td class="text-center">
                                @if($jadwal->status == 'Disetujui')
                                <span class="badge bg-success">Disetujui</span>
                                @else
                                <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center p-4 text-muted">Belum ada riwayat jadwal.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection