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
    Schema::create('enrollment_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_module_id')->constrained('enrollment_modules')->onDelete('cascade');
            $table->foreignId('course_module_lesson_id')->constrained('course_module_lessons')->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('enrollment_lessons');
    }
};
