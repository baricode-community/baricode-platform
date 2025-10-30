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
        Schema::create('kanboards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->string('board_id')->unique(); // Custom generated ID for sharing
            $table->enum('visibility', ['private', 'public'])->default('private');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Additional settings like background color, etc.
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['owner_id', 'is_active']);
            $table->index('board_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanboards');
    }
};
