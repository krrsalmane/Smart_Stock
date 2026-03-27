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
        Schema::create('commands', function (Blueprint $table) {
    $table->id();
    $table->enum('status', ['pending', 'approved', 'received', 'cancelled'])->default('pending');
    $table->date('ordered_at');
    $table->double('total_cost')->default(0);
    $table->foreignId('client_id')->constrained('users');
    $table->timestamps();
});

// The Pivot Table (Command - Product)
        Schema::create('command_product', function (Blueprint $table) {
    $table->id();
    $table->foreignId('command_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->integer('quantity'); 
    $table->double('unit_price'); 
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
