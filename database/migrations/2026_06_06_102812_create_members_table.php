<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('angkatan', 4); // Menyimpan tahun, misal: 2016
            $table->string('no_wa');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('bukti_pendukung'); // Menyimpan nama file (ijazah/KTM)
            
            // Status pendaftaran alumni
            // pending = baru daftar, active = disetujui admin & VA aktif, rejected = ditolak
            $table->enum('status', ['pending', 'active', 'rejected'])->default('pending');
            
            // Kolom untuk menampung nomor VA dari Sandbox nanti
            $table->string('va_muamalat')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};