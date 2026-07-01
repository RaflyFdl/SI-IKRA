<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyaluran_reguler', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program'); // Contoh: Santunan Anak Yatim Juni
            $table->decimal('nominal_diajukan', 15, 2); // Jumlah dana yang diminta
            $table->text('rincian_detail'); // Detail penggunaan dana
            $table->string('penerima_manfaat'); // Target penerima manfaat
            $table->date('tanggal_pelaksanaan'); // Kapan dilaksanakan
            $table->string('periode_bulan', 7); // Kunci Periode (Format: YYYY-MM, contoh: '2026-06')
            
            // Status alur sistem: 'pending', 'disetujui_pembina', 'dicairkan', 'dilaporkan'
            $table->string('status')->default('pending'); 

            $table->timestamp('disetujui_pembina_at')->nullable(); // Waktu disetujui pembina
            $table->text('rincian_realisasi')->nullable(); // Detail belanja riil setelah acara
            $table->string('file_nota')->nullable(); // Path foto nota / bukti pendukung
            $table->timestamp('laporan_diterima_keuangan_at')->nullable(); // Waktu keuangan terima LPJ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyaluran_reguler');
    }
};