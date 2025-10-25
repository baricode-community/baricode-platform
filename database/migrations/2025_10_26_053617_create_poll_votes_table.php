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
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->string('id', 5)->primary();
            $table->string('poll_option_id', 5);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->foreign('poll_option_id')
                ->references('id')
                ->on('poll_options')
                ->onDelete('cascade');
                
            $table->unique(['user_id', 'poll_option_id']); // Ensure one vote per option per user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
    }
};
