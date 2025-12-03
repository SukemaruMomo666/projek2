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
use App\Exports\MahasiswaTemplateExport; // Pastikan ini ada!

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
    // KELOLA MAHASISWA
    // =========================================================================

    public function indexMahasiswa()
    {
        $mahasiswas = User::where('role', 'mahasiswa')
                          ->with('dosenPembimbing')
                          ->orderBy('name')
                          ->get();
        return view('admin.mahasiswa.index', compact('mahasiswas'));
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
            
            return redirect()->route('admin.mahasiswa.index')
                             ->with('success', 'Data mahasiswa berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('admin.mahasiswa.index')
                             ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    // FUNGSI BARU: DOWNLOAD TEMPLATE EXCEL
    public function downloadTemplate()
    {
        return Excel::download(new MahasiswaTemplateExport, 'template_mahasiswa.xlsx');
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