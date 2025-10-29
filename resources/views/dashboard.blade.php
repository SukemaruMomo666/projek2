@extends('layouts.admin')

@section('title', 'Dashboard Mahasiswa')

{{-- Tambahkan CSS khusus jika perlu --}}
@push('styles')
{{-- <link href="path/to/your/custom/dashboard.css" rel="stylesheet"> --}}
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard Mahasiswa</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <!-- Kotak Sambutan -->
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">Selamat Datang, {{ Auth::user()->name }}!</h4>
        <p>Ini adalah pusat kendali Anda untuk mengelola proses bimbingan.</p>
    </div>

    <!-- Baris untuk Kartu Info -->
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Dosen Pembimbing</h5>
                    <p class="card-text fs-4">Dr. Prabu, M.Kom.</p> {{-- Ganti dengan data dinamis nanti --}}
                    <span class="small">NIDN: 0412345678</span> {{-- Ganti dengan data dinamis nanti --}}
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Lihat Profil</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-success text-white mb-4">
                 <div class="card-body">
                    <h5 class="card-title">Status Skripsi</h5>
                    <p class="card-text fs-4">BAB 3: Disetujui</p> {{-- Ganti dengan data dinamis nanti --}}
                    <span class="small">Update: 2 hari lalu</span> {{-- Ganti dengan data dinamis nanti --}}
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Bimbingan Berikutnya</h5>
                    <p class="card-text fs-4">28 Okt 2025</p> {{-- Ganti dengan data dinamis nanti --}}
                    <span class="small">Jam 10:00 - Online</span> {{-- Ganti dengan data dinamis nanti --}}
                </div>
                 <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Lihat Jadwal</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris untuk Tabel Riwayat atau Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-book-open me-1"></i>
                    Riwayat Bimbingan (Logbook)
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped"> {{-- Ganti dengan datatables jika perlu --}}
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Materi Bimbingan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Contoh Data 1 -->
                            <tr>
                                <td>20 Okt 2025</td>
                                <td>Revisi Metodologi (BAB 3)</td>
                                <td><span class="badge bg-success">Disetujui</span></td>
                            </tr>
                            <!-- Contoh Data 2 -->
                            <tr>
                                <td>15 Okt 2025</td>
                                <td>Pengajuan BAB 3</td>
                                <td><span class="badge bg-warning text-dark">Revisi</span></td>
                            </tr>
                            <!-- Contoh Data 3 -->
                            <tr>
                                <td>10 Okt 2025</td>
                                <td>ACC BAB 2</td>
                                <td><span class="badge bg-success">Disetujui</span></td>
                            </tr>
                             {{-- Loop data dinamis di sini nanti --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
             <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-upload me-1"></i>
                    Ajukan Bimbingan Baru
                </div>
                <div class="card-body">
                     <form action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="judul_bimbingan" class="form-label">Judul / Materi</label>
                            <input type="text" name="judul_bimbingan" id="judul_bimbingan" class="form-control" placeholder="Contoh: Pengajuan BAB 4">
                        </div>
                        <div class="mb-3">
                            <label for="file_bimbingan" class="form-label">Upload File (PDF, DOCX)</label>
                            <input class="form-control" type="file" name="file_bimbingan" id="file_bimbingan">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            Kirim Pengajuan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- Tambahkan JS khusus jika perlu (misal untuk Chart.js atau DataTables) --}}
@push('scripts')
{{-- Contoh jika pakai Chart.js atau DataTables --}}
{{--
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('assets/demo/chart-area-demo.js') }}"></script>
<script src="{{ asset('assets/demo/chart-bar-demo.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
--}}
@endpush
