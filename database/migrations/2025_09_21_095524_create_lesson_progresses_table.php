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
    Schema::create('lesson_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_progress_id')->constrained('module_progresses')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lesson_details')->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('lesson_progresses');
    }
};
