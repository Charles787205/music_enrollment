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
            $table->unsignedBigInteger('teacher_id')->nullable()->after('instructor');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
            
            // Add some missing fields for consistency
            $table->string('name')->nullable()->after('title'); // alias for title
            $table->integer('duration_weeks')->nullable()->after('end_date');
            $table->decimal('fee', 8, 2)->nullable()->after('price'); // alias for price
            $table->text('prerequisites')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn([
                'teacher_id',
                'name',
                'duration_weeks', 
                'fee',
                'prerequisites'
            ]);
        });
    }
};
