<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            // Menambahkan kolom description setelah amount_requested
            $table->text('description')->nullable()->after('amount_requested');
        });
    }

    public function down(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            // Menghapus kolom description jika migration di-rollback
            $table->dropColumn('description');
        });
    }
};