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
        // Add new columns to commands table
        Schema::table('commands', function (Blueprint $table) {
            if (!Schema::hasColumn('commands', 'notes')) {
                $table->text('notes')->nullable()->after('expected_at');
            }
            if (!Schema::hasColumn('commands', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('expected_at');
            }
            if (!Schema::hasColumn('commands', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
        });

        // Add new columns to suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'status')) {
                $table->string('status')->default('active')->after('address');
            }
            if (!Schema::hasColumn('suppliers', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0)->after('status');
            }
        });

        // Add tracking columns to command_supplier pivot table
        Schema::table('command_supplier', function (Blueprint $table) {
            if (!Schema::hasColumn('command_supplier', 'delivery_date')) {
                $table->timestamp('delivery_date')->nullable()->after('delivered_at');
            }
            if (!Schema::hasColumn('command_supplier', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('delivery_date');
            }
            if (!Schema::hasColumn('command_supplier', 'notes')) {
                $table->text('notes')->nullable()->after('tracking_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->dropColumn(['notes', 'cancelled_at', 'cancellation_reason']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['status', 'rating']);
        });

        Schema::table('command_supplier', function (Blueprint $table) {
            $table->dropColumn(['delivery_date', 'tracking_number', 'notes']);
        });
    }
};
