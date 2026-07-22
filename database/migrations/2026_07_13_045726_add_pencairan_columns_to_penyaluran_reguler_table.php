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
        Schema::table('penyaluran_reguler', function (Blueprint $table) {
            $table->string('bukti_transfer')->nullable()->after('catatan_pembina');
            $table->string('bukti_transfer_path')->nullable()->after('bukti_transfer');
            $table->timestamp('dicairkan_at')->nullable()->after('bukti_transfer_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyaluran_reguler', function (Blueprint $table) {
            $table->dropColumn(['bukti_transfer', 'bukti_transfer_path', 'dicairkan_at']);
        });
    }
};
