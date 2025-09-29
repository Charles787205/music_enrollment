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
        Schema::table('instrument_borrows', function (Blueprint $table) {
            $table->timestamp('borrowed_at')->nullable()->after('notes');
            $table->timestamp('due_date')->nullable()->after('borrowed_at');
            $table->timestamp('returned_at')->nullable()->after('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instrument_borrows', function (Blueprint $table) {
            $table->dropColumn(['borrowed_at', 'due_date', 'returned_at']);
        });
    }
};
