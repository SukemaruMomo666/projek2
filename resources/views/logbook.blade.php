@extends('layouts.admin') 
 
@section('title', 'Logbook Bimbingan') 
 
@push('styles') 
<style> 
    .card-header-actions { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
    } 
    .status-badge { 
        font-size: 0.75rem; 
        padding: 0.4em 0.8em; 
        border-radius: 20px; 
        font-weight: 600; 
        letter-spacing: 0.5px; 
    } 
    .table-logbook th { 
        background-color: #f8f9fa; 
        font-weight: 600; 
        text-transform: uppercase; 
        font-size: 0.8rem; 
        letter-spacing: 0.5px; 
        color: #6c757d; 
        border-bottom: 2px solid #e9ecef; 
    } 
    .table-logbook td { 
        vertical-align: middle; 
        font-size: 0.9rem; 
    } 
    .date-box { 
        text-align: center; 
        line-height: 1.2; 
    } 
    .date-box .day { 
        font-size: 1.2rem; 
        font-weight: bold; 
        color: #343a40; 
    } 
    .date-box .month { 
        font-size: 0.75rem; 
        color: #adb5bd; 
        text-transform: uppercase; 
    } 
    .feedback-box { 
        background-color: #fff9db; 
        border-left: 3px solid #ffc107; 
        padding: 8px 12px; 
        border-radius: 4px; 
        font-size: 0.85rem; 
        color: #5c5c5c; 
        font-style: italic; 
    } 
    .feedback-box.acc { 
        background-color: #d1e7dd; 
        border-left: 3px solid #198754; 
        color: #0f5132; 
    } 
    .avatar-small { 
        width: 30px; 
        height: 30px; 
        border-radius: 50%; 
        object-fit: cover; 
        margin-right: 5px; 
    } 
</style> 
@endpush 
 
@section('content') 
<div class="container-fluid px-4"> 
     
    <!-- Header Halaman --> 
    <div class="d-flex justify-content-between align-items-center mt-4 
mb-4"> 
        <div> 
            <h1 class="h2 mb-0 fw-bold text-dark">Logbook 
Bimbingan</h1> 
            <p class="text-muted mb-0">Rekam jejak progres skripsi 
Anda</p> 
        </div> 
        <div> 
            <button class="btn btn-outline-dark me-2" 
onclick="window.print()"><i class="fas fa-print me-1"></i> Cetak 
PDF</button> 
            <!-- Tombol Pemicu Modal --> 
            <button class="btn btn-primary shadow-sm" 
data-bs-toggle="modal" data-bs-target="#addLogModal"> 
                <i class="fas fa-plus me-1"></i> Catat Bimbingan 
            </button> 
        </div> 
    </div> 
 
    <!-- Statistik Ringkas --> 
    <div class="row g-3 mb-4"> 
        <div class="col-md-4"> 
            <div class="card border-0 shadow-sm border-start 
border-primary border-4"> 
                <div class="card-body"> 
                    <div class="d-flex align-items-center"> 
                        <div class="flex-grow-1"> 
                            <div class="small fw-bold text-primary 
mb-1">TOTAL BIMBINGAN</div> 
                            <div class="h3 mb-0 fw-bold">12</div> 
                        </div> 
                        <div class="ms-3 text-primary opacity-50"><i 
class="fas fa-book fa-2x"></i></div> 
                    </div> 
                </div> 
            </div> 
        </div> 
        <div class="col-md-4"> 
            <div class="card border-0 shadow-sm border-start 
border-success border-4"> 
                <div class="card-body"> 
                    <div class="d-flex align-items-center"> 
                        <div class="flex-grow-1"> 
                            <div class="small fw-bold text-success 
mb-1">DISETUJUI (ACC)</div> 
                            <div class="h3 mb-0 fw-bold">8</div> 
                        </div> 
                        <div class="ms-3 text-success opacity-50"><i 
class="fas fa-check-circle fa-2x"></i></div> 
                    </div> 
                </div> 
            </div> 
        </div> 
        <div class="col-md-4"> 
            <div class="card border-0 shadow-sm border-start 
border-warning border-4"> 
                <div class="card-body"> 
                    <div class="d-flex align-items-center"> 
                        <div class="flex-grow-1"> 
                            <div class="small fw-bold text-warning 
mb-1">PERLU REVISI</div> 
                            <div class="h3 mb-0 fw-bold">4</div> 
                        </div> 
                        <div class="ms-3 text-warning opacity-50"><i 
class="fas fa-exclamation-circle fa-2x"></i></div> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
 
    <!-- Tabel Logbook --> 
    <div class="card mb-4 shadow border-0 rounded-3"> 
        <div class="card-header bg-white py-3"> 
            <div class="card-header-actions"> 
                <h6 class="m-0 font-weight-bold text-primary"><i 
class="fas fa-history me-2"></i>Riwayat Konsultasi</h6> 
                <!-- Filter Sederhana --> 
                <div class="dropdown"> 
                    <button class="btn btn-sm btn-light 
dropdown-toggle" type="button" id="dropdownMenuButton1" 
data-bs-toggle="dropdown" aria-expanded="false"> 
                        Filter Status 
                    </button> 
                    <ul class="dropdown-menu dropdown-menu-end shadow" 
aria-labelledby="dropdownMenuButton1"> 
                        <li><a class="dropdown-item" 
href="#">Semua</a></li> 
                        <li><a class="dropdown-item" 
href="#">Disetujui</a></li> 
                        <li><a class="dropdown-item" 
href="#">Revisi</a></li> 
                    </ul> 
                </div> 
            </div> 
        </div> 
        <div class="card-body p-0"> 
            <div class="table-responsive"> 
                <table class="table table-hover table-logbook mb-0" 
id="datatablesSimple"> 
                    <thead> 
                        <tr> 
                            <th class="text-center" 
width="80">Tanggal</th> 
                            <th width="25%">Topik Bimbingan</th> 
                            <th width="35%">Catatan Dosen</th> 
                            <th width="15%">Bukti / File</th> 
                            <th class="text-center" 
width="10%">Status</th> 
                            <th class="text-end" width="10%">Aksi</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <!-- Item 1: Revisi --> 
                        <tr> 
                            <td> 
                                <div class="date-box"> 
                                    <div class="day">24</div> 
                                    <div class="month">OKT 25</div> 
                                </div> 
                            </td> 
                            <td> 
                                <span class="badge bg-light text-dark 
border mb-1">BAB 3</span> 
                                <div class="fw-bold 
text-dark">Metodologi Penelitian</div> 
                                <small class="text-muted">Diskusi 
mengenai metode kualitatif dan sampling.</small> 
                            </td> 
                            <td> 
                                <div class="feedback-box"> 
                                    <i class="fas fa-comment-alt 
me-1"></i> 
                                    "Perbaiki diagram alir penelitian, 
tambahkan referensi jurnal 5 tahun terakhir." 
                                    <div class="mt-1 small text-muted 
text-end">- Dr. Prabu</div> 
                                </div> 
                            </td> 
                            <td> 
                                <a href="#" 
class="text-decoration-none"> 
                                    <i class="fas fa-file-pdf 
text-danger me-1"></i> Bab3_Revisi_v1.pdf 
                                </a> 
                            </td> 
                            <td class="text-center"> 
                                <span class="badge bg-warning 
text-dark status-badge">REVISI</span> 
                            </td> 
                            <td class="text-end"> 
                                <div class="btn-group"> 
                                    <button class="btn btn-sm 
btn-outline-secondary" title="Edit"><i class="fas 
fa-edit"></i></button> 
                                    <button class="btn btn-sm 
btn-outline-danger" title="Hapus"><i class="fas 
fa-trash"></i></button> 
                                </div> 
                            </td> 
                        </tr> 
 
                        <!-- Item 2: ACC --> 
                        <tr> 
                            <td> 
                                <div class="date-box"> 
                                    <div class="day">10</div> 
                                    <div class="month">OKT 25</div> 
                                </div> 
                            </td> 
                            <td> 
                                <span class="badge bg-light text-dark 
border mb-1">BAB 2</span> 
                                <div class="fw-bold 
text-dark">Landasan Teori</div> 
                                <small class="text-muted">Finalisasi 
teori pendukung.</small> 
                            </td> 
                            <td> 
                                <div class="feedback-box acc"> 
                                    <i class="fas fa-check me-1"></i> 
                                    "Sudah Bagus. Lanjut ke Bab 
berikutnya." 
                                    <div class="mt-1 small text-muted 
text-end">- Dr. Prabu</div> 
                                </div> 
                            </td> 
                            <td> 
                                <a href="#" 
class="text-decoration-none"> 
                                    <i class="fas fa-file-word 
text-primary me-1"></i> Bab2_Final.docx 
                                </a> 
                            </td> 
                            <td class="text-center"> 
                                <span class="badge bg-success 
status-badge">DISETUJUI</span> 
                            </td> 
                            <td class="text-end"> 
                                <button class="btn btn-sm 
btn-outline-primary" title="Lihat Detail"><i class="fas 
fa-eye"></i></button> 
                            </td> 
                        </tr> 
 
                        <!-- Item 3: Menunggu --> 
                        <tr> 
                            <td> 
                                <div class="date-box"> 
                                    <div class="day">01</div> 
                                    <div class="month">OKT 25</div> 
                                </div> 
                            </td> 
                            <td> 
                                <span class="badge bg-light text-dark 
border mb-1">BAB 1</span> 
                                <div class="fw-bold 
text-dark">Pendahuluan</div> 
                                <small class="text-muted">Pengajuan 
Judul dan Latar Belakang.</small> 
                            </td> 
                            <td> 
                                <span class="text-muted small 
fst-italic">Belum ada catatan dosen...</span> 
                            </td> 
                            <td> 
                                <a href="#" 
class="text-decoration-none"> 
                                    <i class="fas fa-file-pdf 
text-danger me-1"></i> Proposal_Awal.pdf 
                                </a> 
                            </td> 
                            <td class="text-center"> 
                                <span class="badge bg-secondary 
status-badge">MENUNGGU</span> 
                            </td> 
                            <td class="text-end"> 
                                <div class="btn-group"> 
                                    <button class="btn btn-sm 
btn-outline-secondary"><i class="fas fa-edit"></i></button> 
                                </div> 
                            </td> 
                        </tr> 
                    </tbody> 
                </table> 
            </div> 
        </div> 
    </div> 
</div> 
 
<!-- MODAL TAMBAH BIMBINGAN --> 
<div class="modal fade" id="addLogModal" tabindex="-1" 
aria-labelledby="addLogModalLabel" aria-hidden="true"> 
    <div class="modal-dialog modal-lg modal-dialog-centered"> 
        <div class="modal-content border-0 shadow"> 
            <div class="modal-header bg-primary text-white"> 
                <h5 class="modal-title fw-bold" 
id="addLogModalLabel"><i class="fas fa-pen-alt me-2"></i>Catat 
Bimbingan Baru</h5> 
                <button type="button" class="btn-close 
btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button> 
            </div> 
            <form action="#" method="POST" 
enctype="multipart/form-data"> 
                @csrf 
                <div class="modal-body p-4"> 
                    <div class="row g-3"> 
                        <!-- Tanggal & Jam --> 
                        <div class="col-md-6"> 
                            <label for="tanggal" class="form-label 
fw-bold">Tanggal Bimbingan</label> 
                            <input type="date" class="form-control" 
id="tanggal" name="tanggal" required> 
                        </div> 
                        <div class="col-md-6"> 
                            <label for="jam" class="form-label 
fw-bold">Waktu</label> 
                            <input type="time" class="form-control" 
id="jam" name="jam"> 
                        </div> 
 
                        <!-- Topik --> 
                        <div class="col-md-12"> 
                            <label for="bab" class="form-label 
fw-bold">Tahapan Skripsi</label> 
                            <select class="form-select" id="bab" 
name="bab"> 
                                <option selected disabled>Pilih 
Tahapan...</option> 
                                <option value="Judul">Pengajuan 
Judul</option> 
                                <option value="Bab 1">Bab 1 - 
Pendahuluan</option> 
                                <option value="Bab 2">Bab 2 - Landasan 
Teori</option> 
                                <option value="Bab 3">Bab 3 - 
Metodologi</option> 
                                <option value="Bab 4">Bab 4 - Hasil & 
Pembahasan</option> 
                                <option value="Bab 5">Bab 5 - 
Penutup</option> 
                            </select> 
                        </div> 
 
                        <!-- Materi --> 
                        <div class="col-md-12"> 
                            <label for="materi" class="form-label 
fw-bold">Materi / Pokok Bahasan</label> 
                            <input type="text" class="form-control" 
id="materi" name="materi" placeholder="Contoh: Revisi Latar Belakang 
Masalah" required> 
                        </div> 
 
                        <!-- Deskripsi --> 
                        <div class="col-md-12"> 
                            <label for="deskripsi" class="form-label 
fw-bold">Hasil Diskusi / Catatan Pribadi</label> 
                            <textarea class="form-control" 
id="deskripsi" name="deskripsi" rows="4" placeholder="Tuliskan 
poin-poin penting yang dibahas..."></textarea> 
                        </div> 
 
                        <!-- Upload File --> 
                        <div class="col-md-12"> 
                            <label for="file" class="form-label 
fw-bold">Upload File Draft (Opsional)</label> 
                            <input type="file" class="form-control" 
id="file" name="file"> 
                            <div class="form-text">Format: PDF atau 
DOCX. Maksimal 5MB.</div> 
                        </div> 
                    </div> 
                </div> 
                <div class="modal-footer bg-light"> 
                    <button type="button" class="btn btn-secondary" 
data-bs-dismiss="modal">Batal</button> 
                    <button type="submit" class="btn btn-primary 
px-4"><i class="fas fa-save me-1"></i> Simpan Logbook</button> 
                </div> 
            </form> 
        </div> 
    </div> 
</div> 
@endsection 
 
 