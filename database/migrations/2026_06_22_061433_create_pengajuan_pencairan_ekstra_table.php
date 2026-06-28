<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel.
     */
    public function up(): void
    {
        Schema::create('pengajuan_pencairan_ekstra', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel program ekstra (untuk tahu dana program apa yang dicairkan)
            $table->foreignId('extra_program_id')->constrained('extra_programs')->onDelete('cascade');
            
            // Relasi ke tabel staffs (untuk tahu siapa staf operasional yang meminta dana)
            $table->foreignId('staff_id')->constrained('staffs')->onDelete('cascade');
            
            $table->integer('nominal_diminta');
            $table->string('nama_bank');
            $table->string('nomor_rekening');
            
            // Status alur: Baru minta (PENDING), Sudah ditransfer Keuangan (DICAIRKAN), 
            // Nota sudah diinput selesai (SELESAI), Sedang nunggu reimburse tekor (REIMBURSE_PENDING)
            $table->enum('status', ['PENDING', 'DICAIRKAN', 'SELESAI', 'REIMBURSE_PENDING'])->default('PENDING');
            
            // Bukti transfer dari Keuangan ke rekening Operasional (nullable karena diisi nanti)
            $table->string('bukti_transfer_pencairan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi jika terjadi kesalahan.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pencairan_ekstra');
    }
};