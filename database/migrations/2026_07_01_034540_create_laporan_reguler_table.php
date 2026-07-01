<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_reguler', function (Blueprint $table) {
            $table->id();
            // Menghubungkan laporan ke id rencana penyaluran infak reguler kamu
            $table->foreignId('penyaluran_reguler_id')->constrained('penyaluran_reguler')->onDelete('cascade');
            
            // Menyimpan semua rincian item nota (tanggal, uraian, nominal, file_path) dalam satu kolom JSON
            $table->json('items_nota'); 
            
            $table->integer('total_pengeluaran');
            $table->integer('selisih_dana');
            $table->string('bukti_pengembalian_sisa')->nullable(); // Opsional jika ada sisa uang
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_reguler');
    }
};