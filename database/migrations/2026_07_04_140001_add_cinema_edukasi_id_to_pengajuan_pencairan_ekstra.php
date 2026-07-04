<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_pencairan_ekstra', function (Blueprint $table) {
            // Kolom opsional untuk menghubungkan pengajuan ke jadwal cinema edukasi spesifik
            $table->unsignedBigInteger('cinema_edukasi_id')->nullable()->after('extra_program_id');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_pencairan_ekstra', function (Blueprint $table) {
            $table->dropColumn('cinema_edukasi_id');
        });
    }
};
