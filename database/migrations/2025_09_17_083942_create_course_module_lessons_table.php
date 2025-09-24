<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('course_module_lessons', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('module_id');
        $table->foreign('module_id')->references('id')->on('course_modules')->onDelete('cascade');
        $table->string('title');
        $table->text('content')->nullable();
        $table->integer('order')->default(-1);
        $table->timestamps();
    });
    }

    public function down(): void
    {
    Schema::dropIfExists('course_module_lessons');
    }
};
