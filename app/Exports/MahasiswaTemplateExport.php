<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        // Data contoh untuk baris ke-2 dan ke-3
        return [
            ['Budi Santoso', '10111001', 'budi@student.polsub.ac.id', 'D3 Teknik Informatika', 1],
            ['Siti Aminah', '10111002', 'siti@student.polsub.ac.id', 'D3 Teknik Informatika', 1],
        ];
    }

    public function headings(): array
    {
        // Header kolom (Baris 1) - Wajib huruf kecil semua sesuai import
        return [
            'nama',
            'nim',
            'email',
            'prodi',
            'semester',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Bikin header jadi tebal (Bold)
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}