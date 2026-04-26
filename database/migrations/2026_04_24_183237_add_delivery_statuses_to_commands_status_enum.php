<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Expand the status ENUM to include delivery-related statuses.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE commands MODIFY COLUMN status ENUM('pending', 'approved', 'received', 'cancelled', 'in_transit', 'delayed', 'delivered') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE commands MODIFY COLUMN status ENUM('pending', 'approved', 'received', 'cancelled') DEFAULT 'pending'");
    }
};
