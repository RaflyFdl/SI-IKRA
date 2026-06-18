<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('extra_programs', function (Blueprint $table) {
            // Menambahkan kolom baru setelah image_path dan sifatnya nullable (boleh kosong)
            $table->string('documentation_path')->nullable()->after('image_path');
        });
    }

    public function down()
    {
        Schema::table('extra_programs', function (Blueprint $table) {
            $table->dropColumn('documentation_path');
        });
    }
};
