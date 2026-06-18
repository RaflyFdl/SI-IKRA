<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            // Mengubah tipe kolom status dari ENUM menjadi String (VARCHAR) biasa
            $table->string('status', 50)->change();
        });
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            // Jika di-rollback, kembalikan ke format asal (sesuaikan dengan enum lamamu)
            $table->enum('status', ['pending', 'active'])->change();
        });
    }
};