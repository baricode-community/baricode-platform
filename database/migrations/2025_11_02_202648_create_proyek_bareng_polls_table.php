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
        Schema::create('proyek_bareng_polls', function (Blueprint $table) {
            $table->string('proyek_bareng_id', 5);
            $table->unsignedBigInteger('poll_id');
            $table->string('title');
            $table->text('description')->nullable();

            $table->foreign('proyek_bareng_id')->references('id')->on('proyek_bareng')->onDelete('cascade');
            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
            
            $table->unique(['proyek_bareng_id', 'poll_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_bareng_polls');
    }
};
