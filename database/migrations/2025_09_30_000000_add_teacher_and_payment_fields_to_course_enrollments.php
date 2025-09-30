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
            $table->unsignedBigInteger('teacher_id')->nullable()->after('course_id');
            $table->decimal('total_fee', 10, 2)->nullable()->after('notes');
            $table->decimal('amount_paid', 10, 2)->default(0)->after('total_fee');
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending')->after('amount_paid');
            $table->timestamp('payment_due_date')->nullable()->after('payment_status');
            $table->unsignedBigInteger('collected_by')->nullable()->after('payment_due_date')->comment('Employee who collected payment');
            $table->timestamp('payment_collected_at')->nullable()->after('collected_by');
            $table->text('payment_notes')->nullable()->after('payment_collected_at');
            
            // Foreign key constraints
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('collected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['collected_by']);
            $table->dropColumn([
                'teacher_id',
                'total_fee',
                'amount_paid',
                'payment_status',
                'payment_due_date',
                'collected_by',
                'payment_collected_at',
                'payment_notes'
            ]);
        });
    }
};