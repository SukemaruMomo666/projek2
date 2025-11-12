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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Data Umum
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Role: admin, dosen, mahasiswa
            $table->enum('role', ['admin', 'dosen', 'mahasiswa'])->default('mahasiswa');
 
            // Data Khusus Mahasiswa (Nullable: karena Dosen tidak punya ini)
            $table->string('nim')->unique()->nullable();
            $table->string('prodi')->nullable(); // Contoh: D3 Teknik Informatika
            $table->integer('semester')->nullable(); // Contoh: 5, 7
            
            // Data Khusus Dosen (Nullable: karena Mahasiswa tidak punya ini)
            $table->string('nidn')->unique()->nullable();
            
            // Relasi: Mahasiswa punya 1 Dosen Pembimbing
            // Kita relasikan ke tabel 'users' itu sendiri
            $table->foreignId('dosen_pembimbing_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete(); // Jika dosen dihapus, data mahasiswa aman (kolom ini jadi null)
 
            $table->rememberToken();
            $table->timestamps();
        });
 
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
 
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};