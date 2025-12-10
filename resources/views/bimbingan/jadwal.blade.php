@extends('layouts.admin')

@section('title', 'Jadwal Bimbingan')

@push('styles')
<style>
    .nav-tabs .nav-link { border: none; color: #6c757d; font-weight: 600; padding: 1rem 1.5rem; border-bottom: 3px solid transparent; }
    .nav-tabs .nav-link.active { color: #0d6efd; border-bottom: 3px solid #0d6efd; background: none; }
    .nav-tabs .nav-link:hover { border-color: transparent; color: #0d6efd; }
    
    .lecturer-card { transition: transform 0.2s, box-shadow 0.2s; border: 1px solid #e2e8f0; }
    .lecturer-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: #cbd5e1; }
    .lecturer-avatar { width: 80px; height: 80px; object-fit: cover; border: 3px solid #f1f5f9; }
    .badge-expertise { background-color: #e0f2fe; color: #0284c7; font-size: 0.7rem; font-weight: 600; }
    
    /* Styling Kartu Jadwal */
    .schedule-card { border-left: 5px solid #ccc; transition: all 0.2s; }
    .schedule-card:hover { background-color: #f8f9fa; }
    
    .schedule-card.Menunggu { border-left-color: #ffc107; }   /* Kuning */
    .schedule-card.Disetujui { border-left-color: #198754; }  /* Hijau */
    .schedule-card.Ditolak { border-left-color: #dc3545; }    /* Merah */
    .schedule-card.Reschedule { border-left-color: #0dcaf0; } /* Biru Muda */

    .blink-badge { animation: blinker 1.5s linear infinite; }
    @keyframes blinker { 50% { opacity: 0.5; } }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-0 text-dark">Jadwal & Booking</h1>
            <p class="text-muted mb-0">Atur pertemuan bimbingan dengan dosen.</p>
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

    <ul class="nav nav-tabs mb-4" id="scheduleTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="lecturer-tab" data-bs-toggle="tab" data-bs-target="#lecturer" type="button">
                <i class="fas fa-search me-2"></i>Cari Jadwal Dosen
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="myschedule-tab" data-bs-toggle="tab" data-bs-target="#myschedule" type="button">
                <i class="fas fa-calendar-check me-2"></i>Jadwal Saya 
                @php $notifCount = $jadwalSaya->whereIn('status', ['Menunggu', 'Reschedule'])->count(); @endphp
                @if($notifCount > 0)
                    <span class="badge bg-danger rounded-pill ms-1">{{ $notifCount }}</span>
                @endif
            </button>
        </li>
    </ul>

    <div class="tab-content" id="scheduleTabContent">
        
        <div class="tab-pane fade show active" id="lecturer" role="tabpanel">
            <div class="card border-0 shadow-sm mb-4 bg-light">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama dosen...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                @forelse ($dosens as $dosen)
                <div class="col-xl-4 col-md-6">
                    <div class="card lecturer-card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body text-center p-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($dosen->name) }}&background=0D8ABC&color=fff" class="lecturer-avatar rounded-circle mb-3 shadow-sm" alt="Dosen">
                            <h5 class="fw-bold mb-1 text-dark">{{ $dosen->name }}</h5>
                            <div class="text-muted small mb-3">NIDN: {{ $dosen->nidn ?? '-' }}</div>
                            
                            <div class="mb-3">
                                @if(Auth::user()->dosen_pembimbing_id == $dosen->id)
                                    <span class="badge bg-primary me-1">Pembimbing Kamu</span>
                                @else
                                    <span class="badge bg-light text-dark border">Dosen Pengajar</span>
                                @endif
                            </div>

                            <button class="btn btn-primary w-100 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#bookingModal" 
                                    onclick="setDosen('{{ $dosen->id }}', '{{ $dosen->name }}')">
                                <i class="fas fa-calendar-plus me-1"></i> Ajukan Pertemuan
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        Belum ada data dosen.
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <div class="tab-pane fade" id="myschedule" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">Daftar Pertemuan Saya</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        
                        @forelse ($jadwalSaya as $jadwal)
                        <div class="list-group-item p-3 schedule-card {{ $jadwal->status }}">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold text-dark">{{ $jadwal->topik }}</h6>
                                        
                                        @if($jadwal->status == 'Menunggu')
                                            <span class="badge bg-warning text-dark ms-2">MENUNGGU KONFIRMASI</span>
                                        @elseif($jadwal->status == 'Disetujui')
                                            <span class="badge bg-success ms-2">DISETUJUI</span>
                                        @elseif($jadwal->status == 'Ditolak')
                                            <span class="badge bg-danger ms-2">DITOLAK</span>
                                        @elseif($jadwal->status == 'Reschedule')
                                            <span class="badge bg-info text-dark ms-2 blink-badge">TAWARAN JADWAL BARU</span>
                                        @endif
                                    </div>

                                    <div class="text-muted small mt-1">
                                        <i class="fas fa-user me-1"></i> {{ $jadwal->dosen->name }} 
                                        
                                        @if($jadwal->status == 'Reschedule' && $jadwal->waktu_reschedule)
                                            <div class="mt-1 p-2 bg-info bg-opacity-10 rounded text-dark border border-info">
                                                <div class="fw-bold mb-1">Dosen menawarkan waktu pengganti:</div>
                                                <span class="text-decoration-line-through text-muted me-2">
                                                    {{ $jadwal->tanggal_pertemuan->format('d M Y') }} ({{ $jadwal->waktu_mulai }})
                                                </span>
                                                <i class="fas fa-arrow-right mx-1"></i>
                                                <span class="text-primary fw-bold">
                                                    {{ \Carbon\Carbon::parse($jadwal->waktu_reschedule)->format('d M Y') }} 
                                                    ({{ \Carbon\Carbon::parse($jadwal->waktu_reschedule)->format('H:i') }})
                                                </span>
                                            </div>
                                        @else
                                            &bull; <i class="fas fa-calendar me-1 ms-2"></i> {{ $jadwal->tanggal_pertemuan->format('l, d M Y') }}
                                            &bull; <i class="fas fa-clock me-1 ms-2"></i> {{ $jadwal->waktu_mulai }} WIB
                                        @endif
                                    </div>
                                    
                                    @if($jadwal->catatan_dosen)
                                    <div class="mt-2 small fst-italic text-muted">
                                        <i class="fas fa-comment-alt me-1"></i> Pesan Dosen: "{{ $jadwal->catatan_dosen }}"
                                    </div>
                                    @endif
                                </div>

                                <div class="text-end ms-3" style="min-width: 130px;">
                                    @if($jadwal->status == 'Menunggu')
                                        <form action="{{ route('jadwal.destroy', $jadwal->id) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100"><i class="fas fa-times me-1"></i> Batal</button>
                                        </form>
                                    
                                    @elseif($jadwal->status == 'Reschedule')
                                        <form action="{{ route('jadwal.approveReschedule', $jadwal->id) }}" method="POST" class="mb-1">
                                            @csrf @method('PATCH') 
                                            <button type="submit" class="btn btn-sm btn-primary w-100" onclick="return confirm('Setuju dengan waktu baru?')">
                                                <i class="fas fa-check me-1"></i> Setuju
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('jadwal.destroy', $jadwal->id) }}" method="POST" onsubmit="return confirm('Tolak tawaran ini? Jadwal akan dihapus.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                                                <i class="fas fa-times me-1"></i> Tolak
                                            </button>
                                        </form>

                                    @elseif($jadwal->status == 'Disetujui')
                                        <button class="btn btn-sm btn-success w-100 disabled" style="opacity: 1">
                                            <i class="fas fa-check-circle me-1"></i> Fix
                                        </button>
                                    
                                    @else
                                        <button class="btn btn-sm btn-secondary w-100 disabled">Selesai</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center p-5">
                            <img src="https://placehold.co/100x100/EBF8FF/3B82F6?text=📅" class="mb-3 rounded-circle">
                            <h5 class="text-muted">Belum Ada Jadwal</h5>
                            <p class="text-muted small">Ajukan pertemuan di tab pencarian.</p>
                        </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-calendar-check me-2"></i>Booking Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="dosen_id" id="modalDosenId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Dosen Tujuan</label>
                        <input type="text" class="form-control bg-light" id="modalDosenName" value="-" readonly>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Pilih Tanggal</label>
                            <input type="date" class="form-control" name="tanggal_pertemuan" value="{{ date('Y-m-d', strtotime('+1 day')) }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Pilih Jam</label>
                            <input type="time" class="form-control" name="waktu_mulai" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-bold small text-muted">Topik / Keperluan</label>
                        <textarea class="form-control" name="topik" rows="2" placeholder="Contoh: Konsultasi Bab 4 / Bimbingan Lomba" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function setDosen(id, name) {
        document.getElementById('modalDosenId').value = id;
        document.getElementById('modalDosenName').value = name;
    }
</script>
@endsection