<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MahasiswaImport;
use App\Exports\MahasiswaTemplateExport;

class AdminController extends Controller
{
    /**
     * Dashboard Utama Admin
     */
    public function index()
    {
        $totalDosen = User::where('role', 'dosen')->count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        
        $mahasiswaTanpaPembimbing = User::where('role', 'mahasiswa')
                                        ->whereNull('dosen_pembimbing_id')
                                        ->count();

        $totalBimbingan = Bimbingan::count();

        return view('admin.dashboard', [
            'totalDosen' => $totalDosen,
            'totalMahasiswa' => $totalMahasiswa,
            'mahasiswaTanpaPembimbing' => $mahasiswaTanpaPembimbing,
            'totalBimbingan' => $totalBimbingan
        ]);
    }

    // =========================================================================
    // KELOLA DOSEN
    // =========================================================================

    public function indexDosen()
    {
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        return view('admin.dosen.index', compact('dosens'));
    }

    public function storeDosen(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nidn' => 'required|string|unique:users,nidn',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nidn' => $request->nidn,
            'password' => Hash::make($request->password),
            'role' => 'dosen',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.dosen.index')->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function editDosen(User $dosen)
    {
        return view('admin.dosen.edit', compact('dosen'));
    }

    public function updateDosen(Request $request, User $dosen)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($dosen->id)],
            'nidn' => ['required', 'string', Rule::unique('users')->ignore($dosen->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $dosen->name = $request->name;
        $dosen->email = $request->email;
        $dosen->nidn = $request->nidn;
        
        if ($request->filled('password')) {
            $dosen->password = Hash::make($request->password);
        }

        $dosen->save();

        return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function destroyDosen(User $dosen)
    {
        $dosen->delete();
        return redirect()->route('admin.dosen.index')->with('success', 'Dosen berhasil dihapus.');
    }

    // =========================================================================
    // KELOLA MAHASISWA (UPDATED: Search, Sort & Pagination)
    // =========================================================================

    public function indexMahasiswa(Request $request)
    {
        $query = User::where('role', 'mahasiswa')->with('dosenPembimbing');

        // 1. Fitur Pencarian (Search)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        // 2. Fitur Sorting (Klik Header Tabel)
        // Default: urutkan berdasarkan NIM ascending
        $sortColumn = $request->get('sort', 'nim'); 
        $sortDirection = $request->get('direction', 'asc');

        // Validasi kolom agar aman dari error SQL
        $allowedSorts = ['name', 'nim', 'email', 'kelas', 'prodi', 'semester'];
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('nim', 'asc');
        }

        // 3. Pagination (Tampilkan 20 data per halaman)
        // withQueryString() berguna agar saat pindah halaman, sorting & search tidak hilang
        $mahasiswas = $query->paginate(20)->withQueryString(); 
        
        // Data Pendukung untuk Dropdown Modal
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $list_kelas = User::where('role', 'mahasiswa')
                          ->whereNotNull('kelas')
                          ->distinct()
                          ->pluck('kelas');

        return view('admin.mahasiswa.index', compact('mahasiswas', 'dosens', 'list_kelas'));
    }

    public function createMahasiswa()
    {
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        return view('admin.mahasiswa.create', compact('dosens'));
    }

    public function storeMahasiswa(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nim' => 'required|string|unique:users,nim',
            'password' => 'required|string|min:8|confirmed',
            'dosen_pembimbing_id' => 'nullable|exists:users,id',
            'prodi' => 'nullable|string',
            'semester' => 'nullable|integer',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nim' => $request->nim,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
            'dosen_pembimbing_id' => $request->dosen_pembimbing_id,
            'prodi' => $request->prodi,
            'semester' => $request->semester,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function editMahasiswa(User $mahasiswa)
    {
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        return view('admin.mahasiswa.edit', compact('mahasiswa', 'dosens'));
    }

    public function updateMahasiswa(Request $request, User $mahasiswa)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($mahasiswa->id)],
            'nim' => ['required', 'string', Rule::unique('users')->ignore($mahasiswa->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'dosen_pembimbing_id' => 'nullable|exists:users,id',
            'prodi' => 'nullable|string',
            'semester' => 'nullable|integer',
        ]);

        $mahasiswa->name = $request->name;
        $mahasiswa->email = $request->email;
        $mahasiswa->nim = $request->nim;
        $mahasiswa->dosen_pembimbing_id = $request->dosen_pembimbing_id;
        $mahasiswa->prodi = $request->prodi;
        $mahasiswa->semester = $request->semester;
        
        if ($request->filled('password')) {
            $mahasiswa->password = Hash::make($request->password);
        }

        $mahasiswa->save();

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroyMahasiswa(User $mahasiswa)
    {
        $mahasiswa->delete();
        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }

    // Import Mahasiswa dari Excel
    public function importMahasiswa(Request $request) 
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);
 
        try {
            Excel::import(new MahasiswaImport, $request->file('file_excel'));
            return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('admin.mahasiswa.index')->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    // DOWNLOAD TEMPLATE EXCEL
    public function downloadTemplate()
    {
        return Excel::download(new MahasiswaTemplateExport, 'template_mahasiswa.xlsx');
    }

    // =========================================================================
    // FITUR TAMBAHAN: BATCH TOOLS
    // =========================================================================

    /**
     * Generate Kelas Otomatis (A, B, C...) berdasarkan Modulo NIM.
     * Logika: 
     * - Ambil NIM
     * - Bagi dengan jumlah kelas
     * - Sisa bagi menentukan huruf kelas (A, B, C)
     */
    public function generateKelas(Request $request)
    {
        $request->validate([
            'base_kelas'   => 'required|string',         // Contoh: "TRPL 2"
            'nim_start'    => 'required|string',         // Contoh: "10112001"
            'nim_end'      => 'required|string',         // Contoh: "10112084"
            'jumlah_kelas' => 'required|integer|min:1',  // Jumlah kelas yang diinginkan (Contoh: 3)
        ]);

        // Ambil mahasiswa di range tersebut, diurutkan NIM agar rapi
        $mahasiswas = User::where('role', 'mahasiswa')
                          ->whereBetween('nim', [$request->nim_start, $request->nim_end])
                          ->orderBy('nim', 'asc')
                          ->get();

        $count = 0;
        $totalKelas = $request->jumlah_kelas; 

        foreach ($mahasiswas as $mhs) {
            // Ambil angka dari NIM
            $nimInt = (int)$mhs->nim; 
            
            // Hitung sisa bagi
            $sisaBagi = $nimInt % $totalKelas;

            // Tentukan urutan kelas (1, 2, 3...)
            // Jika sisa bagi 0, itu berarti urutan terakhir (misal urutan ke-3 dari 3 kelas)
            if ($sisaBagi == 0) {
                $urutanKelas = $totalKelas; 
            } else {
                $urutanKelas = $sisaBagi;
            }

            // Ubah Angka jadi Huruf (1=A, 2=B, 3=C)
            // Kode ASCII 'A' adalah 65.
            $suffix = chr(64 + $urutanKelas);

            // Simpan Kelas: "TRPL 2" + " " + "A"
            $mhs->kelas = $request->base_kelas . ' ' . $suffix; 
            $mhs->save();
            
            $count++;
        }

        return redirect()->route('admin.mahasiswa.index')
                         ->with('success', "Berhasil split $count mahasiswa ke dalam $totalKelas kelas ($request->base_kelas).");
    }

    // Plotting Dosen Masal per Kelas
    public function bulkPlotting(Request $request)
    {
        $request->validate([
            'kelas_target' => 'required|string',
            'dosen_id'     => 'required|exists:users,id',
        ]);

        $updated = User::where('role', 'mahasiswa')
                       ->where('kelas', $request->kelas_target)
                       ->update(['dosen_pembimbing_id' => $request->dosen_id]);

        return redirect()->route('admin.mahasiswa.index')
                         ->with('success', "Berhasil plotting dosen untuk $updated mahasiswa di kelas " . $request->kelas_target);
    }

    // =========================================================================
    // PENGATURAN SISTEM
    // =========================================================================

    public function settings()
    {
        return view('admin.settings.index');
    }
    
    public function updateSettings(Request $request)
    {
        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}