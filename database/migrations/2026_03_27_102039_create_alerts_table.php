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
        Schema::create('alerts', function (Blueprint $table) {
         $table->id();
         $table->string('status'); // e.g., 'active', 'dismissed'
         $table->timestamp('triggered_at');
         $table->enum('type', ['LOW_STOCK', 'DISCREPANCY', 'ADJUSTMENT']);
    
    // Foreign Key to Product
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
         $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
