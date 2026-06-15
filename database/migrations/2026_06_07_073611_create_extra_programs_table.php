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
    Schema::create('extra_programs', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('target_amount', 15, 2);
        $table->decimal('current_amount', 15, 2)->default(0.00);
        $table->date('end_date');
        $table->string('image_path')->nullable();
        $table->string('va_number')->nullable();
        $table->string('external_id')->unique()->nullable();
        $table->enum('status', ['active', 'completed', 'expired'])->default('active');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_programs');
    }
};
