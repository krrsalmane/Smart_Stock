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
        Schema::table('archives', function (Blueprint $table) {
            // Add action type to track what triggered the archive
            $table->string('action')->default('snapshot')->after('quantity');
            
            // Add notes for context
            $table->text('notes')->nullable()->after('action');
            
            // Add snapshot data (JSON) to store complete product state
            $table->json('snapshot_data')->nullable()->after('notes');
            
            // Add index for better query performance
            $table->index(['product_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['action']);
            $table->dropColumn(['action', 'notes', 'snapshot_data']);
        });
    }
};
