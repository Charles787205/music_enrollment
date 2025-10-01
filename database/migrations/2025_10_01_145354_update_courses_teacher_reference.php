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
        Schema::table('courses', function (Blueprint $table) {
            // Drop the existing foreign key constraint if it exists
            $table->dropForeign(['teacher_id']);
            
            // Recreate the foreign key to reference teachers table
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop the teachers foreign key
            $table->dropForeign(['teacher_id']);
            
            // Recreate the users foreign key
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
