@extends('layouts.admin')

@section('title', 'Kelola Mahasiswa')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1 class="h2 text-dark fw-bold">Kelola Data Mahasiswa</h1>
        <div>
            <!-- Tombol Import Excel -->
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-excel me-1"></i> Import Excel
            </button>
            
            <!-- Tombol Tambah Manual -->
            <a href="{{ route('admin.mahasiswa.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Manual
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4 border-0 shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-graduate me-1"></i> Daftar Mahasiswa</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Prodi</th>
                            <th>Dosen Pembimbing</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mahasiswas as $mhs)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold">{{ $mhs->name }}</div>
                                <small class="text-muted">{{ $mhs->email }}</small>
                            </td>
                            <td>{{ $mhs->nim }}</td>
                            <td>{{ $mhs->prodi ?? '-' }}</td>
                            <td>
                                @if($mhs->dosenPembimbing)
                                    <span class="badge bg-info text-dark">{{ $mhs->dosenPembimbing->name }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Ada</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.mahasiswa.edit', $mhs->id) }}" class="btn btn-sm btn-warning" title="Edit / Plotting Dosen">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.mahasiswa.destroy', $mhs->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus mahasiswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL IMPORT EXCEL -->
<!-- MODAL IMPORT EXCEL -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-excel me-2"></i>Import Data Mahasiswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.mahasiswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    
                    <div class="alert alert-info small mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Petunjuk:</strong>
                        <ol class="ps-3 mb-0">
                            <li>Download template Excel di bawah ini.</li>
                            <li>Isi data mahasiswa sesuai format contoh.</li>
                            <li>Jangan ubah nama kolom header (baris 1).</li>
                            <li>Upload file yang sudah diisi.</li>
                        </ol>
                    </div>

                    <!-- TOMBOL DOWNLOAD TEMPLATE -->
                    <div class="d-grid mb-4">
                        <a href="{{ route('admin.mahasiswa.template') }}" class="btn btn-outline-success">
                            <i class="fas fa-download me-2"></i> Download Template Excel
                        </a>
                    </div>

                    <div class="mb-3">
                        <label for="file_excel" class="form-label fw-bold">Upload File (.xlsx / .csv)</label>
                        <input type="file" class="form-control" id="file_excel" name="file_excel" accept=".xlsx, .xls, .csv" required>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Import Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection