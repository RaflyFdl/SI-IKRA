<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dana_backup', function (Blueprint $col) {
            $col->id();
            $col->string('sumber_dana')->default('EKSTRA'); // Pencatat asal dana
            $col->unsignedBigInteger('pengajuan_id')->nullable(); // Relasi ke pengajuan terkait
            $col->bigInteger('nominal'); // Jumlah sisa dana yang masuk backup
            $col->string('bukti_transfer')->nullable(); // Wadah simpan path gambar pengembalian sisa uang
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dana_backup');
    }
};