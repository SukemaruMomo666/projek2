    @extends('layouts.admin')

    {{-- Mengatur judul halaman --}}
    @section('title', 'Tentang Aplikasi')

    {{-- Mulai bagian konten --}}
    @section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Data Dosen</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Dosen</li>
        </ol>


    @endsection
    {{-- Akhir bagian konten --}}

    {{-- Tambahkan CSS/JS khusus jika perlu --}}
    @push('styles')
    {{-- <style> h1 { color: blue; } </style> --}}
    @endpush

    @push('scripts')
    {{-- <script> console.log('Halaman Tentang dimuat!'); </script> --}}
    @endpush
    
