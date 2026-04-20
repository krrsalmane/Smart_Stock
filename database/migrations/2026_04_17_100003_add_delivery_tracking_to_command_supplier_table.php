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
        Schema::table('command_supplier', function (Blueprint $table) {
            $table->enum('status', ['pending', 'shipped', 'delivered'])->default('pending')->after('expected_delivery');
            $table->timestamp('shipped_at')->nullable()->after('status');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('command_supplier', function (Blueprint $table) {
            $table->dropColumn(['status', 'shipped_at', 'delivered_at']);
        });
    }
};
