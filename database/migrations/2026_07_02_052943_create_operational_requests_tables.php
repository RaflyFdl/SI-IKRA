<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Utama Pengajuan
        Schema::create('operational_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: "Kebutuhan Operasional Kantor Juli 2026"
            $table->decimal('total_amount', 15, 2)->default(0);
            
            // Alur Status: pending -> approved_pembina -> dicairkan -> dilaporkan
            $table->string('status_pembina')->default('pending'); 
            $table->string('status_keuangan')->default('pending'); 
            
            // Kolom Pelaporan Realisasi
            $table->text('realization_report')->nullable();
            $table->string('realization_proof_path')->nullable(); 
            $table->timestamp('reported_at')->nullable();
            
            $table->timestamps();
        });

        // 2. Tabel Multi-Item Detail Kebutuhan
        Schema::create('operational_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_request_id')->constrained('operational_requests')->onDelete('cascade');
            $table->string('description'); // Misal: "Tagihan Listrik", "Sewa Kantor"
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_request_items');
        Schema::dropIfExists('operational_requests');
    }
};