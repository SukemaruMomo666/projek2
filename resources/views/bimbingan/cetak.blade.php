<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Logbook Bimbingan</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 0; font-size: 10pt; }
        
        .biodata { margin-bottom: 20px; }
        .biodata table { width: 100%; }
        .biodata td { padding: 2px 0; vertical-align: top; }
        
        table.logbook { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.logbook th, table.logbook td { border: 1px solid #000; padding: 8px; text-align: left; vertical-align: top; }
        table.logbook th { background-color: #f0f0f0; }
        
        .ttd-area { margin-top: 50px; float: right; width: 200px; text-align: center; }
        
        /* Agar tombol print tidak ikut tercetak */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.history.back()" style="padding: 5px 10px; cursor: pointer;">&larr; Kembali</button>
    </div>

    <div class="header">
        <h2>KARTU KENDALI BIMBINGAN SKRIPSI</h2>
        <p>JURUSAN TEKNOLOGI INFORMASI DAN KOMPUTER</p>
    </div>

    <div class="biodata">
        <table>
            <tr>
                <td width="150">Nama Mahasiswa</td>
                <td width="10">:</td>
                <td>{{ $mahasiswa->name }}</td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $mahasiswa->nim ?? '-' }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $mahasiswa->prodi ?? '-' }}</td>
            </tr>
            <tr>
                <td>Dosen Pembimbing</td>
                <td>:</td>
                <td>{{ $mahasiswa->dosenPembimbing->name ?? 'Belum Ditentukan' }}</td>
            </tr>
        </table>
    </div>

    <table class="logbook">
        <thead>
            <tr>
                <th width="5%" style="text-align: center;">No</th>
                <th width="15%">Tanggal</th>
                <th width="50%">Materi Bimbingan & Catatan</th>
                <th width="15%">Paraf Dosen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logbooks as $index => $log)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($log->tanggal_bimbingan)->format('d/m/Y') }}</td>
                <td>
                    <strong>{{ Str::before($log->materi, ':') }}</strong><br>
                    {{ Str::after($log->materi, ':') }}
                    <br><br>
                    <i>Catatan Dosen: {{ $log->catatan_dosen ?? '-' }}</i>
                </td>
                <td style="text-align: center;">
                    @if($log->status == 'Disetujui')
                        <small>[ACC]</small>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Belum ada data bimbingan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-area">
        <p>Mengetahui,<br>Dosen Pembimbing</p>
        <br><br><br>
        <p><strong>{{ $mahasiswa->dosenPembimbing->name ?? '(..........................)' }}</strong><br>
        NIDN. {{ $mahasiswa->dosenPembimbing->nidn ?? '' }}</p>
    </div>

</body>
</html>