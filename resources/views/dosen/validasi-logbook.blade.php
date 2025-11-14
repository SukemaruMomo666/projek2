@extends('layouts.admin')

@section('title', 'Validasi Logbook')

@push('styles')
<style>
    /* Card Logbook - Mirip email */
    .logbook-item {
        border-left: 5px solid #ffc107; /* Kuning = Menunggu */
        transition: box-shadow 0.2s;
    }
    .logbook-item.status-Disetujui { border-left-color: #198754; }
    .logbook-item.status-Revisi { border-left-color: #dc3545; }
    
    .logbook-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .logbook-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }
    .logbook-body .file-link {
        display: inline-block;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        text-decoration: none;
        color: #0d6efd;
        font-weight: 600;
        transition: background-color 0.2s;
    }
    .logbook-body .file-link:hover {
        background-color: #e9ecef;
    }
    .logbook-body .mahasiswa-note {
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
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-0 text-dark">Validasi Logbook</h1>
            <p class="text-muted mb-0">Review dan berikan feedback untuk mahasiswa bimbingan Anda.</p>
        </div>
    </div>

    <!-- Alert Sukses (Setelah submit review) -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- Alert Error Validasi (Jika form salah) -->
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

    <!-- 1. DAFTAR MENUNGGU VALIDASI (Prioritas) -->
    <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-warning bg-opacity-10 py-3">
            <h6 class="m-0 fw-bold text-warning-emphasis"><i class="fas fa-clock me-2"></i>Menunggu Respon Anda ({{ $logbookMenunggu->count() }})</h6>
        </div>
        <div class="card-body p-3 p-md-4">
            
            @forelse ($logbookMenunggu as $log)
            <div class="card shadow-sm border-0 logbook-item mb-3">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex">
                        <!-- Foto Mahasiswa -->
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($log->mahasiswa->name) }}&background=random&color=fff" 
                             class="logbook-avatar me-3" alt="{{ $log->mahasiswa->name }}">
                        
                        <!-- Info Utama -->
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">{{ $log->mahasiswa->name }}</h5>
                                    <small class="text-muted">NIM: {{ $log->mahasiswa->nim }}</small>
                                </div>
                                <span class="text-muted small d-none d-md-block">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <hr class="my-3">

                            <!-- Isi Bimbingan -->
                            <div class="logbook-body">
                                <h6 class="fw-bold">{{ $log->materi }}</h6>
                                <p class="text-muted small">Diajukan pada: {{ $log->tanggal_bimbingan->format('l, d F Y') }}</p>
                                
                                @if($log->catatan_mahasiswa)
                                <p class="mahasiswa-note">
                                    <i class="fas fa-quote-left fa-xs me-1"></i>
                                    {{ $log->catatan_mahasiswa }}
                                    <i class="fas fa-quote-right fa-xs ms-1"></i>
                                </p>
                                @endif
                                
                                @if($log->file_path)
                                <a href="{{ Storage::url($log->file_path) }}" target="_blank" class="file-link mt-2">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>Lihat Lampiran
                                </a>
                                @endif
                            </div>

                            <!-- Tombol Aksi Dosen -->
                            <div class="text-end mt-3">
                                <button class="btn btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#reviewModal"
                                        onclick="openReviewModal({{ $log->id }}, '{{ $log->mahasiswa->name }}', '{{ $log->materi }}')">
                                    <i class="fas fa-pen-to-square me-1"></i> Beri Tanggapan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center p-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5 class="text-muted">Luar Biasa!</h5>
                <p class="text-muted small">Tidak ada logbook yang perlu direview.</p>
            </div>
            @endforelse
        </div>
    </div>


    <!-- 2. RIWAYAT VALIDASI (Selesai) -->
    <div class="card shadow border-0 rounded-3">
        <div class="card-header bg-light py-3">
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-history me-2"></i>Riwayat Validasi Selesai</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Mahasiswa</th>
                            <th>Tanggal</th>
                            <th>Materi</th>
                            <th>Feedback Anda</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logbookSelesai as $log)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $log->mahasiswa->name }}</div>
                                <small class="text-muted">{{ $log->mahasiswa->nim }}</small>
                            </td>
                            <td>{{ $log->tanggal_bimbingan->format('d M Y') }}</td>
                            <td>{{ $log->materi }}</td>
                            <td><small class="fst-italic text-muted">"{{ Str::limit($log->catatan_dosen, 50) }}"</small></td>
                            <td class="text-center">
                                @if($log->status == 'Disetujui')
                                <span class="badge bg-success">Disetujui</span>
                                @else
                                <span class="badge bg-danger">Revisi</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-4 text-muted">Belum ada riwayat validasi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


<!-- MODAL REVIEW DOSEN -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <!-- Form ini akan mengirim data ke DosenController@storeLogbookValidasi -->
            <form action="{{ route('dosen.validasi.logbook.store') }}" method="POST">
                @csrf
                <!-- Input tersembunyi untuk ID Logbook -->
                <input type="hidden" name="logbook_id" id="modalLogbookId">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="reviewModalLabel">Beri Tanggapan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    
                    <!-- Info Mahasiswa (dinamis) -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">Mahasiswa:</label>
                        <input type="text" class="form-control bg-light" id="modalMahasiswaName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Materi:</label>
                        <input type="text" class="form-control bg-light" id="modalMateri" readonly>
                    </div>
                    
                    <!-- Form Input Dosen -->
                    <div class="mb-3">
                        <label for="catatan_dosen" class="form-label fw-bold">Catatan / Feedback Anda</label>
                        <textarea class="form-control" id="catatan_dosen" name="catatan_dosen" rows="4" placeholder="Tuliskan feedback, arahan revisi, atau ACC di sini..." required></textarea>
                    </div>

                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <!-- Tombol Aksi: Value="Revisi" atau "Disetujui" -->
                    <button type="submit" name="status" value="Revisi" class="btn btn-warning shadow-sm">
                        <i class="fas fa-edit me-1"></i> Kirim Revisi
                    </button>
                    <button type="submit" name="status" value="Disetujui" class="btn btn-success shadow-sm">
                        <i class="fas fa-check-circle me-1"></i> Setujui (ACC)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script untuk modal -->
@push('scripts')
<script>
    function openReviewModal(logbookId, mahasiswaName, materi) {
        // Mengisi data ke dalam form modal
        document.getElementById('modalLogbookId').value = logbookId;
        document.getElementById('modalMahasiswaName').value = mahasiswaName;
        document.getElementById('modalMateri').value = materi;
    }

    // Jika ada error validasi, otomatis buka lagi modalnya
    @if ($errors->any())
        var reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'), {});
        reviewModal.show();
    @endif
</script>
@endpush
@endsection
