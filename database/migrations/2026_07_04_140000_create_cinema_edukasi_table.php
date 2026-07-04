<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cinema_edukasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_materi');                        // Judul/nama program cinema
            $table->string('pengajar');                           // Nama pembawa materi / pengajar
            $table->text('penerima_manfaat');                     // Audiens (misal: siswa SD kelas 3)
            $table->datetime('jadwal_kegiatan');                  // Tanggal & waktu pelaksanaan
            $table->decimal('amount_requested', 15, 2);          // Estimasi anggaran
            $table->text('description')->nullable();              // Rincian biaya
            $table->string('status')->default('requested');       // requested / dicairkan / selesai
            $table->bigInteger('extra_program_id')->unsigned()->nullable(); // Terhubung ke extra_programs
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cinema_edukasi');
    }
};
