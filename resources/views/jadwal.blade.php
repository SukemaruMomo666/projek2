@extends('layouts.admin') 
 
@section('title', 'Jadwal Bimbingan') 
 
@push('styles') 
<style> 
    /* Styling Tab Modern */ 
    .nav-tabs .nav-link { 
        border: none; 
        color: #6c757d; 
        font-weight: 600; 
        padding: 1rem 1.5rem; 
        border-bottom: 3px solid transparent; 
    } 
    .nav-tabs .nav-link.active { 
        color: #0d6efd; 
        border-bottom: 3px solid #0d6efd; 
        background: none; 
    } 
    .nav-tabs .nav-link:hover { 
        border-color: transparent; 
        color: #0d6efd; 
    } 
 
    /* Kartu Dosen */ 
    .lecturer-card { 
        transition: transform 0.2s, box-shadow 0.2s; 
        border: 1px solid #e2e8f0; 
    } 
    .lecturer-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 20px rgba(0,0,0,0.05); 
        border-color: #cbd5e1; 
    } 
    .lecturer-avatar { 
        width: 80px; 
        height: 80px; 
        object-fit: cover; 
        border: 3px solid #f1f5f9; 
    } 
    .badge-expertise { 
        background-color: #e0f2fe; 
        color: #0284c7; 
        font-size: 0.7rem; 
        font-weight: 600; 
    } 
     
    /* Slot Waktu */ 
    .time-slot { 
        cursor: pointer; 
        border: 1px solid #dee2e6; 
        border-radius: 6px; 
        padding: 5px 10px; 
        text-align: center; 
        font-size: 0.85rem; 
        transition: all 0.2s; 
        background-color: #fff; 
    } 
    .time-slot:hover { 
        border-color: #0d6efd; 
        color: #0d6efd; 
        background-color: #f8f9fa; 
    } 
    .time-slot.selected { 
        background-color: #0d6efd; 
        color: white; 
        border-color: #0d6efd; 
    } 
    .time-slot.disabled { 
        background-color: #f8f9fa; 
        color: #adb5bd; 
        cursor: not-allowed; 
        border-color: #f1f5f9; 
    } 
 
    /* Kartu Jadwal Saya */ 
    .schedule-card { 
        border-left: 4px solid #0d6efd; 
    } 
    .schedule-card.pending { border-left-color: #ffc107; } 
    .schedule-card.approved { border-left-color: #198754; } 
    .schedule-card.rejected { border-left-color: #dc3545; } 
</style> 
@endpush 
 
@section('content') 
<div class="container-fluid px-4 pb-5"> 
     
    <!-- Header --> 
    <div class="d-flex justify-content-between align-items-center mt-4 
mb-4"> 
        <div> 
            <h1 class="h2 fw-bold mb-0 text-dark">Jadwal & 
Booking</h1> 
            <p class="text-muted mb-0">Atur pertemuan bimbingan dengan 
dosen pembimbing.</p> 
        </div> 
    </div> 
 
    <!-- Navigasi Tab --> 
    <ul class="nav nav-tabs mb-4" id="scheduleTab" role="tablist"> 
        <li class="nav-item" role="presentation"> 
            <button class="nav-link active" id="lecturer-tab" 
data-bs-toggle="tab" data-bs-target="#lecturer" type="button" 
role="tab"><i class="fas fa-search me-2"></i>Cari Jadwal 
Dosen</button> 
        </li> 
        <li class="nav-item" role="presentation"> 
            <button class="nav-link" id="myschedule-tab" 
data-bs-toggle="tab" data-bs-target="#myschedule" type="button" 
role="tab"><i class="fas fa-calendar-check me-2"></i>Jadwal Saya <span 
class="badge bg-danger rounded-pill ms-1">2</span></button> 
        </li> 
    </ul> 
 
    <div class="tab-content" id="scheduleTabContent"> 
         
        <!-- TAB 1: CARI JADWAL DOSEN --> 
        <div class="tab-pane fade show active" id="lecturer" 
role="tabpanel"> 
             
            <!-- Search Filter --> 
            <div class="card border-0 shadow-sm mb-4 bg-light"> 
                <div class="card-body"> 
                    <div class="row g-3"> 
                        <div class="col-md-8"> 
                            <div class="input-group"> 
                                <span class="input-group-text bg-white 
border-end-0"><i class="fas fa-search text-muted"></i></span> 
                                <input type="text" class="form-control 
border-start-0 ps-0" placeholder="Cari nama dosen atau keahlian..."> 
                            </div> 
                        </div> 
                        <div class="col-md-4"> 
                            <select class="form-select"> 
                                <option selected>Semua Hari</option> 
                                <option value="1">Senin</option> 
                                <option value="2">Selasa</option> 
                                <option value="3">Rabu</option> 
                                <option value="4">Kamis</option> 
                                <option value="5">Jumat</option> 
                            </select> 
                        </div> 
                    </div> 
                </div> 
            </div> 
 
            <div class="row g-4"> 
                <!-- Kartu Dosen 1 --> 
                <div class="col-xl-4 col-md-6"> 
                    <div class="card lecturer-card h-100 border-0 
shadow-sm rounded-3"> 
                        <div class="card-body text-center p-4"> 
                            <img 
src="https://ui-avatars.com/api/?name=Prabu+Alam&background=0D8ABC&col
 or=fff" class="lecturer-avatar rounded-circle mb-3 shadow-sm" 
alt="Dosen"> 
                            <h5 class="fw-bold mb-1 text-dark">Dr. 
Prabu Alam, M.Kom.</h5> 
                            <div class="text-muted small mb-3">NIDN: 
0412345678</div> 
                             
                            <div class="mb-3"> 
                                <span class="badge badge-expertise 
me-1">Rekayasa Perangkat Lunak</span> 
                                <span class="badge 
badge-expertise">AI</span> 
                            </div> 
 
                            <div class="alert alert-light border small 
text-start mb-3"> 
                                <i class="fas fa-info-circle text-info 
me-1"></i> <strong>Status:</strong>  
                                <span class="text-success 
fw-bold">Tersedia Hari Ini</span> 
                            </div> 
 
                            <button class="btn btn-primary w-100 
fw-bold rounded-pill" data-bs-toggle="modal" 
data-bs-target="#bookingModal" onclick="setDosen('Dr. Prabu Alam')"> 
                                <i class="fas fa-calendar-plus 
me-1"></i> Ajukan Pertemuan 
                            </button> 
                        </div> 
                        <div class="card-footer bg-white border-top-0 
text-center pb-3"> 
                            <small class="text-muted">Jadwal Terdekat: 
<strong>Senin, 09:00 WIB</strong></small> 
                        </div> 
                    </div> 
                </div> 
 
                <!-- Kartu Dosen 2 --> 
                <div class="col-xl-4 col-md-6"> 
                    <div class="card lecturer-card h-100 border-0 
shadow-sm rounded-3"> 
                        <div class="card-body text-center p-4"> 
                            <img 
src="https://ui-avatars.com/api/?name=Siti+Aminah&background=E91E63&co
 lor=fff" class="lecturer-avatar rounded-circle mb-3 shadow-sm" 
alt="Dosen"> 
                            <h5 class="fw-bold mb-1 text-dark">Siti 
Aminah, S.T., M.T.</h5> 
                            <div class="text-muted small mb-3">NIDN: 
0487654321</div> 
                             
                            <div class="mb-3"> 
                                <span class="badge badge-expertise 
me-1">Jaringan Komputer</span> 
                                <span class="badge 
badge-expertise">IoT</span> 
                            </div> 
 
                            <div class="alert alert-light border small 
text-start mb-3"> 
                                <i class="fas fa-clock text-warning 
me-1"></i> <strong>Status:</strong>  
                                <span class="text-muted">Sibuk s.d. 
13:00</span> 
                            </div> 
 
                            <button class="btn btn-primary w-100 
fw-bold rounded-pill" data-bs-toggle="modal" 
data-bs-target="#bookingModal" onclick="setDosen('Siti Aminah')"> 
                                <i class="fas fa-calendar-plus 
me-1"></i> Ajukan Pertemuan 
                            </button> 
                        </div> 
                        <div class="card-footer bg-white border-top-0 
text-center pb-3"> 
                            <small class="text-muted">Jadwal Terdekat: 
<strong>Selasa, 10:00 WIB</strong></small> 
                        </div> 
                    </div> 
                </div> 
            </div> 
        </div> 
 
        <!-- TAB 2: JADWAL SAYA --> 
        <div class="tab-pane fade" id="myschedule" role="tabpanel"> 
             
            <div class="card border-0 shadow-sm rounded-3"> 
                <div class="card-header bg-white py-3"> 
                    <h6 class="m-0 fw-bold text-primary">Daftar 
Pertemuan Saya</h6> 
                </div> 
                <div class="card-body p-0"> 
                    <div class="list-group list-group-flush"> 
                         
                        <!-- Item Jadwal: Pending --> 
                        <div class="list-group-item p-3 schedule-card 
pending"> 
                            <div class="d-flex w-100 
justify-content-between align-items-center"> 
                                <div> 
                                    <div class="d-flex 
align-items-center mb-1"> 
                                        <h6 class="mb-0 fw-bold 
text-dark">Bimbingan Bab 3 (Revisi)</h6> 
                                        <span class="badge bg-warning 
text-dark ms-2" style="font-size: 0.7em;">MENUNGGU KONFIRMASI</span> 
                                    </div> 
                                    <small class="text-muted"> 
                                        <i class="fas fa-user 
me-1"></i> Dr. Prabu Alam •  
                                        <i class="fas fa-calendar me-1 
ms-2"></i> Kamis, 27 Okt 2025 •  
                                        <i class="fas fa-clock me-1 
ms-2"></i> 10:00 - 11:00 WIB 
                                    </small> 
                                    <div class="mt-2 small text-muted 
bg-light p-2 rounded"> 
                                        <i class="fas fa-sticky-note 
me-1"></i> Catatan: "Saya ingin mendiskusikan metode sampling." 
                                    </div> 
                                </div> 
                                <div class="text-end"> 
                                    <button class="btn btn-sm 
btn-outline-danger" title="Batalkan"><i class="fas 
fa-times"></i></button> 
                                </div> 
                            </div> 
                        </div> 
 
                        <!-- Item Jadwal: Approved --> 
                        <div class="list-group-item p-3 schedule-card 
approved"> 
                            <div class="d-flex w-100 
justify-content-between align-items-center"> 
                                <div> 
                                    <div class="d-flex 
align-items-center mb-1"> 
                                        <h6 class="mb-0 fw-bold 
text-dark">ACC Judul & Proposal</h6> 
                                        <span class="badge bg-success 
ms-2" style="font-size: 0.7em;">DISETUJUI</span> 
                                    </div> 
                                    <small class="text-muted"> 
                                        <i class="fas fa-user 
me-1"></i> Dr. Prabu Alam •  
                                        <i class="fas fa-calendar me-1 
ms-2"></i> Senin, 24 Okt 2025 •  
                                        <i class="fas 
fa-map-marker-alt me-1 ms-2"></i> Ruang Dosen 1 
                                    </small> 
                                </div> 
                                <div class="text-end"> 
                                    <button class="btn btn-sm 
btn-success disabled"><i class="fas fa-check"></i> Siap</button> 
                                </div> 
                            </div> 
                        </div> 
 
                    </div> 
                </div> 
            </div> 
 
        </div> 
    </div> 
</div> 
 
<!-- MODAL BOOKING --> 
<div class="modal fade" id="bookingModal" tabindex="-1" 
aria-hidden="true"> 
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content border-0 shadow"> 
            <div class="modal-header bg-primary text-white"> 
                <h5 class="modal-title fw-bold"><i class="fas 
fa-calendar-check me-2"></i>Booking Jadwal</h5> 
                <button type="button" class="btn-close 
btn-close-white" data-bs-dismiss="modal"></button> 
            </div> 
            <form action="#"> 
                @csrf 
                <div class="modal-body p-4"> 
                    <div class="mb-3"> 
                        <label class="form-label fw-bold small 
text-muted">Dosen Pembimbing</label> 
                        <input type="text" class="form-control 
bg-light" id="modalDosenName" value="Dr. Prabu Alam" readonly> 
                    </div> 
                     
                    <div class="mb-3"> 
                        <label class="form-label fw-bold small 
text-muted">Pilih Tanggal</label> 
                        <input type="date" class="form-control" 
required> 
                    </div> 
 
                    <div class="mb-3"> 
                        <label class="form-label fw-bold small 
text-muted">Pilih Slot Waktu (Durasi 1 Jam)</label> 
                        <div class="d-flex flex-wrap gap-2"> 
                            <!-- Contoh Slot --> 
                            <div class="time-slot" 
onclick="selectSlot(this)">08:00</div> 
                            <div class="time-slot" 
onclick="selectSlot(this)">09:00</div> 
                            <div class="time-slot disabled" 
title="Sudah dipesan">10:00</div> 
                            <div class="time-slot" 
onclick="selectSlot(this)">11:00</div> 
                            <div class="time-slot" 
onclick="selectSlot(this)">13:00</div> 
                            <div class="time-slot" 
onclick="selectSlot(this)">14:00</div> 
                        </div> 
                        <input type="hidden" name="selected_slot" 
id="selected_slot"> 
                    </div> 
 
                    <div class="mb-3"> 
                        <label class="form-label fw-bold small 
text-muted">Topik / Keperluan</label> 
                        <textarea class="form-control" rows="2" 
placeholder="Contoh: Konsultasi Bab 4" required></textarea> 
                    </div> 
                </div> 
                <div class="modal-footer bg-light"> 
                    <button type="button" class="btn btn-link 
text-muted text-decoration-none" 
data-bs-dismiss="modal">Batal</button> 
                    <button type="submit" class="btn btn-primary 
px-4">Kirim Pengajuan</button> 
                </div> 
            </form> 
        </div> 
    </div> 
</div> 
 
<!-- Simple Script --> 
<script> 
    function setDosen(name) { 
        document.getElementById('modalDosenName').value = name; 
    } 
 
    function selectSlot(element) { 
        if (element.classList.contains('disabled')) return; 
         
        // Reset all slots 
        document.querySelectorAll('.time-slot').forEach(el => 
el.classList.remove('selected')); 
         
        // Select clicked 
        element.classList.add('selected'); 
        document.getElementById('selected_slot').value = 
element.innerText; 
    } 
</script> 
@endsection 
 


 
