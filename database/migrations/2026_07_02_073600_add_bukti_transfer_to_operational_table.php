<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('operational_requests', function (Blueprint $table) {
            // Menambahkan kolom bukti_transfer setelah status_keuangan
            $table->string('bukti_transfer')->nullable()->after('status_keuangan');
        });
    }

    public function down()
    {
        Schema::table('operational_requests', function (Blueprint $table) {
            $table->dropColumn('bukti_transfer');
        });
    }
};