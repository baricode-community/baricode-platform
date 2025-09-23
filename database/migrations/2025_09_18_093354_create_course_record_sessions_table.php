<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_record_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_enrollment_id');
            $table->unsignedTinyInteger('day_of_week')->comment('1=Ahad, 2=Senin, 3=Selasa, 4=Rabu, 5=Kamis, 6=Jumat, 7=Sabtu');

            $table->time('reminder_1');
            $table->time('reminder_2')->nullable();
            $table->time('reminder_3')->nullable();

            $table->timestamps();

            $table->foreign('course_enrollment_id')
                ->references('id')->on('course_enrollments')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_record_sessions');
    }
};