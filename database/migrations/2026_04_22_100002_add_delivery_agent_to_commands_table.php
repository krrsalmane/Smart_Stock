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
        Schema::table('commands', function (Blueprint $table) {
            // Add delivery agent foreign key
            $table->foreignId('delivery_agent_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Add delivery tracking fields
            $table->timestamp('delivery_started_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('current_location')->nullable();
            $table->string('delivery_location')->nullable();
            $table->timestamp('assigned_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->dropForeign(['delivery_agent_id']);
            $table->dropColumn([
                'delivery_agent_id',
                'delivery_started_at',
                'delivered_at',
                'current_location',
                'delivery_location',
                'assigned_at',
            ]);
        });
    }
};
