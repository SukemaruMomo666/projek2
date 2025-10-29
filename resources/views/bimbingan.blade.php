@extends('layouts.admin')

@section('title', 'Logbook Bimbingan')

{{-- Tambahkan CSS khusus jika perlu, misalnya untuk DataTables --}}
@push('styles')
    {{-- <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" /> --}}
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Logbook Bimbingan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Logbook Bimbingan</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Detail Riwayat Bimbingan
        </div>
        <div class="card-body">
            {{-- Tambahkan id="datatablesSimple" jika ingin menggunakan Simple DataTables --}}
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Materi Bimbingan</th>
                        <th>Catatan Dosen</th>
                        <th>Status</th>
                        <th>File Revisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Contoh Data Statis - Nanti diganti dengan loop data dari database --}}
                    <tr>
                        <td>1</td>
                        <td>20 Okt 2025</td>
                        <td>Revisi Metodologi (BAB 3)</td>
                        <td>Perbaiki bagian analisis data, tambahkan referensi X.</td>
                        <td><span class="badge bg-success">Disetujui</span></td>
                        <td><a href="#">BAB3_Revisi_Final.docx</a></td>
                        <td>
                            <button class="btn btn-sm btn-info" title="Lihat Detail"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>
                     <tr>
                        <td>2</td>
                        <td>15 Okt 2025</td>
                        <td>Pengajuan BAB 3</td>
                        <td>Metodologi kurang jelas, perlu diperbaiki alur penelitiannya. Lihat komentar di file.</td>
                        <td><span class="badge bg-warning text-dark">Revisi</span></td>
                        <td><a href="#">BAB3_Pengajuan.docx</a></td>
                         <td>
                            <button class="btn btn-sm btn-info" title="Lihat Detail"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-primary" title="Upload Revisi"><i class="fas fa-upload"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>10 Okt 2025</td>
                        <td>ACC BAB 2</td>
                        <td>Landasan teori sudah cukup baik. Lanjutkan ke BAB 3.</td>
                         <td><span class="badge bg-success">Disetujui</span></td>
                        <td><a href="#">BAB2_Final.docx</a></td>
                         <td>
                             <button class="btn btn-sm btn-info" title="Lihat Detail"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>
                    {{-- Loop data bimbingan dari database akan ada di sini nanti menggunakan @foreach --}}
                    {{-- Contoh: --}}
                    {{-- @foreach ($riwayatBimbingan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->tanggal->format('d M Y') }}</td>
                            <td>{{ $item->materi }}</td>
                            <td>{{ $item->catatan_dosen ?? '-' }}</td>
                            <td>
                                @if($item->status == 'Disetujui')
                                    <span class="badge bg-success">{{ $item->status }}</span>
                                @elseif($item->status == 'Revisi')
                                    <span class="badge bg-warning text-dark">{{ $item->status }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $item->status }}</span>
                                @endif
                            </td>
                             <td>
                                @if($item->file_path)
                                    <a href="{{ Storage::url($item->file_path) }}" target="_blank">Lihat File</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td> ... aksi ... </td>
                        </tr>
                    @endforeach --}}
                </tbody>
            </table>
             <div class="mt-3">
                 {{-- Tambahkan link pagination nanti jika datanya banyak --}}
                 {{-- {{ $riwayatBimbingan->links() }} --}}
             </div>
        </div>
    </div>
</div>
@endsection

{{-- Tambahkan JS khusus jika perlu (misal untuk DataTables) --}}
@push('scripts')
    {{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> --}}
    {{-- <script>
        $(document).ready(function() {
            $('#datatablesSimple').DataTable(); // Sesuaikan dengan ID tabel Anda
        });
    </script> --}}
@endpush
