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
        Schema::create('extra_program_payments', function (Blueprint $table) {
            $table->id();
            // Mengunci ke ID Program Infak
            $table->foreignId('extra_program_id')->constrained('extra_programs')->onDelete('cascade');
            // Mengunci ke ID Anggota/User yang bayar
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            $table->string('xendit_payment_id')->unique();
            $table->string('external_id');
            $table->decimal('amount', 15, 2);
            $table->string('bank_code');
            $table->string('account_number');
            $table->timestamp('payment_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_program_payments');
    }
};