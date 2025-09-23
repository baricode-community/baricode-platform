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
    Schema::create('course_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->date('absent_date');
            $table->enum('status', ['Masuk', 'Bolos', 'Izin'])->default('Masuk');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Composite unique key untuk mencegah duplikasi absensi per hari
            $table->unique(['course_id', 'student_id', 'absent_date']);
            
            // Index untuk performa query
            $table->index(['course_id', 'absent_date']);
            $table->index(['student_id', 'absent_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('course_attendances');
    }
};