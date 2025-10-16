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
        Schema::table('task_assignments', function (Blueprint $table) {
            // Drop unique constraint untuk allow multiple assignments per user pada task yang sama
            $table->dropUnique(['task_id', 'user_id']);
            
            // Add title untuk membedakan assignments
            $table->string('title')->nullable()->after('task_id');
            
            // Add description untuk assignment specifics
            $table->text('description')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_assignments', function (Blueprint $table) {
            // Restore unique constraint
            $table->unique(['task_id', 'user_id']);
            
            // Remove added columns
            $table->dropColumn(['title', 'description']);
        });
    }
};
