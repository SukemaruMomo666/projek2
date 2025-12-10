@extends('layouts.admin')

@section('title', 'Dashboard Dosen')

@section('content')
<div class="container-fluid px-4">
    
    <div class="bg-white rounded-3 shadow-sm p-4 mt-4 mb-4 border-start border-primary border-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Selamat Datang, {{ Auth::user()->name }}</h1>
                <p class="text-muted mb-0">NIDN: {{ Auth::user()->nidn ?? '-' }} • Dosen Pembimbing</p>
            </div>
            <div class="d-none d-md-block text-end">
                <span class="text-muted small d-block">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-white-50 small text-uppercase">Mahasiswa</div>
                            <div class="h2 mb-0 fw-bold">{{ $totalMahasiswa }}</div>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-white-50 small text-uppercase">Logbook Pending</div>
                            <div class="h2 mb-0 fw-bold">{{ $menungguReview }}</div>
                        </div>
                        <i class="fas fa-book-reader fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-white-50 small text-uppercase">Jadwal Pending</div>
                            <div class="h2 mb-0 fw-bold">{{ $jadwalPending->count() }}</div>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-white-50 small text-uppercase">Jadwal Hari Ini</div>
                            <div class="h2 mb-0 fw-bold">{{ $jadwalHariIni }}</div>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($jadwalPending->count() > 0)
    <div class="card mb-4 shadow border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark">
                <i class="fas fa-calendar-check me-2 text-warning"></i>Permintaan Jadwal Bimbingan
            </h6>
            <a href="{{ route('dosen.jadwal.index') }}" class="btn btn-sm btn-outline-warning">Kelola Detail</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-uppercase">
                        <tr>
                            <th class="ps-4">Mahasiswa</th>
                            <th>Rencana Waktu</th>
                            <th>Topik</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalPending as $jadwal)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $jadwal->mahasiswa->name }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($jadwal->tanggal_pertemuan)->format('d M Y') }}
                                <span class="badge bg-light text-dark border ms-1">{{ $jadwal->waktu_mulai }} WIB</span>
                            </td>
                            <td>{{ Str::limit($jadwal->topik, 30) }}</td>
                            <td class="text-center">
                                @if($jadwal->status == 'Menunggu')
                                    <span class="badge bg-warning text-dark">Baru</span>
                                @else
                                    <span class="badge bg-info">Reschedule (Menunggu Mhs)</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('dosen.kelola-jadwal', ['aksi' => 'terima']) }}" method="POST" class="d-inline"> 
                                    @csrf 
                                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                    <input type="hidden" name="aksi" value="terima">
                                    <button type="submit" class="btn btn-sm btn-success" title="Terima Langsung" onclick="return confirm('Terima jadwal ini?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <a href="{{ route('dosen.jadwal.index') }}" class="btn btn-sm btn-secondary" title="Atur Ulang / Tolak">
                                    <i class="fas fa-cog"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="card mb-4 shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark">
                <i class="fas fa-book-reader me-2 text-primary"></i>Logbook Perlu Validasi
            </h6>
            <a href="{{ route('dosen.validasi.logbook.index') }}" class="btn btn-sm btn-link text-decoration-none">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Mahasiswa</th>
                            <th>Materi</th>
                            <th>Tanggal</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestValidations as $log)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($log->mahasiswa->name) }}&background=random" class="rounded-circle me-2" width="30">
                                    <div>
                                        <div class="fw-bold small">{{ $log->mahasiswa->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ Str::limit($log->materi, 40) }}</td>
                            <td class="small text-muted">{{ \Carbon\Carbon::parse($log->tanggal_bimbingan)->format('d M Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('dosen.validasi.logbook.index') }}" class="btn btn-sm btn-outline-primary">Review</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted small">
                                Tidak ada logbook baru.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection