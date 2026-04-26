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
        // Remove the CASCADE foreign key so archives persist after product deletion
        Schema::table('archives', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            // Don't add foreign key back - allow product_id to reference deleted products
            // This preserves the archive even after the product is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the CASCADE constraint
        Schema::table('archives', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }
};
