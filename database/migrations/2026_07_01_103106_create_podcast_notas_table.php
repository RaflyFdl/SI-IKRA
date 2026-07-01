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
        Schema::create('podcast_notas', function (Blueprint $table) {
            $table->id();
            
            // Menghubungkan nota dengan podcast (Foreign Key)
            // Jika data podcast dihapus, nota di bawahnya otomatis ikut terhapus
            $table->foreignId('podcast_id')->constrained('podcasts')->onDelete('cascade');
            
            // Kolom isi form nota (Kuantitas sudah masuk di sini)
            $table->date('tanggal');
            $table->string('uraian');
            $table->string('kuantitas'); // Contoh: "10 Pcs", "1 Paket"
            $table->decimal('nominal', 12, 2);
            $table->string('bukti_nota'); // Tempat menyimpan path file gambar nota
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('podcast_notas');
    }
};
