@extends('layouts.admin')

@section('title', 'Kelola Jadwal Pertemuan')

@push('styles')
<style>
    .jadwal-item {
        border-left: 5px solid #ffc107; /* Kuning = Menunggu */
        transition: box-shadow 0.2s;
    }
    .jadwal-item.status-Reschedule { border-left-color: #0dcaf0; } /* Biru = Reschedule */
    
    .jadwal-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .jadwal-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    .mahasiswa-note {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        font-style: italic;
        color: #495057;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-0 text-dark">Kelola Jadwal</h1>
            <p class="text-muted mb-0">Setujui, tolak, atau ajukan waktu pengganti untuk mahasiswa.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> Gagal! Periksa inputan Anda.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-warning bg-opacity-10 py-3">
            <h6 class="m-0 fw-bold text-warning-emphasis"><i class="fas fa-clock me-2"></i>Menunggu Respon Anda ({{ $jadwalMenunggu->count() }})</h6>
        </div>
        <div class="card-body p-3 p-md-4 bg-light">
            
            @forelse ($jadwalMenunggu as $jadwal)
            <div class="card shadow-sm border-0 jadwal-item status-{{ $jadwal->status }} mb-3">
                <div class="card-body p-3 p-md-4">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($jadwal->mahasiswa->name) }}&background=random&color=fff" 
                                     class="jadwal-avatar me-3" alt="{{ $jadwal->mahasiswa->name }}">
                                
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">{{ $jadwal->mahasiswa->name }}</h5>
                                    <small class="text-muted">NIM: {{ $jadwal->mahasiswa->nim }} &bull; {{ $jadwal->created_at->diffForHumans() }}</small>
                                    
                                    @if($jadwal->status == 'Reschedule')
                                        <div class="badge bg-info text-dark mt-2">Menunggu Persetujuan Mahasiswa (Reschedule)</div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 ps-5">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="fas fa-calendar-alt me-2"></i> 
                                    {{ $jadwal->tanggal_pertemuan->format('l, d F Y') }} 
                                    <span class="text-dark ms-2"><i class="fas fa-clock me-1"></i> {{ $jadwal->waktu_mulai }} WIB</span>
                                </h6>
                                <div class="mahasiswa-note border">
                                    <i class="fas fa-quote-left fa-xs me-2 text-muted"></i>
                                    {{ $jadwal->topik }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 border-start d-flex flex-column justify-content-center mt-3 mt-md-0">
                            <form action="{{ route('dosen.kelola-jadwal') }}" method="POST">
                                @csrf
                                <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                
                                <label class="form-label fw-bold small text-muted">Aksi Dosen:</label>
                                <select class="form-select mb-3" name="aksi" id="aksi-{{ $jadwal->id }}" onchange="toggleForm('{{ $jadwal->id }}')">
                                    <option value="terima" selected>✅ Terima Jadwal Ini</option>
                                    <option value="reschedule">🔄 Ajukan Waktu Lain (Reschedule)</option>
                                    <option value="tolak">❌ Tolak Pengajuan</option>
                                </select>

                                <div id="form-reschedule-{{ $jadwal->id }}" class="d-none bg-light p-2 rounded border mb-3">
                                    <label class="small fw-bold">Waktu Baru:</label>
                                    <input type="date" class="form-control form-control-sm mb-2" name="tanggal_baru">
                                    <input type="time" class="form-control form-control-sm" name="jam_baru">
                                </div>

                                <div class="mb-3">
                                    <textarea class="form-control form-control-sm" name="pesan" rows="2" placeholder="Catatan untuk mahasiswa (Opsional)..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 btn-sm fw-bold">
                                    <i class="fas fa-paper-plane me-1"></i> Kirim Respon
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center p-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3 opacity-50"></i>
                <h5 class="text-muted">Tidak ada pengajuan baru.</h5>
                <p class="text-muted small">Semua jadwal sudah Anda respon.</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="card shadow border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-history me-2"></i>Riwayat Keputusan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Mahasiswa</th>
                            <th>Jadwal</th>
                            <th>Topik</th>
                            <th>Catatan Anda</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwalSelesai as $jadwal)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $jadwal->mahasiswa->name }}</div>
                            </td>
                            <td>{{ $jadwal->tanggal_pertemuan->format('d M Y') }} ({{ $jadwal->waktu_mulai }})</td>
                            <td>{{ Str::limit($jadwal->topik, 30) }}</td>
                            <td class="small text-muted fst-italic">{{ Str::limit($jadwal->catatan_dosen, 40) ?? '-' }}</td>
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
                            <td colspan="5" class="text-center p-4 text-muted">Belum ada riwayat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    function toggleForm(id) {
        var aksi = document.getElementById('aksi-' + id).value;
        var formReschedule = document.getElementById('form-reschedule-' + id);
        
        if (aksi === 'reschedule') {
            formReschedule.classList.remove('d-none');
        } else {
            formReschedule.classList.add('d-none');
        }
    }
</script>
@endsection