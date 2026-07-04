<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel Utama Laporan Operasional
        Schema::create('operational_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_request_id')->constrained('operational_requests')->onDelete('cascade');
            $table->decimal('total_realization', 15, 2); // Total uang rill yang terpakai belanja
            $table->decimal('selisih', 15, 2);           // Positif = Sisa (Refund), Negatif = Kurang (Reimburse)
            $table->string('status_keuangan')->default('pending'); // pending, selesai_refund, selesai_reimburse
            $table->string('nota_global')->nullable();    // File foto/scan bukti nota gabungan
            $table->timestamps();
        });

        // 2. Tabel Detail Item Belanja Realisasi
        Schema::create('operational_report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_report_id')->constrained('operational_reports')->onDelete('cascade');
            $table->string('description');
            $table->decimal('amount_realization', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('operational_report_details');
        Schema::dropIfExists('operational_reports');
    }
};