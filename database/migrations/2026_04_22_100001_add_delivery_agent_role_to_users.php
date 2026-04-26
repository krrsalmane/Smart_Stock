<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the role enum to include 'delivery_agent'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'magasinier', 'client', 'supplier', 'delivery_agent') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove delivery_agent role (ensure no users have this role first)
        DB::table('users')->where('role', 'delivery_agent')->update(['role' => 'client']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'magasinier', 'client', 'supplier') NOT NULL");
    }
};
