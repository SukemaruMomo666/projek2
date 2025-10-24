<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-gray-800">
                        Selamat Datang, {{ Auth::user()->name }}!
                    </h3>
                    <p class="mt-2 text-gray-600">
                        Ini adalah pusat kendali Anda untuk mengelola proses bimbingan.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-2 space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h4 class="text-sm font-medium text-gray-500">Dosen Pembimbing</h4>
                            <p class="mt-1 text-xl font-semibold text-gray-900">
                                Dr. Prabu, M.Kom.
                            </p>
                            <span class="text-xs text-gray-500">NIDN: 0412345678</span>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h4 class="text-sm font-medium text-gray-500">Status Skripsi</h4>
                            <p class="mt-1 text-xl font-semibold text-green-600">
                                BAB 3: Disetujui
                            </p>
                            <span class="text-xs text-gray-500">Update: 2 hari lalu</span>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h4 class="text-sm font-medium text-gray-500">Bimbingan Berikutnya</h4>
                            <p class="mt-1 text-xl font-semibold text-gray-900">
                                28 Okt 2025
                            </p>
                            <span class="text-xs text-gray-500">Jam 10:00 - Online</span>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Bimbingan (Logbook)</h3>
                            <table class="w-full min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materi Bimbingan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">20 Okt 2025</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Revisi Metodologi (BAB 3)</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Disetujui
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">15 Okt 2025</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Pengajuan BAB 3</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Revisi
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10 Okt 2025</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ACC BAB 2</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Disetujui
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div> <div class="md:col-span-1 space-y-6">

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ajukan Bimbingan Baru</h3>
                        <form action="#" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="judul_bimbingan" class="block text-sm font-medium text-gray-700">Judul / Materi</label>
                                    <input type="text" name="judul_bimbingan" id="judul_bimbingan" placeholder="Contoh: Pengajuan BAB 4"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="file_bimbingan" class="block text-sm font-medium text-gray-700">Upload File (PDF, DOCX)</label>
                                    <input type="file" name="file_bimbingan" id="file_bimbingan"
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                                <button type="submit" 
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Kirim Pengajuan
                                </button>
                            </div>
                        </form>
                    </div>

                </div> </div> </div>
    </div>
</x-app-layout>