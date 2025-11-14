<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bimbingans', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Mahasiswa (User)
            $table->foreignId('mahasiswa_id')
                  ->constrained('users') // Terhubung ke tabel 'users'
                  ->onDelete('cascade'); // Jika user dihapus, datanya ikut hilang

            // Relasi ke Dosen (User)
            $table->foreignId('dosen_id')
                  ->constrained('users') // Terhubung ke tabel 'users'
                  ->onDelete('cascade');

            // Data Inti Logbook
            $table->date('tanggal_bimbingan');
            $table->string('materi'); // Cth: "Bab 1 - Latar Belakang"
            $table->text('catatan_mahasiswa')->nullable(); // Cth: "Diskusi tentang metode..."
            $table->text('catatan_dosen')->nullable(); // Diisi oleh dosen nanti
            $table->string('file_path')->nullable(); // Path ke file revisi jika ada
            
            // Status dari bimbingan ini
            $table->enum('status', ['Menunggu', 'Revisi', 'Disetujui'])->default('Menunggu');

            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bimbingans');
    }
};
