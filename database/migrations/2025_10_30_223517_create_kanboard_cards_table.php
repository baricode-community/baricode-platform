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
        Schema::create('kanboard_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kanboard_id')->constrained('kanboards')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['todo', 'doing', 'done'])->default('todo');
            $table->integer('order')->default(0); // For sorting cards within status
            $table->string('color')->nullable(); // Hex color for card background
            $table->json('labels')->nullable(); // Array of labels/tags
            $table->timestamp('due_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['kanboard_id', 'status', 'order']);
            $table->index(['kanboard_id', 'is_archived']);
            $table->index(['assigned_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanboard_cards');
    }
};
