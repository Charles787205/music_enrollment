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
        // Rename the enrollments table to instrument_borrows
        Schema::rename('enrollments', 'instrument_borrows');
        
        // Update the enum values to be more appropriate for borrowing
        Schema::table('instrument_borrows', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('instrument_borrows', function (Blueprint $table) {
            $table->enum('status', ['borrowed', 'returned', 'overdue', 'pending'])->default('pending')->after('instrument_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the enum values
        Schema::table('instrument_borrows', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('instrument_borrows', function (Blueprint $table) {
            $table->enum('status', ['active', 'completed', 'dropped', 'pending'])->default('pending')->after('instrument_id');
        });
        
        // Rename back to enrollments
        Schema::rename('instrument_borrows', 'enrollments');
    }
};
