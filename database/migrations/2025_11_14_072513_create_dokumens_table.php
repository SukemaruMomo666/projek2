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
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            
            // Relasi
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');

            // Info dari Form
            $table->string('kategori'); // Cth: "Proposal", "Bab 1", "Full Draft"
            $table->text('keterangan')->nullable(); // Pesan dari mahasiswa
            
            // Info File
            $table->string('nama_file_asli'); // Nama file asli dari komputer user
            $table->string('file_path'); // Path file yang disimpan di storage
            
            // Info Review Dosen
            $table->enum('status', ['Menunggu', 'Revisi', 'Disetujui'])->default('Menunggu');
            $table->text('catatan_dosen')->nullable(); // Diisi oleh dosen

            $table->timestamps(); // Kapan file ini di-upload
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};
