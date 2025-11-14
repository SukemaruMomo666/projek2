@extends('layouts.admin')

@section('title', 'Jadwal Bimbingan')

@push('styles')
<style>
    /* ... (CSS Anda dari file sebelumnya tetap di sini) ... */
    .nav-tabs .nav-link { border: none; color: #6c757d; font-weight: 600; padding: 1rem 1.5rem; border-bottom: 3px solid transparent; }
    .nav-tabs .nav-link.active { color: #0d6efd; border-bottom: 3px solid #0d6efd; background: none; }
    .nav-tabs .nav-link:hover { border-color: transparent; color: #0d6efd; }
    .lecturer-card { transition: transform 0.2s, box-shadow 0.2s; border: 1px solid #e2e8f0; }
    .lecturer-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: #cbd5e1; }
    .lecturer-avatar { width: 80px; height: 80px; object-fit: cover; border: 3px solid #f1f5f9; }
    .badge-expertise { background-color: #e0f2fe; color: #0284c7; font-size: 0.7rem; font-weight: 600; }
    .time-slot { cursor: pointer; border: 1px solid #dee2e6; border-radius: 6px; padding: 5px 10px; text-align: center; font-size: 0.85rem; transition: all 0.2s; background-color: #fff; }
    .time-slot:hover { border-color: #0d6efd; color: #0d6efd; background-color: #f8f9fa; }
    .time-slot.selected { background-color: #0d6efd; color: white; border-color: #0d6efd; }
    .time-slot.disabled { background-color: #f8f9fa; color: #adb5bd; cursor: not-allowed; border-color: #f1f5f9; }
    .schedule-card { border-left: 4px solid #0d6efd; }
    .schedule-card.Menunggu { border-left-color: #ffc107; }
    .schedule-card.Disetujui { border-left-color: #198754; }
    .schedule-card.Ditolak { border-left-color: #dc3545; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-0 text-dark">Jadwal & Booking</h1>
            <p class="text-muted mb-0">Atur pertemuan bimbingan dengan dosen pembimbing.</p>
        </div>
    </div>

    <!-- 1. ALERT SUKSES & ERROR -->
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

    <!-- Navigasi Tab -->
    <ul class="nav nav-tabs mb-4" id="scheduleTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="lecturer-tab" data-bs-toggle="tab" data-bs-target="#lecturer" type="button" role="tab"><i class="fas fa-search me-2"></i>Cari Jadwal Dosen</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="myschedule-tab" data-bs-toggle="tab" data-bs-target="#myschedule" type="button" role="tab">
                <i class="fas fa-calendar-check me-2"></i>Jadwal Saya 
                <!-- 2. PERBAIKAN: Badge Dinamis (Hitung yang 'Menunggu') -->
                @if($jadwalSaya->where('status', 'Menunggu')->count() > 0)
                    <span class="badge bg-danger rounded-pill ms-1">{{ $jadwalSaya->where('status', 'Menunggu')->count() }}</span>
                @endif
            </button>
        </li>
    </ul>

    <div class="tab-content" id="scheduleTabContent">
        
        <!-- TAB 1: CARI JADWAL DOSEN (DINAMIS) -->
        <div class="tab-pane fade show active" id="lecturer" role="tabpanel">
            
            <!-- Search Filter (Masih statis, bisa dikembangkan nanti) -->
            <div class="card border-0 shadow-sm mb-4 bg-light">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari nama dosen...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select">
                                <option selected>Semua Hari</option>
                                <option value="1">Senin</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- 3. PERBAIKAN: Looping Kartu Dosen dari Controller -->
                @forelse ($dosens as $dosen)
                <div class="col-xl-4 col-md-6">
                    <div class="card lecturer-card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body text-center p-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($dosen->name) }}&background=0D8ABC&color=fff" class="lecturer-avatar rounded-circle mb-3 shadow-sm" alt="Dosen">
                            <h5 class="fw-bold mb-1 text-dark">{{ $dosen->name }}</h5>
                            <div class="text-muted small mb-3">NIDN: {{ $dosen->nidn ?? '-' }}</div>
                            
                            <div class="mb-3">
                                <span class="badge badge-expertise me-1">Rekayasa Perangkat Lunak</span>
                                <span class="badge badge-expertise">AI</span>
                            </div>

                            <div class="alert alert-light border small text-start mb-3">
                                <i class="fas fa-info-circle text-info me-1"></i> <strong>Status:</strong> 
                                <span class="text-success fw-bold">Tersedia Hari Ini</span>
                            </div>

                            <!-- 4. PERBAIKAN: Kirim ID & Nama Dosen ke Modal -->
                            <button class="btn btn-primary w-100 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#bookingModal" 
                                    onclick="setDosen('{{ $dosen->id }}', '{{ $dosen->name }}')">
                                <i class="fas fa-calendar-plus me-1"></i> Ajukan Pertemuan
                            </button>
                        </div>
                        <div class="card-footer bg-white border-top-0 text-center pb-3">
                            <small class="text-muted">Jadwal Terdekat: <strong>Senin, 09:00 WIB</strong></small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        Dosen pembimbing Anda belum diatur. Harap hubungi admin.
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 2: JADWAL SAYA (DINAMIS) -->
        <div class="tab-pane fade" id="myschedule" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">Daftar Pertemuan Saya</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        
                        <!-- 5. PERBAIKAN: Looping Jadwal Saya dari Controller -->
                        @forelse ($jadwalSaya as $jadwal)
                        <div class="list-group-item p-3 schedule-card {{ $jadwal->status }}">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold text-dark">{{ $jadwal->topik }}</h6>
                                        <!-- Status Badge Dinamis -->
                                        @if($jadwal->status == 'Menunggu')
                                            <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7em;">MENUNGGU KONFIRMASI</span>
                                        @elseif($jadwal->status == 'Disetujui')
                                            <span class="badge bg-success ms-2" style="font-size: 0.7em;">DISETUJUI</span>
                                        @elseif($jadwal->status == 'Ditolak')
                                            <span class="badge bg-danger ms-2" style="font-size: 0.7em;">DITOLAK</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i> {{ $jadwal->dosen->name ?? 'Dosen' }} &bull; 
                                        <i class="fas fa-calendar me-1 ms-2"></i> {{ $jadwal->tanggal_pertemuan->format('l, d M Y') }} &bull; 
                                        <i class="fas fa-clock me-1 ms-2"></i> {{ $jadwal->waktu_mulai }} WIB
                                    </small>
                                    
                                    <!-- Catatan Dosen jika Ditolak -->
                                    @if($jadwal->status == 'Ditolak' && $jadwal->catatan_dosen)
                                    <div class="mt-2 small text-danger bg-danger bg-opacity-10 p-2 rounded">
                                        <i class="fas fa-times-circle me-1"></i> <strong>Alasan Ditolak:</strong> "{{ $jadwal->catatan_dosen }}"
                                    </div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <!-- Hanya bisa batal jika masih Menunggu -->
                                    @if($jadwal->status == 'Menunggu')
                                    <button class="btn btn-sm btn-outline-danger" title="Batalkan"><i class="fas fa-times"></i></button>
                                    @else
                                    <button class="btn btn-sm btn-light text-muted disabled"><i class="fas fa-check"></i></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center p-5">
                            <img src="https://placehold.co/100x100/EBF8FF/3B82F6?text=📅" class="mb-3" style="width: 80px; border-radius: 50%;">
                            <h5 class="text-muted">Belum Ada Pengajuan Jadwal</h5>
                            <p class="text-muted small">Anda dapat mengajukan jadwal pertemuan pada tab "Cari Jadwal Dosen".</p>
                        </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BOOKING (DINAMIS) -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-calendar-check me-2"></i>Booking Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <!-- 6. PERBAIKAN: Arahkan Form ke Route 'jadwal.store' -->
            <form action="{{ route('jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    
                    <!-- 7. PERBAIKAN: Input Tersembunyi untuk ID Dosen -->
                    <input type="hidden" name="dosen_id" id="modalDosenId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Dosen Pembimbing</label>
                        <input type="text" class="form-control bg-light" id="modalDosenName" value="-" readonly>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Pilih Tanggal</label>
                            <input type="date" class="form-control" name="tanggal_pertemuan" value="{{ old('tanggal_pertemuan') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Pilih Jam</label>
                            <input type="time" class="form-control" name="waktu_mulai" value="{{ old('waktu_mulai') }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-bold small text-muted">Topik / Keperluan</label>
                        <textarea class="form-control" name="topik" rows="2" placeholder="Contoh: Konsultasi Bab 4" required>{{ old('topik') }}</textarea>
                    </div>

                    <!-- Input Slot Waktu (Dihapus karena diganti Input Jam Manual) -->
                    
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 8. PERBAIKAN: Update Script -->
<script>
    // Fungsi ini sekarang menerima ID dan NAMA
    function setDosen(id, name) {
        document.getElementById('modalDosenId').value = id;
        document.getElementById('modalDosenName').value = name;
    }

    // Fungsi ini tidak kita pakai lagi, tapi kita biarkan jika nanti diperlukan
    function selectSlot(element) {
        if (element.classList.contains('disabled')) return;
        document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        document.getElementById('selected_slot').value = element.innerText;
    }
</script>
@endsection
