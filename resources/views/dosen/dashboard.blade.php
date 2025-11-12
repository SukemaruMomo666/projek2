@extends('layouts.admin') 
 
@section('title', 'Dashboard Dosen') 
 
@section('content') 
<div class="container-fluid px-4"> 
     
    <!-- Hero Section Dosen --> 
    <div class="bg-white rounded-3 shadow-sm p-4 mt-4 mb-4 
border-start border-primary border-5"> 
        <div class="d-flex justify-content-between 
align-items-center"> 
            <div> 
                <h1 class="h3 fw-bold text-dark mb-1">Selamat Datang, 
{{ Auth::user()->name }}</h1> 
                <p class="text-muted mb-0">NIDN: {{ Auth::user()->nidn 
?? '-' }} • Dosen Pembimbing</p> 
            </div> 
            <div class="d-none d-md-block text-end"> 
                <span class="text-muted small d-block">{{ 
now()->translatedFormat('l, d F Y') }}</span> 
            </div> 
        </div> 
    </div> 
 
    <!-- Statistik --> 
    <div class="row g-3 mb-4"> 
        <div class="col-xl-3 col-md-6"> 
            <div class="card bg-primary text-white h-100 shadow-sm 
border-0"> 
                <div class="card-body"> 
                    <div class="d-flex justify-content-between 
align-items-center"> 
                        <div> 
                            <div class="fw-bold text-white-50 small 
text-uppercase">Mahasiswa Bimbingan</div> 
                            <div class="h2 mb-0 fw-bold">12</div> 
                        </div> 
                        <i class="fas fa-users fa-2x opacity-50"></i> 
                    </div> 
                </div> 
            </div> 
        </div> 
        <div class="col-xl-3 col-md-6"> 
            <div class="card bg-warning text-white h-100 shadow-sm 
border-0"> 
                <div class="card-body"> 
                    <div class="d-flex justify-content-between 
align-items-center"> 
                        <div> 
                            <div class="fw-bold text-white-50 small 
text-uppercase">Menunggu Review</div> 
                            <div class="h2 mb-0 fw-bold">4</div> 
                        </div> 
                        <i class="fas fa-clock fa-2x opacity-50"></i> 
                    </div> 
                </div> 
            </div> 
        </div> 
        <div class="col-xl-3 col-md-6"> 
            <div class="card bg-success text-white h-100 shadow-sm 
border-0"> 
                <div class="card-body"> 
                    <div class="d-flex justify-content-between 
align-items-center"> 
                        <div> 
                            <div class="fw-bold text-white-50 small 
text-uppercase">Siap Sidang</div> 
                            <div class="h2 mb-0 fw-bold">2</div> 
                        </div> 
                        <i class="fas fa-graduation-cap fa-2x 
opacity-50"></i> 
                    </div> 
                </div> 
            </div> 
        </div> 
        <div class="col-xl-3 col-md-6"> 
            <div class="card bg-info text-white h-100 shadow-sm 
border-0"> 
                <div class="card-body"> 
                    <div class="d-flex justify-content-between 
align-items-center"> 
                        <div> 
                            <div class="fw-bold text-white-50 small 
text-uppercase">Jadwal Hari Ini</div> 
                            <div class="h2 mb-0 fw-bold">1</div> 
                        </div> 
                        <i class="fas fa-calendar-day fa-2x 
opacity-50"></i> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
 
    <!-- Tabel Validasi Cepat --> 
    <div class="card mb-4 shadow border-0"> 
        <div class="card-header bg-white py-3"> 
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-tasks 
me-2 text-primary"></i>Perlu Validasi Terbaru</h6> 
        </div> 
        <div class="card-body p-0"> 
            <div class="table-responsive"> 
                <table class="table table-hover align-middle mb-0"> 
                    <thead class="bg-light"> 
                        <tr> 
                            <th class="ps-4">Mahasiswa</th> 
                            <th>Dokumen</th> 
                            <th>Tanggal Upload</th> 
                            <th class="text-end pe-4">Aksi</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <!-- Contoh Data --> 
                        <tr> 
                            <td class="ps-4"> 
                                <div class="d-flex 
align-items-center"> 
                                    <img 
src="https://ui-avatars.com/api/?name=Qisty&background=random" 
class="rounded-circle me-2" width="35"> 
                                    <div> 
                                        <div 
class="fw-bold">Qisty</div> 
                                        <div class="small 
text-muted">NIM: 10111054</div> 
                                    </div> 
                                </div> 
                            </td> 
                            <td> 
                                <span class="badge bg-light text-dark 
border">Bab 3</span> 
                                <div class="small 
text-primary">Revisi_Metodologi.pdf</div> 
                            </td> 
                            <td>24 Okt 2025</td> 
                            <td class="text-end pe-4"> 
                                <button class="btn btn-sm 
btn-primary">Review</button> 
                            </td> 
                        </tr> 
                    </tbody> 
                </table> 
            </div> 
</div> 
</div> 
</div> 
@endsection 