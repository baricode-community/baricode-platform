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
        Schema::table('time_tracker_tasks', function (Blueprint $table) {
            $table->boolean('is_completed')->default(false)->after('estimated_duration');
            $table->timestamp('completed_at')->nullable()->after('is_completed');
        });

        Schema::table('time_tracker_projects', function (Blueprint $table) {
            $table->boolean('is_completed')->default(false)->after('description');
            $table->timestamp('completed_at')->nullable()->after('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_tracker_tasks', function (Blueprint $table) {
            $table->dropColumn(['is_completed', 'completed_at']);
        });

        Schema::table('time_tracker_projects', function (Blueprint $table) {
            $table->dropColumn(['is_completed', 'completed_at']);
        });
    }
};
