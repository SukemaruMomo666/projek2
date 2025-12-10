@extends('layouts.admin')

@section('title', 'Logbook Bimbingan')

@push('styles')
<style>
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

{{-- LOGIKA CEK LEVEL MAHASISWA --}}
@php
    $user = Auth::user();
    $prodi = strtolower($user->prodi ?? '');
    $semester = $user->semester ?? 0;
    
    $isSkripsi = false; 
    if (str_contains($prodi, 'sistem informasi') && $semester >= 5) $isSkripsi = true;
    elseif (str_contains($prodi, 'rekayasa perangkat lunak') && $semester >= 7) $isSkripsi = true;
@endphp

<div class="container-fluid px-4">
    
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h2 mb-0 fw-bold text-dark">Logbook Bimbingan</h1>
            <p class="text-muted mb-0">
                @if($isSkripsi) Rekam jejak Skripsi @else Rekam jejak Akademik & Perwalian @endif
            </p>
        </div>
        <div>
            <a href="{{ route('bimbingan.cetak') }}" target="_blank" class="btn btn-outline-dark me-2">
                <i class="fas fa-print me-1"></i> Cetak PDF
            </a>
            
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addLogModal">
                <i class="fas fa-plus me-1"></i> Catat Bimbingan
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            @if(!$isSkripsi)
                <div class="card border-0 shadow-sm border-start border-{{ $jumlahPerwalian >= 3 ? 'success' : 'danger' }} border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="small fw-bold text-{{ $jumlahPerwalian >= 3 ? 'success' : 'danger' }} mb-1">TARGET PERWALIAN</div>
                                <div class="h3 mb-0 fw-bold">{{ $jumlahPerwalian }} <span class="text-muted fs-6">/ 3 Kali</span></div>
                                <small class="text-muted" style="font-size: 0.75rem">
                                    {{ $jumlahPerwalian < 3 ? 'Kurang ' . (3 - $jumlahPerwalian) . ' kali lagi!' : 'Target tercapai!' }}
                                </small>
                            </div>
                            <div class="ms-3 text-{{ $jumlahPerwalian >= 3 ? 'success' : 'danger' }} opacity-50"><i class="fas fa-user-friends fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="small fw-bold text-primary mb-1">TOTAL BIMBINGAN</div>
                                <div class="h3 mb-0 fw-bold">{{ $logbooks->count() }}</div>
                            </div>
                            <div class="ms-3 text-primary opacity-50"><i class="fas fa-book fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-success mb-1">DISETUJUI (ACC)</div>
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
                            <div class="small fw-bold text-warning mb-1">Pembimbing</div>
                            <div class="h6 mb-0 fw-bold text-truncate" style="max-width: 150px;">
                                {{ Auth::user()->dosenPembimbing->name ?? 'Belum Ada' }}
                            </div>
                        </div>
                        <div class="ms-3 text-warning opacity-50"><i class="fas fa-user-tie fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>Riwayat Aktivitas</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-logbook mb-0" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th class="text-center" width="80">Tanggal</th>
                            <th width="25%">Jenis & Topik</th>
                            <th width="35%">Catatan Dosen</th>
                            <th width="15%">Bukti</th>
                            <th class="text-center" width="10%">Status</th>
                            <th class="text-end" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logbooks as $log)
                        <tr>
                            <td>
                                <div class="date-box">
                                    <div class="day">{{ $log->tanggal_bimbingan->format('d') }}</div>
                                    <div class="month">{{ $log->tanggal_bimbingan->format('M y') }}</div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $badgeColor = 'bg-secondary';
                                    $topik = Str::before($log->materi, ':'); 
                                    if(str_contains($topik, 'Perwalian')) $badgeColor = 'bg-primary';
                                    if(str_contains($topik, 'Lomba')) $badgeColor = 'bg-success';
                                    if(str_contains($topik, 'Bab')) $badgeColor = 'bg-warning text-dark';
                                @endphp
                                <span class="badge {{ $badgeColor }} border mb-1">{{ $topik }}</span>
                                <div class="fw-bold text-dark">{{ Str::after($log->materi, ':') }}</div>
                            </td>
                            <td>
                                @if ($log->catatan_dosen)
                                    <div class="feedback-box {{ $log->status == 'Disetujui' ? 'acc' : '' }}">
                                        <i class="fas {{ $log->status == 'Disetujui' ? 'fa-check' : 'fa-comment-alt' }} me-1"></i>
                                        "{{ $log->catatan_dosen }}"
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Menunggu review...</span>
                                @endif
                            </td>
                            <td>
                                @if ($log->file_path)
                                    <a href="{{ Storage::url($log->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100"><i class="fas fa-file-alt me-1"></i> Lihat</a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($log->status == 'Disetujui') <span class="badge bg-success status-badge">ACC</span>
                                @elseif ($log->status == 'Revisi') <span class="badge bg-warning text-dark status-badge">REVISI</span>
                                @else <span class="badge bg-secondary status-badge">PENDING</span> @endif
                            </td>
                            <td class="text-end">
                                
                                {{-- TOMBOL AKSI DINAMIS --}}
                                @if($log->status == 'Menunggu')
                                    <form action="{{ route('bimbingan.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                
                                @elseif($log->status == 'Revisi')
                                    <button class="btn btn-sm btn-warning text-dark fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#editLogModal{{ $log->id }}">
                                        <i class="fas fa-edit me-1"></i> Perbaiki
                                    </button>
                                @endif

                            </td>
                        </tr>

                        @if($log->status == 'Revisi')
                        <div class="modal fade" id="editLogModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit me-2"></i>Perbaiki Logbook</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('bimbingan.update', $log->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-body p-4 text-start">
                                            
                                            <div class="alert alert-warning d-flex align-items-center small py-2 mb-3">
                                                <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                                                <div><strong>Catatan Dosen:</strong> "{{ $log->catatan_dosen }}"</div>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <label class="form-label fw-bold">Tahapan (Tidak diubah)</label>
                                                    <input type="text" class="form-control bg-light" value="{{ Str::before($log->materi, ':') }}" readonly>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-bold">Detail Materi (Silakan Edit)</label>
                                                    <input type="text" class="form-control" name="detail_materi" value="{{ trim(Str::after($log->materi, ':')) }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Tanggal</label>
                                                    <input type="date" class="form-control" name="tanggal_bimbingan" value="{{ $log->tanggal_bimbingan->format('Y-m-d') }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Upload File Baru (Jika perlu)</label>
                                                    <input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png,.docx">
                                                    <div class="form-text small">Kosongkan jika tidak ingin mengganti file.</div>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-bold">Hasil Diskusi / Perbaikan</label>
                                                    <textarea class="form-control" name="catatan_mahasiswa" rows="4" required>{{ $log->catatan_mahasiswa }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning fw-bold px-4">Kirim Perbaikan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr><td colspan="6" class="text-center p-5 text-muted">Belum ada riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-pen-alt me-2"></i>Catat Aktivitas Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('bimbingan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info d-flex align-items-center small py-2 mb-3">
                        <i class="fas fa-info-circle me-2 fs-5"></i>
                        <div>
                            @if(Auth::user()->dosen_pembimbing_id)
                                Bimbingan ini akan diteruskan ke Dosen Pembimbing Skripsi Anda.
                            @else
                                Anda belum memiliki Pembimbing Skripsi. Log ini tercatat sebagai aktivitas akademik/umum.
                            @endif
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Ambil dari Jadwal ACC (Opsional)</label>
                            <select class="form-select bg-light border-primary" id="pilihJadwal" onchange="isiOtomatis(this)">
                                <option value="" selected>-- Input Manual (Tanpa Jadwal) --</option>
                                @if(isset($jadwalDisetujui) && $jadwalDisetujui->count() > 0)
                                    @foreach($jadwalDisetujui as $jadwal)
                                        <option value="{{ $jadwal->id }}" data-tanggal="{{ $jadwal->tanggal_pertemuan->format('Y-m-d') }}" data-topik="{{ $jadwal->topik }}">
                                            [ACC] {{ $jadwal->tanggal_pertemuan->format('d M') }} - {{ Str::limit($jadwal->topik, 40) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <hr>
                        <div class="col-md-5">
                            <label for="tahapan" class="form-label fw-bold">Jenis Kegiatan</label>
                            <select class="form-select" id="tahapan" name="tahapan" required>
                                <option value="" selected disabled>-- Pilih Kegiatan --</option>
                                @if($isSkripsi)
                                    <option value="Pengajuan Judul">Pengajuan Judul</option>
                                    <option value="Bab 1">Bab 1: Pendahuluan</option>
                                    <option value="Bab 2">Bab 2: Landasan Teori</option>
                                    <option value="Bab 3">Bab 3: Metodologi</option>
                                    <option value="Bab 4">Bab 4: Hasil & Pembahasan</option>
                                    <option value="Bab 5">Bab 5: Penutup</option>
                                    <option value="Revisi">Revisi Umum</option>
                                @else
                                    <option value="Perwalian Ke-1">Perwalian Ke-1 (Awal)</option>
                                    <option value="Perwalian Ke-2">Perwalian Ke-2 (Tengah)</option>
                                    <option value="Perwalian Ke-3">Perwalian Ke-3 (Akhir)</option>
                                    <option value="Bimbingan Lomba">Bimbingan Lomba</option>
                                    <option value="Bimbingan Umum">Bimbingan Akademik</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label for="detail_materi" class="form-label fw-bold">Topik Pembahasan</label>
                            <input type="text" class="form-control" id="detail_materi" name="detail_materi" placeholder="Contoh: Diskusi nilai / Revisi Bab 1..." required>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_bimbingan" class="form-label fw-bold">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal_bimbingan" name="tanggal_bimbingan" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="file" class="form-label fw-bold">Bukti Foto / Dokumen</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.docx">
                        </div>
                        <div class="col-md-12">
                            <label for="catatan_mahasiswa" class="form-label fw-bold">Hasil Diskusi</label>
                            <textarea class="form-control" id="catatan_mahasiswa" name="catatan_mahasiswa" rows="4" placeholder="Tuliskan poin penting arahan dosen..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function isiOtomatis(selectObject) {
        var selectedOption = selectObject.options[selectObject.selectedIndex];
        var tanggal = selectedOption.getAttribute('data-tanggal');
        var topik = selectedOption.getAttribute('data-topik');
        if (tanggal && topik) {
            document.getElementById('tanggal_bimbingan').value = tanggal;
            document.getElementById('detail_materi').value = topik;
            var tahapan = document.getElementById('tahapan');
            if(tahapan.value == "") tahapan.selectedIndex = 1; 
        }
    }
</script>
@endsection