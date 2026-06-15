<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // Ditambahkan: Jenis transaksi untuk mempermudah filter laporan keuangan
            $table->enum('transaction_type', ['reguler', 'ekstra'])->default('reguler');
            
            // Diubah menjadi nullable: Karena Infak Ekstra tidak terikat ke 1 member khusus di tabel members
            $table->unsignedBigInteger('member_id')->nullable(); 
            
            // Ditambahkan: Menghubungkan transaksi dengan id program di tabel extra_programs
            $table->unsignedBigInteger('extra_program_id')->nullable();

            $table->string('external_id'); // ID referensi transaksi unik dari Xendit
            $table->decimal('amount', 12, 2); // Nominal infak (Reguler atau Ekstra)
            $table->string('bank_code'); // Mengunci kode bank (MUAMALAT)
            $table->string('account_number'); // Nomor VA Muamalat yang dibayar
            $table->string('payment_id')->nullable(); // ID bukti bayar sukses dari Xendit
            
            // Diubah menjadi nullable: Karena Infak Ekstra tidak memiliki periode tagihan bulanan
            $table->string('periode')->nullable(); // Contoh: '2026-06'
            
            $table->timestamps();

            // Relasi foreign key:
            // Jika data member dihapus, riwayat transaksi ikut terhapus
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            
            // Jika program ekstra dihapus, kolom extra_program_id di transaksi diset NULL agar riwayat keuangan tidak hilang
            $table->foreign('extra_program_id')->references('id')->on('extra_programs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};