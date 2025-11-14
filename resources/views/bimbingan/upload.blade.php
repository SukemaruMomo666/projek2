@extends('layouts.admin')

@section('title', 'Upload Dokumen Revisi')

@push('styles')
<style>
    /* ... (CSS Anda dari file sebelumnya tetap di sini) ... */
    .upload-zone { border: 2px dashed #cbd5e1; border-radius: 15px; background-color: #f8fafc; padding: 3rem 2rem; text-align: center; transition: all 0.3s ease; cursor: pointer; position: relative; }
    .upload-zone:hover, .upload-zone.dragover { border-color: #3b82f6; background-color: #eff6ff; }
    .upload-icon { font-size: 3.5rem; color: #94a3b8; margin-bottom: 1rem; transition: color 0.3s ease; }
    .upload-zone:hover .upload-icon { color: #3b82f6; }
    .file-input-hidden { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .file-card { transition: transform 0.2s; border-left: 4px solid transparent; }
    .file-card:hover { transform: translateX(5px); background-color: #f8f9fa; }
    .file-card.status-Menunggu { border-left-color: #ffc107; }
    .file-card.status-Disetujui { border-left-color: #198754; }
    .file-card.status-Revisi { border-left-color: #dc3545; }
    .file-type-icon { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.2rem; font-weight: bold; }
    .icon-pdf { background-color: #fee2e2; color: #dc2626; }
    .icon-word { background-color: #e0f2fe; color: #0284c7; }
    .version-badge { font-size: 0.7rem; background-color: #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 12px; margin-left: 8px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-0 text-dark">Upload Revisi</h1>
            <p class="text-muted mb-0">Kirimkan dokumen skripsi terbaru Anda di sini.</p>
        </div>
        <div class="d-none d-md-block">
            <span class="badge bg-light text-dark border px-3 py-2">
                <i class="fas fa-info-circle me-1 text-info"></i> Batas Ukuran: 10 MB
            </span>
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
            <strong>Gagal upload!</strong> Harap periksa error berikut:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="row g-4">
        
        <!-- KOLOM KIRI: FORM UPLOAD -->
        <div class="col-lg-5">
            
            <div class="alert alert-primary d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-lightbulb fa-2x me-3"></i>
                <div>
                    <strong>Tips:</strong> Beri nama file dengan format <code>NIM_Nama_BabX_RevisiKe.pdf</code> agar mudah dicek dosen.
                </div>
            </div>

            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-cloud-upload-alt me-2"></i>Formulir Upload</h6>
                </div>
                <div class="card-body p-4">
                    <!-- 2. PERBAIKAN: Arahkan form ke route 'bimbingan.upload.store' -->
                    <form action="{{ route('bimbingan.upload.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Pilihan Tahapan -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Dokumen Untuk</label>
                            <select class="form-select py-2" name="kategori" required>
                                <option value="" selected disabled>Pilih Bab / Tahapan...</option>
                                <option value="Proposal" {{ old('kategori') == 'Proposal' ? 'selected' : '' }}>Proposal Skripsi</option>
                                <option value="Bab 1" {{ old('kategori') == 'Bab 1' ? 'selected' : '' }}>Bab 1 - Pendahuluan</option>
                                <option value="Bab 2" {{ old('kategori') == 'Bab 2' ? 'selected' : '' }}>Bab 2 - Landasan Teori</option>
                                <option value="Bab 3" {{ old('kategori') == 'Bab 3' ? 'selected' : '' }}>Bab 3 - Metodologi</option>
                                <option value="Bab 4" {{ old('kategori') == 'Bab 4' ? 'selected' : '' }}>Bab 4 - Hasil & Pembahasan</option>
                                <option value="Bab 5" {{ old('kategori') == 'Bab 5' ? 'selected' : '' }}>Bab 5 - Penutup</option>
                                <option value="Full Draft" {{ old('kategori') == 'Full Draft' ? 'selected' : '' }}>Full Draft (Lengkap)</option>
                            </select>
                        </div>

                        <!-- Area Upload Drag & Drop -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">File Dokumen</label>
                            <div class="upload-zone" id="dropZone">
                                <input type="file" name="file_skripsi" class="file-input-hidden" id="fileInput" accept=".pdf,.doc,.docx" required>
                                <div class="upload-icon">
                                    <i class="fas fa-file-import"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" id="fileName">Klik atau Tarik File ke Sini</h6>
                                <p class="text-muted small mb-0">Format: PDF atau Word (Docx)</p>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Pesan untuk Dosen</label>
                            <textarea class="form-control" rows="3" placeholder="Contoh: Ini perbaikan Bab 2 sesuai catatan bimbingan tanggal 24 Okt." name="keterangan">{{ old('keterangan') }}</textarea>
                        </div>

                        <!-- Tombol -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Dokumen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: RIWAYAT FILE (DINAMIS) -->
        <div class="col-lg-7">
            <div class="card shadow border-0 rounded-3 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-folder-open me-2 text-warning"></i>Arsip Dokumen Saya</h6>
                    <!-- 3. PERBAIKAN: Hitung total file dinamis -->
                    <div class="badge bg-secondary bg-opacity-10 text-secondary">Total: {{ $dokumens->count() }} File</div>
                </div>
                <div class="card-body p-0">
                    
                    <!-- List Group -->
                    <div class="list-group list-group-flush">
                        
                        <!-- 4. PERBAIKAN: Looping data dari database -->
                        @forelse ($dokumens as $index => $dokumen)
                        <div class="list-group-item p-3 file-card status-{{ $dokumen->status }}">
                            <div class="d-flex align-items-center">
                                <!-- Ikon File (PDF/Word) -->
                                <div class="file-type-icon {{ Str::endsWith($dokumen->nama_file_asli, 'pdf') ? 'icon-pdf' : 'icon-word' }} me-3">
                                    <i class="fas {{ Str::endsWith($dokumen->nama_file_asli, 'pdf') ? 'fa-file-pdf' : 'fa-file-word' }}"></i>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold text-dark">{{ $dokumen->nama_file_asli }}</h6>
                                        <span class="version-badge">V.{{ $dokumens->count() - $index }}</span>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i> {{ $dokumen->created_at->format('d M Y') }} &bull; 
                                        <span class="text-primary">{{ $dokumen->kategori }}</span>
                                    </div>
                                    
                                    <!-- Tampilkan Catatan Mahasiswa -->
                                    @if($dokumen->keterangan)
                                    <div class="mt-2 p-2 bg-light rounded border-start border-primary border-3 small text-muted">
                                        <i class="fas fa-quote-left fa-xs me-1"></i> {{ $dokumen->keterangan }}
                                    </div>
                                    @endif
                                    
                                    <!-- Tampilkan Feedback Dosen -->
                                    @if($dokumen->catatan_dosen)
                                    <div class="mt-2 p-2 rounded border-start border-3 small {{ $dokumen->status == 'Revisi' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }}">
                                        <strong class="d-block mb-1">Catatan Dosen:</strong>
                                        <i class="fas fa-comment me-1"></i> "{{ $dokumen->catatan_dosen }}"
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="text-end ms-3">
                                    <!-- Status -->
                                    @if ($dokumen->status == 'Disetujui')
                                        <span class="badge bg-success mb-2 d-block">Disetujui (ACC)</span>
                                    @elseif ($dokumen->status == 'Revisi')
                                        <span class="badge bg-danger mb-2 d-block">Perlu Revisi</span>
                                    @else
                                        <span class="badge bg-warning text-dark mb-2 d-block">Sedang Direview</span>
                                    @endif
                                    
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank" class="btn btn-light text-secondary" title="Download"><i class="fas fa-download"></i></a>
                                        <!-- Logika Tombol Hapus: Hanya bisa hapus jika status 'Menunggu' -->
                                        @if($dokumen->status == 'Menunggu')
                                        <a href="#" class="btn btn-light text-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @empty
                        <!-- Tampilkan jika tidak ada data -->
                        <div class="text-center p-5">
                            <img src="https://placehold.co/100x100/EBF8FF/3B82F6?text=📂" class="mb-3" style="width: 80px; border-radius: 50%;">
                            <h5 class="text-muted">Belum Ada Dokumen</h5>
                            <p class="text-muted small">Mulai upload file skripsi pertama Anda menggunakan form di sebelah kiri.</p>
                        </div>
                        @endforelse

                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Interaksi Upload (Tetap sama) -->
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