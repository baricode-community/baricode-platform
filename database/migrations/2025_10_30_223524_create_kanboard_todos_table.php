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
        Schema::create('kanboard_todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kanboard_card_id')->constrained('kanboard_cards')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->integer('order')->default(0); // For sorting todos within card
            $table->timestamp('due_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['kanboard_card_id', 'is_completed', 'order']);
            $table->index(['assigned_to', 'is_completed']);
            $table->index(['created_by', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanboard_todos');
    }
};
