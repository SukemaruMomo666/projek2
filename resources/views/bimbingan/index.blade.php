@extends('layouts.admin')

@section('title', 'Logbook Bimbingan')

@push('styles')
<style>
    /* ... (CSS Anda dari file sebelumnya tetap di sini) ... */
    .card-header-actions { display: flex; justify-content: space-between; align-items: center; }
    .status-badge { font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 600; letter-spacing: 0.5px; }
    .table-logbook th { background-color: #f8f9fa; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; color: #6c757d; border-bottom: 2px solid #e9ecef; }
    .table-logbook td { vertical-align: middle; font-size: 0.9rem; }
    .date-box { text-align: center; line-height: 1.2; }
    .date-box .day { font-size: 1.2rem; font-weight: bold; color: #343a40; }
    .date-box .month { font-size: 0.75rem; color: #adb5bd; text-transform: uppercase; }
    .feedback-box { background-color: #fff9db; border-left: 3px solid #ffc107; padding: 8px 12px; border-radius: 4px; font-size: 0.85rem; color: #5c5c5c; font-style: italic; }
    .feedback-box.acc { background-color: #d1e7dd; border-left: 3px solid #198754; color: #0f5132; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    
    <!-- Header Halaman -->
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h2 mb-0 fw-bold text-dark">Logbook Bimbingan</h1>
            <p class="text-muted mb-0">Rekam jejak progres skripsi Anda</p>
        </div>
        <div>
            <button class="btn btn-outline-dark me-2" onclick="window.print()"><i class="fas fa-print me-1"></i> Cetak PDF</button>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addLogModal">
                <i class="fas fa-plus me-1"></i> Catat Bimbingan
            </button>
        </div>
    </div>

    <!-- 1. ALERT SUKSES (setelah simpan form) -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 2. ALERT VALIDASI ERROR (jika form salah isi) -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Gagal menyimpan!</strong> Harap periksa error berikut:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <!-- Statistik Ringkas (Sekarang Dinamis) -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-primary mb-1">TOTAL BIMBINGAN</div>
                            <!-- 3. Tampilkan jumlah data dari controller -->
                            <div class="h3 mb-0 fw-bold">{{ $logbooks->count() }}</div>
                        </div>
                        <div class="ms-3 text-primary opacity-50"><i class="fas fa-book fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-success mb-1">DISETUJUI (ACC)</div>
                            <!-- 4. Tampilkan jumlah data yg statusnya 'Disetujui' -->
                            <div class="h3 mb-0 fw-bold">{{ $logbooks->where('status', 'Disetujui')->count() }}</div>
                        </div>
                        <div class="ms-3 text-success opacity-50"><i class="fas fa-check-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-warning mb-1">PERLU REVISI</div>
                            <!-- 5. Tampilkan jumlah data yg statusnya 'Revisi' -->
                            <div class="h3 mb-0 fw-bold">{{ $logbooks->where('status', 'Revisi')->count() }}</div>
                        </div>
                        <div class="ms-3 text-warning opacity-50"><i class="fas fa-exclamation-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Logbook -->
    <div class="card mb-4 shadow border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>Riwayat Konsultasi</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-logbook mb-0" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th class="text-center" width="80">Tanggal</th>
                            <th width="25%">Topik Bimbingan</th>
                            <th width="35%">Catatan Dosen</th>
                            <th width="15%">Bukti / File</th>
                            <th class="text-center" width="10%">Status</th>
                            <th class="text-end" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 6. Looping Data Asli (MENGGANTIKAN DUMMY DATA) -->
                        @forelse ($logbooks as $log)
                        <tr>
                            <td>
                                <div class="date-box">
                                    <div class="day">{{ $log->tanggal_bimbingan->format('d') }}</div>
                                    <div class="month">{{ $log->tanggal_bimbingan->format('M y') }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border mb-1">BAB 3</span> <!-- (Ini masih statis, nanti bisa diambil dari $log->bab) -->
                                <div class="fw-bold text-dark">{{ $log->materi }}</div>
                                <small class="text-muted">{{ $log->catatan_mahasiswa ?? 'Tidak ada catatan pribadi.' }}</small>
                            </td>
                            <td>
                                <!-- Tampilkan catatan dosen jika ada -->
                                @if ($log->catatan_dosen)
                                    <div class="feedback-box {{ $log->status == 'Disetujui' ? 'acc' : '' }}">
                                        <i class="fas {{ $log->status == 'Disetujui' ? 'fa-check' : 'fa-comment-alt' }} me-1"></i>
                                        "{{ $log->catatan_dosen }}"
                                        <div class="mt-1 small text-muted text-end">- {{ $log->dosen->name ?? 'Dosen' }}</div>
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Belum ada catatan dosen...</span>
                                @endif
                            </td>
                            <td>
                                @if ($log->file_path)
                                    <!-- 7. Buat link file yang bisa diklik -->
                                    <a href="{{ Storage::url($log->file_path) }}" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-file-pdf text-danger me-1"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted small fst-italic">Tidak ada file</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($log->status == 'Disetujui')
                                    <span class="badge bg-success status-badge">DISETUJUI</span>
                                @elseif ($log->status == 'Revisi')
                                    <span class="badge bg-warning text-dark status-badge">REVISI</span>
                                @else
                                    <span class="badge bg-secondary status-badge">MENUNGGU</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" title="Edit" {{ $log->status != 'Menunggu' ? 'disabled' : '' }}>
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <!-- Tampilkan jika tidak ada data -->
                        <tr>
                            <td colspan="6" class="text-center p-5">
                                <img src="https://placehold.co/100x100/EBF8FF/3B82F6?text=📂" class="mb-3" style="width: 80px; border-radius: 50%;">
                                <h5 class="text-muted">Belum Ada Data Logbook</h5>
                                <p class="text-muted small">Mulai catat bimbingan pertama Anda dengan menekan tombol "Catat Bimbingan".</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH BIMBINGAN -->
<div class="modal fade" id="addLogModal" tabindex="-1" aria-labelledby="addLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="addLogModalLabel"><i class="fas fa-pen-alt me-2"></i>Catat Bimbingan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- 8. Arahkan Form ke Route 'bimbingan.store' -->
            <form action="{{ route('bimbingan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tanggal_bimbingan" class="form-label fw-bold">Tanggal Bimbingan</label>
                            <!-- 9. Ganti nama input -->
                            <input type="date" class="form-control" id="tanggal_bimbingan" name="tanggal_bimbingan" value="{{ old('tanggal_bimbingan', date('Y-m-d')) }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="materi" class="form-label fw-bold">Materi / Pokok Bahasan</label>
                            <!-- 10. Ganti nama input -->
                            <input type="text" class="form-control" id="materi" name="materi" placeholder="Contoh: Revisi Latar Belakang Masalah" value="{{ old('materi') }}" required>
                        </div>

                        <div class="col-md-12">
                            <label for="catatan_mahasiswa" class="form-label fw-bold">Hasil Diskusi / Catatan Pribadi</label>
                            <!-- 11. Ganti nama input -->
                            <textarea class="form-control" id="catatan_mahasiswa" name="catatan_mahasiswa" rows="4" placeholder="Tuliskan poin-poin penting yang dibahas...">{{ old('catatan_mahasiswa') }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label for="file" class="form-label fw-bold">Upload File Draft (Opsional)</label>
                            <input type="file" class="form-control" id="file" name="file">
                            <div class="form-text">Format: PDF atau DOCX. Maksimal 5MB.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Logbook</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection