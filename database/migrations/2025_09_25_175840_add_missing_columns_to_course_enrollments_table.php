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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('course_enrollments', 'grade')) {
                $table->decimal('grade', 5, 2)->nullable()->after('completed_at');
            }
            if (!Schema::hasColumn('course_enrollments', 'notes')) {
                $table->text('notes')->nullable()->after('grade');
            }
            
            // Ensure enrolled_at and completed_at are nullable
            $table->date('enrolled_at')->nullable()->change();
            $table->date('completed_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropColumn(['grade', 'notes']);
        });
    }
};
