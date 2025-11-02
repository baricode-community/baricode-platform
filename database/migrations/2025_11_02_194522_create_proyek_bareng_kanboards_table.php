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
        Schema::create('proyek_bareng_kanboards', function (Blueprint $table) {
            $table->id();
            $table->string('proyek_bareng_id', 5);
            $table->unsignedBigInteger('kanboard_id');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('proyek_bareng_id')->references('id')->on('proyek_bareng')->onDelete('cascade');
            $table->foreign('kanboard_id')->references('id')->on('kanboards')->onDelete('cascade');
            
            $table->unique(['proyek_bareng_id', 'kanboard_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_bareng_kanboards');
    }
};
