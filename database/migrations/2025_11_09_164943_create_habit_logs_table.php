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
        Schema::create('habit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('habit_id', 5);
            $table->foreign('habit_id')->references('id')->on('habits')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('log_date');
            $table->time('log_time');
            $table->enum('status', ['present', 'absent', 'late'])->default('present');
            $table->text('notes')->nullable(); // keterangan dari user
            $table->timestamp('logged_at');
            $table->timestamps();
            
            $table->unique(['habit_id', 'user_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habit_logs');
    }
};
