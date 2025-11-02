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
        Schema::create('proyek_bareng_meets', function (Blueprint $table) {
            $table->id();
            $table->string('proyek_bareng_id', 5);
            $table->unsignedBigInteger('meet_id');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('proyek_bareng_id')->references('id')->on('proyek_bareng')->onDelete('cascade');
            $table->foreign('meet_id')->references('id')->on('meets')->onDelete('cascade');
            
            $table->unique(['proyek_bareng_id', 'meet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_bareng_meets');
    }
};
