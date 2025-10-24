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
    Schema::table('users', function (Blueprint $table) {
        // Tambahkan kolom nim, unik (tidak boleh ada yg sama), dan boleh kosong
        // (karena nanti ada user yg login via Microsoft)
        $table->string('nim')->unique()->nullable()->after('name');

        // Ubah kolom email agar boleh kosong (nullable)
        $table->string('email')->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
