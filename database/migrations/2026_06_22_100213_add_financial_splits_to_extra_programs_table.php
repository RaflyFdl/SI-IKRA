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
        Schema::table('extra_programs', function (Blueprint $table) {
            // Menambahkan kantong dana bersih (65%) setelah current_amount
            $table->bigInteger('dana_bersih_ekstra')->default(0)->after('current_amount');
            
            // Menambahkan kantong dana operasional (35%) setelah dana_bersih_ekstra
            $table->bigInteger('dana_operasional_ekstra')->default(0)->after('dana_bersih_ekstra');
        });
    }

    public function down(): void
    {
        Schema::table('extra_programs', function (Blueprint $table) {
            $table->dropColumn(['dana_bersih_ekstra', 'dana_operasional_ekstra']);
        });
    }
};
