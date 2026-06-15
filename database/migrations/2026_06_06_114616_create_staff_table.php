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
        // Kita gunakan nama 'staffs' (jamak) agar standar dengan penamaan Laravel
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique(); // Unique artinya email tidak boleh kembar
            $table->string('password');
            
            // Kolom enum untuk mengunci pilihan role kepengurusan
            $table->enum('role', ['admin', 'keuangan', 'operasional', 'pembina']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};