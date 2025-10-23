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
        // Projects table
        Schema::create('time_tracker_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tasks table
        Schema::create('time_tracker_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('time_tracker_projects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('estimated_duration')->nullable()->comment('Estimated duration in seconds');
            $table->timestamps();
            $table->softDeletes();
        });

        // Time entries table
        Schema::create('time_tracker_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('time_tracker_tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('stopped_at')->nullable();
            $table->integer('duration')->default(0)->comment('Duration in seconds');
            $table->boolean('is_running')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_tracker_entries');
        Schema::dropIfExists('time_tracker_tasks');
        Schema::dropIfExists('time_tracker_projects');
    }
};
