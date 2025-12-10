@extends('layouts.admin')

@section('title', 'Kelola Mahasiswa')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1 class="h2 text-dark fw-bold">Kelola Data Mahasiswa</h1>
        <div>
            <button class="btn btn-warning me-2 text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#batchToolsModal">
                <i class="fas fa-magic me-1"></i> Batch Tools
            </button>

            <button class="btn btn-success me-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-excel me-1"></i> Import Excel
            </button>
            
            <a href="{{ route('admin.mahasiswa.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-1"></i> Tambah Manual
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0"><i class="fas fa-user-graduate me-1"></i> Daftar Mahasiswa</h6>
            
            <form action="{{ route('admin.mahasiswa.index') }}" method="GET" class="d-flex">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama / NIM..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-outline-secondary" title="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>
                                <a href="{{ route('admin.mahasiswa.index', ['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none text-dark">
                                    Nama Mahasiswa
                                    @if(request('sort') == 'name')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort text-muted ms-1" style="opacity: 0.3"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('admin.mahasiswa.index', ['sort' => 'nim', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none text-dark">
                                    NIM
                                    @if(request('sort') == 'nim')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort text-muted ms-1" style="opacity: 0.3"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('admin.mahasiswa.index', ['sort' => 'kelas', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none text-dark">
                                    Kelas
                                    @if(request('sort') == 'kelas')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort text-muted ms-1" style="opacity: 0.3"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Prodi</th>
                            <th>Dosen Pembimbing</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mahasiswas as $mhs)
                        <tr>
                            <td>{{ $loop->iteration + ($mahasiswas->currentPage() - 1) * $mahasiswas->perPage() }}</td>
                            <td>
                                <div class="fw-bold text-primary">{{ $mhs->name }}</div>
                                <small class="text-muted"><i class="far fa-envelope me-1"></i>{{ $mhs->email }}</small>
                            </td>
                            <td class="fw-bold">{{ $mhs->nim }}</td>
                            <td>
                                @if($mhs->kelas)
                                    <span class="badge bg-secondary rounded-pill">{{ $mhs->kelas }}</span>
                                @else
                                    <span class="text-muted small fst-italic">-</span>
                                @endif
                            </td>
                            <td>{{ $mhs->prodi ?? '-' }}</td>
                            <td>
                                @if($mhs->dosenPembimbing)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-info text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:24px; height:24px; font-size:10px;">
                                            {{ substr($mhs->dosenPembimbing->name, 0, 1) }}
                                        </div>
                                        <span>{{ $mhs->dosenPembimbing->name }}</span>
                                    </div>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Ada</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.mahasiswa.edit', $mhs->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.mahasiswa.destroy', $mhs->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus mahasiswa ini? Data tidak bisa dikembalikan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="https://img.icons8.com/ios/100/cccccc/nothing-found.png" alt="No Data" class="mb-3" style="width: 60px; opacity: 0.5;">
                                <p class="text-muted mb-0">Belum ada data mahasiswa atau data tidak ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 d-flex justify-content-end">
                {{ $mahasiswas->links() }} 
                </div>
        </div>
    </div>
</div>

<div class="modal fade" id="batchToolsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-magic me-2"></i>Batch Tools (Otomatisasi)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-primary mb-3">1. Auto Split Kelas (Pola Urut)</h6>
                        <p class="small text-muted mb-3">
                            Membagi kelas secara otomatis berdasarkan urutan NIM menggunakan pola A, B, C... sesuai jumlah kelas.
                        </p>
                        <form action="{{ route('admin.mahasiswa.generate-kelas') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label fw-bold small">Nama Dasar Kelas</label>
                                <input type="text" name="base_kelas" class="form-control form-control-sm" placeholder="Contoh: TRPL 2" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Jumlah Kelas (Pecahan)</label>
                                <input type="number" name="jumlah_kelas" class="form-control form-control-sm" placeholder="Contoh: 3 (Untuk A, B, C)" min="1" required>
                                <div class="form-text small text-muted">Isi <b>1</b> jika hanya ada 1 kelas (Semua jadi A).</div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold small">NIM Mulai</label>
                                    <input type="text" name="nim_start" class="form-control form-control-sm" placeholder="1060..." required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold small">NIM Akhir</label>
                                    <input type="text" name="nim_end" class="form-control form-control-sm" placeholder="1060..." required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-sm">
                                <i class="fas fa-sync me-1"></i> Generate Kelas
                            </button>
                        </form>
                    </div>

                    <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                        <h6 class="fw-bold text-success mb-3">2. Plotting Dosen Massal</h6>
                        <p class="small text-muted mb-3">
                            Pilih kelas yang sudah ada, lalu set satu dosen pembimbing untuk seluruh mahasiswa di kelas tersebut.
                        </p>
                        <form action="{{ route('admin.mahasiswa.bulk-plotting') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Pilih Kelas</label>
                                <select name="kelas_target" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @if(isset($list_kelas))
                                        @foreach($list_kelas as $kls)
                                            <option value="{{ $kls }}">{{ $kls }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Pilih Dosen</label>
                                <select name="dosen_id" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    @if(isset($dosens))
                                        @foreach($dosens as $dosen)
                                            <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100 btn-sm">
                                <i class="fas fa-users-cog me-1"></i> Simpan Plotting
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                    <div class="d-grid mb-4">
                        <a href="{{ route('admin.mahasiswa.template') }}" class="btn btn-outline-success border-2 border-dashed">
                            <i class="fas fa-download me-2"></i> Download Template Excel
                        </a>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload File (.xlsx / .csv)</label>
                        <input type="file" class="form-control" name="file_excel" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection