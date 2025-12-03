@extends('layouts.admin')

@section('title', 'Kelola Dosen')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1 class="h2 text-dark fw-bold">Kelola Data Dosen</h1>
        <a href="{{ route('admin.dosen.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Dosen
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4 border-0 shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-table me-1"></i> Daftar Dosen Aktif</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="datatablesSimple">
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
                            <td>{{ $dosen->name }}</td>
                            <td>{{ $dosen->nidn }}</td>
                            <td>{{ $dosen->email }}</td>
                            <td>
                                <a href="{{ route('admin.dosen.edit', $dosen->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.dosen.destroy', $dosen->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus dosen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection