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
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            
            // Data Jadwal & Kreatif
            $table->string('title'); // Judul episode
            $table->text('topic'); // Topik pembahasan
            $table->string('guest_star'); // Narasumber
            $table->string('host'); // Host
            $table->dateTime('taping_date'); // Jadwal rekaman
            $table->dateTime('airing_date'); // Jadwal tayang
            
            // Data Keuangan Awal
            $table->decimal('amount_requested', 12, 2)->default(0); // Dana yang diminta
            $table->decimal('amount_approved', 12, 2)->default(0); // Dana yang dicairkan keuangan
            $table->string('disbursement_proof')->nullable(); // Bukti transfer keuangan
            
            // Status Progress Perjalanan Podcast (Progress Bar)
            // 1: Jadwal Dibuat/Draft, 2: Dana Diajukan, 3: Dana Dicairkan, 4: LPJ Selesai
            $table->enum('status', ['draft', 'requested', 'disbursed', 'completed'])->default('draft');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
};
