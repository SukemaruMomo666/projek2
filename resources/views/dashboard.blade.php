@extends('layouts.admin') 
 
@section('title', 'Dashboard Mahasiswa') 
 
@push('styles') 
<style> 
    /* Kustomisasi Elegan untuk Dashboard */ 
    .welcome-card { 
        background: linear-gradient(45deg, #2937f0, #9f1ae2); 
        color: white; 
        border: none; 
        border-radius: 15px; 
    } 
     
    .stat-card { 
        border: none; 
        border-radius: 15px; 
        transition: transform 0.3s ease, box-shadow 0.3s ease; 
        overflow: hidden; 
    } 
     
    .stat-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 20px rgba(0,0,0,0.1); 
    } 
 
    .stat-icon { 
        opacity: 0.3; 
        font-size: 3rem; 
        position: absolute; 
        right: 20px; 
        top: 20px; 
    } 
 
    .card-header-custom { 
        background-color: white; 
        border-bottom: 1px solid #f0f0f0; 
        font-weight: 600; 
        padding: 1.2rem; 
        border-radius: 15px 15px 0 0 !important; 
    } 
 
    .avatar-circle { 
        width: 60px; 
        height: 60px; 
        background-color: #e9ecef; 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 1.5rem; 
        color: #495057; 
    } 
 
    /* Progress Step Sederhana */ 
    .step-progress { 
        display: flex; 
        justify-content: space-between; 
        margin: 20px 0; 
        position: relative; 
    } 
    .step-item { 
        text-align: center; 
        position: relative; 
        z-index: 1; 
        width: 100%; 
    } 
    .step-circle { 
        width: 35px; 
        height: 35px; 
        background: #e9ecef; 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        margin: 0 auto 10px; 
        color: #6c757d; 
        font-weight: bold; 
        transition: all 0.3s; 
    } 
    .step-item.active .step-circle { 
        background: #198754; 
        color: white; 
        box-shadow: 0 0 0 5px rgba(25, 135, 84, 0.2); 
    } 
    .step-item.completed .step-circle { 
        background: #198754; 
        color: white; 
    } 
    .step-label { 
        font-size: 0.8rem; 
        color: #6c757d; 
        font-weight: 600; 
    } 
    .progress-line { 
        position: absolute; 
        top: 17px; 
        left: 0; 
        width: 100%; 
        height: 2px; 
        background: #e9ecef; 
        z-index: 0; 
    } 
    .progress-line-fill { 
        height: 100%; 
        background: #198754; 
        width: 40%; /* Sesuaikan dengan progres dinamis */ 
        transition: width 0.5s ease; 
    } 
</style> 
@endpush 
 
@section('content') 
<div class="container-fluid px-4 pb-4"> 
     
    <!-- 1. Hero Section: Sambutan --> 
    <div class="row mt-4"> 
        <div class="col-12"> 
            <div class="card welcome-card shadow-sm mb-4"> 
                <div class="card-body p-4 d-flex align-items-center 
justify-content-between"> 
                    <div> 
                        <h2 class="fw-bold mb-1">Halo, {{ 
Auth::user()->name }}! 
 </h2> 
                        <p class="mb-0 op-8">Selamat datang di Sistem 
Bimbingan JTIK. Terus semangat mengejar mimpimu!!!</p> 
                    </div> 
                    <div class="d-none d-md-block text-end"> 
                        <h5 class="mb-0">{{ 
\Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</h5> 
                        <small>Semester Ganjil 2025/2026</small> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
 
    <!-- 2. Statistik Ringkas (4 Kolom) --> 
    <div class="row g-4 mb-4"> 
        <!-- Status Skripsi --> 
        <div class="col-xl-3 col-md-6"> 
            <div class="card stat-card bg-primary text-white h-100"> 
                <div class="card-body"> 
                    <div class="stat-icon"><i class="fas 
fa-book"></i></div> 
                    <h6 class="text-uppercase mb-2 opacity-75">Status 
Skripsi</h6> 
                    <h3 class="fw-bold">BAB 3</h3> 
                    <div class="progress mt-3" style="height: 5px; 
background-color: rgba(255,255,255,0.3);"> 
                        <div class="progress-bar bg-white" 
role="progressbar" style="width: 60%" aria-valuenow="60" 
aria-valuemin="0" aria-valuemax="100"></div> 
                    </div> 
                    <small class="d-block mt-2">Progres 60%</small> 
                </div> 
            </div> 
        </div> 
 
        <!-- Total Bimbingan --> 
        <div class="col-xl-3 col-md-6"> 
            <div class="card stat-card bg-success text-white h-100"> 
                <div class="card-body"> 
                    <div class="stat-icon"><i class="fas 
fa-comments"></i></div> 
                    <h6 class="text-uppercase mb-2 opacity-75">Total 
Bimbingan</h6> 
                    <h3 class="fw-bold">12 Kali</h3> 
                    <small class="d-block mt-4"><i class="fas 
fa-arrow-up me-1"></i> 2 kali bulan ini</small> 
                </div> 
            </div> 
        </div> 
 
        <!-- Menunggu Revisi --> 
        <div class="col-xl-3 col-md-6"> 
            <div class="card stat-card bg-warning text-white h-100"> 
                <div class="card-body"> 
                    <div class="stat-icon"><i class="fas 
fa-clock"></i></div> 
                    <h6 class="text-uppercase mb-2 opacity-75">Status 
Terkini</h6> 
                    <h3 class="fw-bold">Revisi</h3> 
                    <small class="d-block mt-4">Menunggu konfirmasi 
dosen</small> 
                </div> 
            </div> 
        </div> 
 
        <!-- Jadwal Berikutnya --> 
        <div class="col-xl-3 col-md-6"> 
            <div class="card stat-card bg-danger text-white h-100"> 
                <div class="card-body"> 
                    <div class="stat-icon"><i class="fas 
fa-calendar-check"></i></div> 
                    <h6 class="text-uppercase mb-2 opacity-75">Jadwal 
Sidang</h6> 
                    <h3 class="fw-bold">--/--</h3> 
                    <small class="d-block mt-4">Belum 
dijadwalkan</small> 
                </div> 
            </div> 
        </div> 
    </div> 
 
    <!-- 3. Layout Utama: Progress & Logbook (Kiri), Info Dosen 
(Kanan) --> 
    <div class="row g-4"> 
         
        <!-- Kolom Kiri (8 Grid) --> 
        <div class="col-lg-8"> 
             
            <!-- Progress Tracker --> 
            <div class="card shadow-sm border-0 mb-4 rounded-3"> 
                <div class="card-header card-header-custom"> 
                    <i class="fas fa-tasks me-2 text-primary"></i> 
Tahapan Skripsi 
                </div> 
                <div class="card-body"> 
                    <div class="position-relative"> 
                        <div class="progress-line"> 
                            <div class="progress-line-fill"></div> 
                        </div> 
                        <div class="step-progress"> 
                            <div class="step-item completed"> 
                                <div class="step-circle"><i class="fas 
fa-check"></i></div> 
                                <div class="step-label">Judul</div> 
                            </div> 
                            <div class="step-item completed"> 
                                <div class="step-circle"><i class="fas 
fa-check"></i></div> 
                                <div class="step-label">Bab 1</div> 
                            </div> 
                            <div class="step-item active"> 
                                <div class="step-circle">3</div> 
                                <div class="step-label">Bab 2-3</div> 
                            </div> 
                            <div class="step-item"> 
                                <div class="step-circle">4</div> 
                                <div class="step-label">Bab 4-5</div> 
                            </div> 
                            <div class="step-item"> 
                                <div class="step-circle">5</div> 
                                <div class="step-label">Sidang</div> 
                            </div> 
                        </div> 
                    </div> 
                </div> 
            </div> 
 
            <!-- Tabel Logbook Terakhir --> 
            <div class="card shadow-sm border-0 rounded-3"> 
                <div class="card-header card-header-custom d-flex 
justify-content-between align-items-center"> 
                    <span><i class="fas fa-history me-2 
text-primary"></i> Aktivitas Bimbingan Terakhir</span> 
                    <a href="{{ route('logbook') }}" 
class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a> 
                </div> 
                <div class="card-body p-0"> 
                    <div class="table-responsive"> 
                        <table class="table table-hover align-middle 
mb-0"> 
                            <thead class="bg-light"> 
                                <tr> 
                                    <th class="ps-4">Tanggal</th> 
                                    <th>Materi</th> 
                                    <th>Status</th> 
                                    <th class="text-end 
pe-4">Aksi</th> 
                                </tr> 
                            </thead> 
                            <tbody> 
                                <!-- Data Dummy --> 
                                <tr> 
                                    <td class="ps-4">24 Okt 2025</td> 
                                    <td><span class="fw-bold">Revisi 
Bab 3</span><br><small class="text-muted">Metodologi 
Penelitian</small></td> 
                                    <td><span class="badge bg-warning 
text-dark rounded-pill px-3">Menunggu</span></td> 
                                    <td class="text-end pe-4"><button 
class="btn btn-sm btn-light text-primary"><i class="fas 
fa-eye"></i></button></td> 
                                </tr> 
                                <tr> 
                                    <td class="ps-4">10 Okt 2025</td> 
                                    <td><span class="fw-bold">ACC Bab 
2</span><br><small class="text-muted">Landasan Teori</small></td> 
                                    <td><span class="badge bg-success 
rounded-pill px-3">Disetujui</span></td> 
                                    <td class="text-end pe-4"><button 
class="btn btn-sm btn-light text-primary"><i class="fas 
fa-eye"></i></button></td> 
                                </tr> 
                                <tr> 
                                    <td class="ps-4">01 Okt 2025</td> 
                                    <td><span 
class="fw-bold">Bimbingan Bab 2</span><br><small 
class="text-muted">Diskusi Teori Dasar</small></td> 
                                    <td><span class="badge 
bg-secondary rounded-pill px-3">Selesai</span></td> 
                                    <td class="text-end pe-4"><button 
class="btn btn-sm btn-light text-primary"><i class="fas 
fa-eye"></i></button></td> 
                                </tr> 
                            </tbody> 
                        </table> 
                    </div> 
                </div> 
            </div> 
        </div> 
 
        <!-- Kolom Kanan (4 Grid) --> 
        <div class="col-lg-4"> 
             
            <!-- Kartu Dosen Pembimbing --> 
            <div class="card shadow-sm border-0 mb-4 rounded-3"> 
                <div class="card-header card-header-custom"> 
                    <i class="fas fa-chalkboard-teacher me-2 
text-primary"></i> Dosen Pembimbing 
                </div> 
                <div class="card-body text-center p-4"> 
                    <div class="avatar-circle mx-auto mb-3 bg-primary 
text-white"> 
                        <i class="fas fa-user-tie"></i> 
                        <!-- Jika ada foto: <img src="..." 
class="rounded-circle" width="60"> --> 
                    </div> 
                    <h5 class="fw-bold mb-1">Dr. Prabu, M.Kom.</h5> 
                    <p class="text-muted mb-3">NIDN: 0412345678</p> 
                     
                    <div class="d-grid gap-2"> 
                        <button class="btn btn-outline-primary btn-sm 
rounded-pill"><i class="fab fa-whatsapp me-2"></i>Hubungi via 
WA</button> 
                        <button class="btn btn-outline-dark btn-sm 
rounded-pill"><i class="fas fa-envelope me-2"></i>Kirim Email</button> 
                    </div> 
                </div> 
            </div> 
 
            <!-- Shortcut Menu (Quick Actions) --> 
            <div class="card shadow-sm border-0 rounded-3"> 
                <div class="card-header card-header-custom"> 
                    <i class="fas fa-bolt me-2 text-primary"></i> Aksi 
Cepat 
                </div> 
                <div class="card-body"> 
                    <div class="d-grid gap-3"> 
                        <a href="#" class="btn btn-light text-start 
border hover-shadow"> 
                            <div class="d-flex align-items-center"> 
                                <div class="bg-primary text-white 
rounded p-2 me-3"><i class="fas fa-plus"></i></div> 
                                <div> 
                                    <div class="fw-bold">Tambah 
Logbook</div> 
                                    <small class="text-muted">Catat 
bimbingan baru</small> 
                                </div> 
                            </div> 
                        </a> 
                        <a href="#" class="btn btn-light text-start 
border hover-shadow"> 
                            <div class="d-flex align-items-center"> 
                                <div class="bg-success text-white 
rounded p-2 me-3"><i class="fas fa-upload"></i></div> 
                                <div> 
                                    <div class="fw-bold">Upload 
Berkas</div> 
                                    <small class="text-muted">Kirim 
file revisi</small> 
                                </div> 
                            </div> 
                        </a> 
                    </div> 
                </div> 
            </div> 
 
        </div> 
    </div> 
</div> 
@endsection 