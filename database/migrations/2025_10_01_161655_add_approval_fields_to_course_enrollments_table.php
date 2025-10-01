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
        Schema::table('course_enrollments', function (Blueprint $table) {
            // Add approved_at timestamp field
            $table->timestamp('approved_at')->nullable()->after('enrolled_at');
            
            // Update status enum to include approval statuses
            $table->enum('status', ['pending', 'enrolled', 'approved', 'rejected', 'completed', 'dropped'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            // Remove approved_at field
            $table->dropColumn('approved_at');
            
            // Revert status enum to original values
            $table->enum('status', ['enrolled', 'completed', 'dropped', 'pending'])->default('enrolled')->change();
        });
    }
};
