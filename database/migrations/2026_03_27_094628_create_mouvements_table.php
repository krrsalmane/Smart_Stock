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
        Schema::create('mouvements', function (Blueprint $table) {
    $table->id();
    $table->enum('type', ['IN', 'OUT', 'ADJ']);
    $table->integer('quantity');
    $table->string('note')->nullable(); 
    
    // Foreign Keys
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->foreignId('command_id')->nullable()->constrained()->onDelete('set null');
    $table->foreignId('user_id')->constrained(); 
    
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements');
    }
};
