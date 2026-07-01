<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extra_programs', function (Blueprint $table) {
            // Menyuntikkan kolom execution_date setelah end_date
            $table->date('execution_date')->nullable()->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('extra_programs', function (Blueprint $table) {
            $table->dropColumn('execution_date');
        });
    }
};