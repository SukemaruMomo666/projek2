<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // ROLE (Admin, Dosen, Mahasiswa)
            $table->enum('role', ['admin', 'dosen', 'mahasiswa'])->default('mahasiswa');

            // Data Khusus Mahasiswa
            $table->string('nim')->unique()->nullable();
            $table->string('prodi')->nullable();
            $table->integer('semester')->nullable();
            
            // Data Khusus Dosen
            $table->string('nidn')->unique()->nullable();
            
            // Relasi Dosen Pembimbing
            $table->foreignId('dosen_pembimbing_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // KOLOM BARU: Status Ganti Password
            $table->boolean('is_password_changed')->default(false); 

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

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};