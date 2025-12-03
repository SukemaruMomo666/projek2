@extends('layouts.admin')

@section('title', 'Kelola Dosen')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1 class="h2 text-dark fw-bold">Kelola Data Dosen</h1>
        <!-- Tombol Pemicu Modal -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDosenModal">
            <i class="fas fa-plus me-1"></i> Tambah Dosen
        </button>
    </div>

    <!-- Alert Sukses -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Alert Error Validasi (Penting untuk Modal) -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> Gagal menyimpan data! Silakan cek input Anda.
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4 border-0 shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-table me-1"></i> Daftar Dosen Aktif</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Dosen</th>
                            <th>NIDN</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dosens as $dosen)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold">{{ $dosen->name }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $dosen->nidn }}</span></td>
                            <td>{{ $dosen->email }}</td>
                            <td>
                                <div class="btn-group">
                                    <!-- Edit masih pakai halaman terpisah untuk sekarang -->
                                    <a href="{{ route('admin.dosen.edit', $dosen->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.dosen.destroy', $dosen->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus dosen ini?')">
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

<!-- MODAL TAMBAH DOSEN -->
<div class="modal fade" id="addDosenModal" tabindex="-1" aria-labelledby="addDosenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="addDosenModalLabel"><i class="fas fa-user-plus me-2"></i>Tambah Dosen Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.dosen.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    
                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold small text-muted">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="Cth: Dr. Budi Santoso, M.Kom.">
                    </div>

                    <!-- NIDN -->
                    <div class="mb-3">
                        <label for="nidn" class="form-label fw-bold small text-muted">NIDN</label>
                        <input type="text" class="form-control" id="nidn" name="nidn" value="{{ old('nidn') }}" required placeholder="Nomor Induk Dosen Nasional">
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small text-muted">Email Kampus</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="dosen@polsub.ac.id">
                    </div>

                    <!-- Password -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-bold small text-muted">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label fw-bold small text-muted">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Script kecil: Jika ada error validasi (misal email duplikat),
    // otomatis buka kembali modalnya agar user tau salahnya dimana.
    @if ($errors->any())
        var addDosenModal = new bootstrap.Modal(document.getElementById('addDosenModal'));
        addDosenModal.show();
    @endif
</script>
@endpush
@endsection