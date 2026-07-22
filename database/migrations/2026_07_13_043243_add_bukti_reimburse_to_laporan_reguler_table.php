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
        Schema::table('laporan_reguler', function (Blueprint $table) {
            // Path file bukti transfer reimburse dari keuangan ke operasional (jika ada kurang dana)
            $table->string('bukti_reimburse')->nullable()->after('bukti_pengembalian_sisa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_reguler', function (Blueprint $table) {
            $table->dropColumn('bukti_reimburse');
        });
    }
};
