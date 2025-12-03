@extends('layouts.admin')

@section('title', 'Edit Mahasiswa')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-dark fw-bold">Edit Data Mahasiswa</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.mahasiswa.index') }}">Data Mahasiswa</a></li>
        <li class="breadcrumb-item active">Edit: {{ $mahasiswa->name }}</li>
    </ol>

    <div class="card mb-4 border-0 shadow">
        <div class="card-body p-4">
            <form action="{{ route('admin.mahasiswa.update', $mahasiswa->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Baris 1: Nama & NIM -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $mahasiswa->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nim" class="form-label fw-bold">NIM</label>
                        <input type="text" class="form-control @error('nim') is-invalid @enderror" id="nim" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required>
                        @error('nim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Baris 2: Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $mahasiswa->email) }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Baris 3: Prodi & Semester -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="prodi" class="form-label fw-bold">Program Studi</label>
                        <input type="text" class="form-control" id="prodi" name="prodi" value="{{ old('prodi', $mahasiswa->prodi) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="semester" class="form-label fw-bold">Semester</label>
                        <input type="number" class="form-control" id="semester" name="semester" value="{{ old('semester', $mahasiswa->semester) }}">
                    </div>
                </div>

                <!-- FITUR UTAMA: PLOTTING PEMBIMBING -->
                <div class="mb-4 p-3 bg-info bg-opacity-10 rounded border border-info">
                    <label for="dosen_pembimbing_id" class="form-label fw-bold text-primary"><i class="fas fa-user-tie me-2"></i>Plotting Dosen Pembimbing</label>
                    <select class="form-select" id="dosen_pembimbing_id" name="dosen_pembimbing_id">
                        <option value="">-- Belum Ditentukan --</option>
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}" {{ old('dosen_pembimbing_id', $mahasiswa->dosen_pembimbing_id) == $dosen->id ? 'selected' : '' }}>
                                {{ $dosen->name }} (NIDN: {{ $dosen->nidn }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-dark">Pilih dosen yang akan membimbing mahasiswa ini.</div>
                </div>

                <div class="alert alert-light border">
                    <small><i class="fas fa-info-circle me-1"></i> Kosongkan kolom password jika tidak ingin mengubahnya.</small>
                </div>

                <!-- Baris 4: Password (Opsional) -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-bold">Password Baru</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection