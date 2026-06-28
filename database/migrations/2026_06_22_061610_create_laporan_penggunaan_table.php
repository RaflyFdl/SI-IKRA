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
        Schema::create('laporan_penggunaan', function (Blueprint $table) {
            $table->id();
            
            // Penanda jenis dana (Sekarang kita fokus ke EKSTRA dulu, tapi tabel ini siap untuk REGULER/OPERASIONAL)
            $table->enum('sumber_dana', ['EKSTRA', 'REGULER', 'OPERASIONAL']);
            
            // ID dari tabel pengajuan terkait (misal: ID dari tabel pengajuan_pencairan_ekstra)
            $table->unsignedBigInteger('pengajuan_id'); 
            
            // Total akumulasi pengeluaran dari nota-nota
            $table->integer('total_terpakai')->default(0);
            
            // Selisih = (Dana Dicairkan - Total Terpakai)
            // Nilai POSITIF (+) = Ada Sisa Uang (Contoh: +200000)
            // Nilai NEGATIF (-) = Tekor/Kurang Uang (Contoh: -100000)
            $table->integer('selisih')->default(0);
            
            // Bukti transfer balik ke yayasan jika ada sisa uang (nullable karena diisi kalau sisa saja)
            $table->string('bukti_pengembalian')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi jika terjadi kesalahan.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_penggunaan');
    }
};