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
        Schema::create('kanboard_todo_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kanboard_todo_id')->constrained('kanboard_todos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination of todo and user
            $table->unique(['kanboard_todo_id', 'user_id']);
            
            // Indexes for performance
            $table->index(['kanboard_todo_id', 'assigned_at']);
            $table->index(['user_id', 'assigned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanboard_todo_users');
    }
};
