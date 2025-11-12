@extends('layouts.admin') 
 
@section('title', 'Upload Dokumen Revisi') 
 
@push('styles') 
<style> 
    /* Styling Area Upload Drag & Drop */ 
    .upload-zone { 
        border: 2px dashed #cbd5e1; 
        border-radius: 15px; 
        background-color: #f8fafc; 
        padding: 3rem 2rem; 
        text-align: center; 
        transition: all 0.3s ease; 
        cursor: pointer; 
        position: relative; 
    } 
    .upload-zone:hover, .upload-zone.dragover { 
        border-color: #3b82f6; 
        background-color: #eff6ff; 
    } 
    .upload-icon { 
        font-size: 3.5rem; 
        color: #94a3b8; 
        margin-bottom: 1rem; 
        transition: color 0.3s ease; 
    } 
    .upload-zone:hover .upload-icon { 
        color: #3b82f6; 
    } 
     
    /* Styling Input File Asli (Disembunyikan) */ 
    .file-input-hidden { 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        opacity: 0; 
        cursor: pointer; 
    } 
 
    /* Kartu File di List */ 
    .file-card { 
        transition: transform 0.2s; 
        border-left: 4px solid transparent; 
    } 
    .file-card:hover { 
        transform: translateX(5px); 
        background-color: #f8f9fa; 
    } 
    .file-card.status-pending { border-left-color: #ffc107; } 
    .file-card.status-acc { border-left-color: #198754; } 
    .file-card.status-reject { border-left-color: #dc3545; } 
 
    /* Ikon Tipe File */ 
    .file-type-icon { 
        width: 45px; 
        height: 45px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        border-radius: 10px; 
        font-size: 1.2rem; 
        font-weight: bold; 
    } 
    .icon-pdf { background-color: #fee2e2; color: #dc2626; } 
    .icon-word { background-color: #e0f2fe; color: #0284c7; } 
     
    .version-badge { 
        font-size: 0.7rem; 
        background-color: #e2e8f0; 
        color: #475569; 
        padding: 2px 8px; 
        border-radius: 12px; 
        margin-left: 8px; 
    } 
</style> 
@endpush 
 
@section('content') 
<div class="container-fluid px-4 pb-5"> 
     
    <!-- Header --> 
    <div class="d-flex justify-content-between align-items-center mt-4 
mb-4"> 
        <div> 
            <h1 class="h2 fw-bold mb-0 text-dark">Upload Revisi</h1> 
            <p class="text-muted mb-0">Kirimkan dokumen skripsi 
terbaru Anda di sini.</p> 
        </div> 
        <div class="d-none d-md-block"> 
            <span class="badge bg-light text-dark border px-3 py-2"> 
                <i class="fas fa-info-circle me-1 text-info"></i> 
Batas Ukuran: 10 MB 
            </span> 
        </div> 
    </div> 
 
    <div class="row g-4"> 
         
        <!-- KOLOM KIRI: FORM UPLOAD --> 
        <div class="col-lg-5"> 
             
            <!-- Tips --> 
            <div class="alert alert-primary d-flex align-items-center 
border-0 shadow-sm mb-4" role="alert"> 
                <i class="fas fa-lightbulb fa-2x me-3"></i> 
                <div> 
                    <strong>Tips:</strong> Beri nama file dengan 
format <code>NIM_Nama_BabX_RevisiKe.pdf</code> agar mudah dicek dosen. 
                </div> 
            </div> 
 
            <div class="card shadow border-0 rounded-3"> 
                <div class="card-header bg-white py-3"> 
                    <h6 class="m-0 fw-bold text-primary"><i class="fas 
fa-cloud-upload-alt me-2"></i>Formulir Upload</h6> 
                </div> 
                <div class="card-body p-4"> 
                    <form action="#" method="POST" 
enctype="multipart/form-data"> 
                        @csrf 
                         
                        <!-- Pilihan Tahapan --> 
                        <div class="mb-3"> 
                            <label class="form-label fw-bold small 
text-uppercase text-muted">Dokumen Untuk</label> 
                            <select class="form-select py-2" 
name="kategori" required> 
                                <option selected disabled>Pilih Bab / 
Tahapan...</option> 
                                <option value="Proposal">Proposal 
Skripsi</option> 
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
                                <option value="Full Draft">Full Draft 
(Lengkap)</option> 
                            </select> 
                        </div> 
 
                        <!-- Area Upload Drag & Drop --> 
                        <div class="mb-3"> 
                            <label class="form-label fw-bold small 
text-uppercase text-muted">File Dokumen</label> 
                            <div class="upload-zone" id="dropZone"> 
                                <input type="file" name="file_skripsi" 
class="file-input-hidden" id="fileInput" accept=".pdf,.doc,.docx" 
required> 
                                <div class="upload-icon"> 
                                    <i class="fas fa-file-import"></i> 
                                </div> 
                                <h6 class="fw-bold text-dark mb-1" 
id="fileName">Klik atau Tarik File ke Sini</h6> 
                                <p class="text-muted small 
mb-0">Format: PDF atau Word (Docx)</p> 
                            </div> 
                        </div> 
 
                        <!-- Catatan --> 
                        <div class="mb-4"> 
                            <label class="form-label fw-bold small 
text-uppercase text-muted">Pesan untuk Dosen</label> 
                            <textarea class="form-control" rows="3" 
placeholder="Contoh: Ini perbaikan Bab 2 sesuai catatan bimbingan 
tanggal 24 Okt." name="keterangan"></textarea> 
                        </div> 
 
                        <!-- Tombol --> 
                        <div class="d-grid"> 
                            <button type="submit" class="btn 
btn-primary py-2 fw-bold shadow-sm"> 
                                <i class="fas fa-paper-plane 
me-2"></i> Kirim Dokumen 
                            </button> 
                        </div> 
                    </form> 
                </div> 
            </div> 
        </div> 
 
        <!-- KOLOM KANAN: RIWAYAT FILE --> 
        <div class="col-lg-7"> 
            <div class="card shadow border-0 rounded-3 h-100"> 
                <div class="card-header bg-white py-3 d-flex 
justify-content-between align-items-center"> 
                    <h6 class="m-0 fw-bold text-dark"><i class="fas 
fa-folder-open me-2 text-warning"></i>Arsip Dokumen Saya</h6> 
                    <div class="badge bg-secondary bg-opacity-10 
text-secondary">Total: 5 File</div> 
                </div> 
                <div class="card-body p-0"> 
                     
                    <!-- List Group --> 
                    <div class="list-group list-group-flush"> 
                         
                        <!-- Item 1 (PDF - Pending) --> 
                        <div class="list-group-item p-3 file-card 
status-pending"> 
                            <div class="d-flex align-items-center"> 
                                <div class="file-type-icon icon-pdf 
me-3"> 
                                    <i class="fas fa-file-pdf"></i> 
                                </div> 
                                <div class="flex-grow-1"> 
                                    <div class="d-flex 
align-items-center mb-1"> 
                                        <h6 class="mb-0 fw-bold 
text-dark">10602042_Bab3_Revisi2.pdf</h6> 
                                        <span 
class="version-badge">V.2</span> 
                                    </div> 
                                    <div class="small text-muted"> 
                                        <i class="fas fa-calendar-alt 
me-1"></i> 24 Okt 2025 •  
                                        <span class="text-primary">Bab 
3 - Metodologi</span> 
                                    </div> 
                                </div> 
                                <div class="text-end"> 
                                    <span class="badge bg-warning 
text-dark mb-2 d-block">Sedang Direview</span> 
                                    <div class="btn-group 
btn-group-sm"> 
                                        <a href="#" class="btn 
btn-light text-secondary" title="Download"><i class="fas 
fa-download"></i></a> 
                                        <a href="#" class="btn 
btn-light text-danger" title="Hapus"><i class="fas fa-trash"></i></a> 
                                    </div> 
                                </div> 
                            </div> 
                        </div> 
 
                        <!-- Item 2 (Word - ACC) --> 
                        <div class="list-group-item p-3 file-card 
status-acc"> 
                            <div class="d-flex align-items-center"> 
                                <div class="file-type-icon icon-word 
me-3"> 
                                    <i class="fas fa-file-word"></i> 
                                </div> 
                                <div class="flex-grow-1"> 
                                    <div class="d-flex 
align-items-center mb-1"> 
                                        <h6 class="mb-0 fw-bold 
text-dark">10602042_Bab2_Final.docx</h6> 
                                        <span 
class="version-badge">Final</span> 
                                    </div> 
                                    <div class="small text-muted"> 
                                        <i class="fas fa-calendar-alt 
me-1"></i> 10 Okt 2025 •  
                                        <span class="text-primary">Bab 
2 - Landasan Teori</span> 
                                    </div> 
                                </div> 
                                <div class="text-end"> 
                                    <span class="badge bg-success mb-2 
d-block">Disetujui (ACC)</span> 
                                    <div class="btn-group 
btn-group-sm"> 
                                        <a href="#" class="btn 
btn-light text-secondary"><i class="fas fa-download"></i></a> 
                                    </div> 
                                </div> 
                            </div> 
                        </div> 
 
                        <!-- Item 3 (PDF - Revisi) --> 
                        <div class="list-group-item p-3 file-card 
status-reject"> 
                            <div class="d-flex align-items-center"> 
                                <div class="file-type-icon icon-pdf 
me-3"> 
                                    <i class="fas fa-file-pdf"></i> 
                                </div> 
                                <div class="flex-grow-1"> 
                                    <div class="d-flex 
align-items-center mb-1"> 
                                        <h6 class="mb-0 fw-bold 
text-dark">10602042_Bab3_Draft1.pdf</h6> 
                                        <span 
class="version-badge">V.1</span> 
                                    </div> 
                                    <div class="small text-muted"> 
                                        <i class="fas fa-calendar-alt 
me-1"></i> 01 Okt 2025 •  
                                        <span class="text-primary">Bab 
3 - Metodologi</span> 
                                    </div> 
                                    <!-- Feedback --> 
                                    <div class="mt-2 p-2 bg-light 
rounded border-start border-danger border-3 small text-danger"> 
                                        <i class="fas fa-comment 
me-1"></i> "Format penulisan masih salah." 
                                    </div> 
                                </div> 
                                <div class="text-end"> 
                                    <span class="badge bg-danger mb-2 
d-block">Perlu Revisi</span> 
                                    <div class="btn-group 
btn-group-sm"> 
                                        <a href="#" class="btn 
btn-light text-secondary"><i class="fas fa-download"></i></a> 
                                    </div> 
                                </div> 
                            </div> 
                        </div> 
 
                    </div> 
                     
                    <!-- Footer Card --> 
                    <div class="card-footer bg-light text-center p-3"> 
                        <a href="#" class="small text-decoration-none 
fw-bold">Lihat Semua Arsip <i class="fas fa-arrow-right ms-1"></i></a> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
</div> 
 
<!-- Script Interaksi Upload --> 
<script> 
    const fileInput = document.getElementById('fileInput'); 
    const dropZone = document.getElementById('dropZone'); 
    const fileNameDisplay = document.getElementById('fileName'); 
 
    // Ubah nama file saat dipilih 
    fileInput.addEventListener('change', function() { 
        if (this.files && this.files[0]) { 
            fileNameDisplay.textContent = this.files[0].name; 
            fileNameDisplay.classList.add('text-primary'); 
            dropZone.style.borderColor = '#3b82f6'; 
            dropZone.style.backgroundColor = '#eff6ff'; 
        } 
    }); 
 
    // Efek Dragover 
    dropZone.addEventListener('dragover', (e) => { 
        e.preventDefault(); 
        dropZone.classList.add('dragover'); 
    }); 
 
    dropZone.addEventListener('dragleave', () => { 
        dropZone.classList.remove('dragover'); 
    }); 
 
    dropZone.addEventListener('drop', (e) => { 
        e.preventDefault(); 
        dropZone.classList.remove('dragover'); 
        fileInput.files = e.dataTransfer.files; 
        const event = new Event('change'); 
        fileInput.dispatchEvent(event); 
    }); 
</script> 
@endsection 
 
 