@extends('layouts.app')

@section('title', 'Bimbingan Mahasiswa')

@section('content')
<!-- ====== Hero Section ====== -->
<section class="bg-blue-600 text-white py-20 text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Selamat Datang di Sistem Bimbingan Mahasiswa</h1>
        <p class="text-lg max-w-2xl mx-auto">
            Platform digital untuk mempermudah proses bimbingan antara dosen dan mahasiswa —
            mulai dari penjadwalan, laporan, hingga komunikasi akademik secara efisien.
        </p>
        <div class="mt-6">
            <a href="{{ route('login') }}" class="bg-white text-blue-700 font-semibold px-6 py-3 rounded-lg hover:bg-gray-100 transition">
                Mulai Sekarang
            </a>
        </div>
    </div>
</section>

<!-- ====== Fitur Utama ====== -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Fitur Utama</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="bg-white shadow-lg p-8 rounded-2xl hover:shadow-2xl transition">
                <div class="text-blue-600 text-5xl mb-4">📅</div>
                <h3 class="text-xl font-semibold mb-2">Penjadwalan Bimbingan</h3>
                <p class="text-gray-600">
                    Atur dan pantau jadwal bimbingan secara fleksibel agar komunikasi tetap terkoordinasi.
                </p>
            </div>

            <div class="bg-white shadow-lg p-8 rounded-2xl hover:shadow-2xl transition">
                <div class="text-blue-600 text-5xl mb-4">💬</div>
                <h3 class="text-xl font-semibold mb-2">Chat & Pesan</h3>
                <p class="text-gray-600">
                    Fasilitas pesan antara mahasiswa dan dosen pembimbing untuk konsultasi cepat dan mudah.
                </p>
            </div>

            <div class="bg-white shadow-lg p-8 rounded-2xl hover:shadow-2xl transition">
                <div class="text-blue-600 text-5xl mb-4">📑</div>
                <h3 class="text-xl font-semibold mb-2">Laporan Kemajuan</h3>
                <p class="text-gray-600">
                    Upload dan pantau perkembangan laporan tugas akhir atau kegiatan bimbingan lainnya.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ====== Dosen Pembimbing ====== -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Dosen Pembimbing</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 shadow-md rounded-xl text-center">
                <img src="/images/dosen1.jpg" alt="Dosen" class="w-32 h-32 mx-auto rounded-full object-cover mb-4">
                <h4 class="text-lg font-semibold">Dr. Rahmat Setiawan, M.Kom</h4>
                <p class="text-gray-500">Bidang: Rekayasa Perangkat Lunak</p>
            </div>

            <div class="bg-white p-6 shadow-md rounded-xl text-center">
                <img src="/images/dosen2.jpg" alt="Dosen" class="w-32 h-32 mx-auto rounded-full object-cover mb-4">
                <h4 class="text-lg font-semibold">Ir. Dian Suryani, M.T</h4>
                <p class="text-gray-500">Bidang: Sistem Informasi</p>
            </div>

            <div class="bg-white p-6 shadow-md rounded-xl text-center">
                <img src="/images/dosen3.jpg" alt="Dosen" class="w-32 h-32 mx-auto rounded-full object-cover mb-4">
                <h4 class="text-lg font-semibold">Dr. Adi Nugroho, S.T., M.Kom</h4>
                <p class="text-gray-500">Bidang: Kecerdasan Buatan</p>
            </div>
        </div>
    </div>
</section>

<!-- ====== Pengumuman Terbaru ====== -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Pengumuman Terbaru</h2>

        <div class="max-w-3xl mx-auto space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h4 class="font-semibold text-xl mb-2">📢 Jadwal Bimbingan Minggu Ini</h4>
                <p class="text-gray-600">
                    Mahasiswa diminta hadir sesuai jadwal yang telah ditentukan di sistem. Pastikan laporan sudah diperbarui sebelum bimbingan dimulai.
                </p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h4 class="font-semibold text-xl mb-2">🗓️ Deadline Upload Proposal</h4>
                <p class="text-gray-600">
                    Batas akhir pengumpulan proposal adalah tanggal 5 November 2025 pukul 23:59 WIB. Segera upload melalui menu “Laporan Kemajuan”.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ====== Footer ====== -->
<footer class="bg-blue-700 text-white py-6 text-center">
    <p>© {{ date('Y') }} Sistem Bimbingan Mahasiswa | Teknologi Rekayasa Perangkat Lunak</p>
</footer>
@endsection
