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
        Schema::create('laporan_penggunaan_detail', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel induk laporan_penggunaan di Langkah 3
            // Jika data induk dihapus, detail nota otomatis ikut terhapus (cascade)
            $table->foreignId('laporan_penggunaan_id')->constrained('laporan_penggunaan')->onDelete('cascade');
            
            $table->date('tanggal'); // Tanggal pengeluaran di nota
            $table->text('uraian');  // Penjelasan rinci dana dibelanjakan untuk apa
            $table->integer('nominal'); // Nominal pengeluaran di nota
            $table->string('bukti_nota'); // Nama file/path foto nota belanjaan
            
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi jika terjadi kesalahan.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_penggunaan_detail');
    }
};