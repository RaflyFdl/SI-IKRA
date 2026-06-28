<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambah kolom.
     */
    public function up(): void
    {
        Schema::table('extra_programs', function (Blueprint $table) {
            // Membuat kolom sisa_dana dengan tipe data angka, nilai awal 0, dan posisinya setelah current_amount
            $table->integer('sisa_dana')->default(0)->after('current_amount');
        });
    }

    /**
     * Batalkan migrasi jika terjadi kesalahan.
     */
    public function down(): void
    {
        Schema::table('extra_programs', function (Blueprint $table) {
            $table->dropColumn('sisa_dana');
        });
    }
};