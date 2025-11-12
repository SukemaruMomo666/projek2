@extends('layouts.admin') 
 
@section('title', 'Profil Saya') 
 
@push('styles') 
<style> 
    /* Header Profil dengan Gradien */ 
    .profile-header { 
        background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); 
        height: 150px; 
        border-radius: 15px 15px 0 0; 
        position: relative; 
        margin-bottom: 60px; 
    } 
     
    /* Foto Profil Bulat & Timbul */ 
    .profile-img-container { 
        position: absolute; 
        bottom: -50px; 
        left: 40px; 
        width: 120px; 
        height: 120px; 
        border-radius: 50%; 
        border: 5px solid #fff; 
        background-color: #fff; 
        overflow: hidden; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.15); 
    } 
    .profile-img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
    } 
 
    /* Styling Tab Navigasi */ 
    .profile-tabs .nav-link { 
        border: none; 
        color: #6c757d; 
        font-weight: 600; 
        padding: 1rem 1.5rem; 
        border-bottom: 3px solid transparent; 
        background: transparent; 
    } 
    .profile-tabs .nav-link.active { 
        color: #0d6efd; 
        border-bottom: 3px solid #0d6efd; 
    } 
    .profile-tabs .nav-link:hover { 
        color: #0d6efd; 
    } 
 
    /* Form Input Styling */ 
    .form-label-custom { 
        font-size: 0.85rem; 
        font-weight: 700; 
        color: #495057; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
    } 
    .form-control:focus { 
        box-shadow: none; 
        border-color: #0d6efd; 
        background-color: #f8f9fa; 
    } 
</style> 
@endpush 
 
@section('content') 
<div class="container-fluid px-4 pb-5"> 
     
    <!-- Judul Halaman --> 
    <h1 class="mt-4 mb-4 fw-bold h3 text-dark">Pengaturan Profil</h1> 
 
    <!-- Alert Sukses (Jika ada update data) --> 
    @if (session('status') === 'profile-updated') 
        <div class="alert alert-success alert-dismissible fade show 
shadow-sm border-0" role="alert"> 
            <i class="fas fa-check-circle me-2"></i> Data profil 
berhasil diperbarui! 
            <button type="button" class="btn-close" 
data-bs-dismiss="alert" aria-label="Close"></button> 
        </div> 
    @endif 
    @if (session('status') === 'password-updated') 
        <div class="alert alert-success alert-dismissible fade show 
shadow-sm border-0" role="alert"> 
            <i class="fas fa-lock me-2"></i> Password berhasil diubah! 
            <button type="button" class="btn-close" 
data-bs-dismiss="alert" aria-label="Close"></button> 
        </div> 
    @endif 
 
    <div class="row"> 
        <!-- KOLOM KIRI: KARTU PROFIL SINGKAT --> 
        <div class="col-xl-4"> 
            <div class="card border-0 shadow rounded-3 mb-4"> 
                <!-- Header Warna --> 
                <div class="profile-header"> 
                    <div class="profile-img-container"> 
                        <!-- Avatar Default (Inisial Nama) --> 
                        <img src="https://ui-avatars.com/api/?name={{ 
urlencode(Auth::user()->name) }}&background=random&color=fff&size=128" 
class="profile-img" alt="User Avatar"> 
                    </div> 
                </div> 
                 
                <div class="card-body pt-0 mt-2 px-4"> 
                    <!-- Info Singkat --> 
                    <div class="mb-4" style="margin-top: 60px;"> <!-- 
Spacer untuk foto --> 
                        <h4 class="fw-bold text-dark mb-1">{{ 
Auth::user()->name }}</h4> 
                        <p class="text-muted mb-1"><i class="fas 
fa-id-card me-2"></i>{{ Auth::user()->nim ?? 'NIM Belum Diisi' }}</p> 
                        <p class="text-primary small fw-bold mb-3">{{ 
Auth::user()->email }}</p> 
                        <span class="badge bg-success rounded-pill 
px-3">Mahasiswa Aktif</span> 
                    </div> 
 
                    <hr> 
 
                    <!-- Statistik Akademik (Dummy Data) --> 
                    <div class="row text-center"> 
                        <div class="col-4 border-end"> 
                            <h5 class="mb-0 fw-bold text-dark">7</h5> 
                            <small class="text-muted">Semester</small> 
                        </div> 
                        <div class="col-4 border-end"> 
                            <h5 class="mb-0 fw-bold 
text-dark">3.85</h5> 
                            <small class="text-muted">IPK</small> 
                        </div> 
                        <div class="col-4"> 
                            <h5 class="mb-0 fw-bold 
text-dark">144</h5> 
                            <small class="text-muted">SKS</small> 
                        </div> 
                    </div> 
                </div> 
            </div> 
        </div> 
 
        <!-- KOLOM KANAN: FORM EDIT & PASSWORD --> 
        <div class="col-xl-8"> 
            <div class="card border-0 shadow rounded-3"> 
                <div class="card-header bg-white border-bottom-0 pt-3 
px-4"> 
                    <ul class="nav nav-tabs profile-tabs 
card-header-tabs" id="profileTab" role="tablist"> 
                        <li class="nav-item"> 
                            <button class="nav-link active" 
id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" 
type="button" role="tab"><i class="fas fa-user-edit me-2"></i>Edit 
Biodata</button> 
                        </li> 
                        <li class="nav-item"> 
                            <button class="nav-link" id="security-tab" 
data-bs-toggle="tab" data-bs-target="#security" type="button" 
role="tab"><i class="fas fa-shield-alt me-2"></i>Keamanan & 
Password</button> 
                        </li> 
                    </ul> 
                </div> 
                 
                <div class="card-body p-4"> 
                    <div class="tab-content" id="profileTabContent"> 
                         
                        <!-- TAB 1: EDIT BIODATA --> 
                        <div class="tab-pane fade show active" 
id="detail" role="tabpanel"> 
                            <form method="post" action="{{ 
route('profile.update') }}"> 
                                @csrf 
                                @method('patch') 
 
                                <div class="row g-3"> 
                                    <!-- Nama Lengkap --> 
                                    <div class="col-md-6"> 
                                        <label 
class="form-label-custom">Nama Lengkap</label> 
                                        <input type="text" 
class="form-control py-2" name="name" value="{{ old('name', 
Auth::user()->name) }}" required> 
                                        @error('name') <div 
class="text-danger small mt-1">{{ $message }}</div> @enderror 
                                    </div> 
 
                                    <!-- NIM (Biasanya Readonly) --> 
                                    <div class="col-md-6"> 
                                        <label 
class="form-label-custom">NIM</label> 
                                        <input type="text" 
class="form-control py-2 bg-light" name="nim" value="{{ old('nim', 
Auth::user()->nim) }}" readonly title="Hubungi admin untuk mengubah 
NIM"> 
                                    </div> 
 
                                    <!-- Email --> 
                                    <div class="col-md-12"> 
                                        <label 
class="form-label-custom">Email Kampus</label> 
                                        <input type="email" 
class="form-control py-2" name="email" value="{{ old('email', 
Auth::user()->email) }}" required> 
                                        @error('email') <div 
class="text-danger small mt-1">{{ $message }}</div> @enderror 
                                    </div> 
 
                                    <!-- Prodi (Statik Dulu) --> 
                                    <div class="col-md-6"> 
                                        <label 
class="form-label-custom">Program Studi</label> 
                                        <select class="form-select 
py-2" disabled> 
                                            <option selected>D3 - 
Teknik Informatika</option> 
                                        </select> 
                                    </div> 
 
                                    <!-- No HP (Contoh Tambahan) --> 
                                    <div class="col-md-6"> 
                                        <label 
class="form-label-custom">No. WhatsApp</label> 
                                        <input type="text" 
class="form-control py-2" placeholder="08xxxxxxxxxx" value=""> 
                                    </div> 
                                </div> 
 
                                <div class="d-flex justify-content-end 
mt-4"> 
                                    <button type="submit" class="btn 
btn-primary px-4 fw-bold shadow-sm"> 
                                        <i class="fas fa-save 
me-2"></i> Simpan Perubahan 
                                    </button> 
                                </div> 
                            </form> 
                        </div> 
 
                        <!-- TAB 2: GANTI PASSWORD --> 
                        <div class="tab-pane fade" id="security" 
role="tabpanel"> 
                             
                            <div class="alert alert-warning border-0 
shadow-sm mb-4"> 
                                <div class="d-flex"> 
                                    <div class="me-3"><i class="fas 
fa-exclamation-triangle fa-2x"></i></div> 
                                    <div> 
                                        <h6 class="fw-bold 
mb-1">Penting!</h6> 
                                        <small>Gunakan password yang 
kuat (minimal 8 karakter, kombinasi huruf dan angka) untuk menjaga 
keamanan akun akademik Anda.</small> 
                                    </div> 
                                </div> 
                            </div> 
 
                            <form method="post" action="{{ 
route('password.update') }}"> 
                                @csrf 
                                @method('put') 
 
                                <div class="mb-3"> 
                                    <label 
class="form-label-custom">Password Saat Ini</label> 
                                    <input type="password" 
class="form-control" name="current_password" 
autocomplete="current-password" placeholder="Masukkan password 
lama..."> 
                                    @error('current_password', 
'updatePassword')  
                                        <div class="text-danger small 
mt-1">{{ $message }}</div>  
                                    @enderror 
                                </div> 
 
                                <div class="row g-3"> 
                                    <div class="col-md-6"> 
                                        <label 
class="form-label-custom">Password Baru</label> 
                                        <input type="password" 
class="form-control" name="password" autocomplete="new-password" 
placeholder="Password baru..."> 
                                        @error('password', 
'updatePassword')  
                                            <div class="text-danger 
small mt-1">{{ $message }}</div>  
                                        @enderror 
                                    </div> 
                                    <div class="col-md-6"> 
                                        <label 
class="form-label-custom">Konfirmasi Password Baru</label> 
                                        <input type="password" 
class="form-control" name="password_confirmation" 
autocomplete="new-password" placeholder="Ulangi password baru..."> 
                                        
@error('password_confirmation', 'updatePassword')  
                                            <div class="text-danger 
small mt-1">{{ $message }}</div>  
                                        @enderror 
                                    </div> 
                                </div> 
 
                                <div class="d-flex justify-content-end 
mt-4"> 
                                    <button type="submit" class="btn 
btn-danger px-4 fw-bold shadow-sm"> 
                                        <i class="fas fa-key 
me-2"></i> Update Password 
                                    </button> 
                                </div> 
                            </form> 
                        </div> 
 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
</div> 
@endsection 
 
 