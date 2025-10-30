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
        Schema::create('kanboard_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kanboard_id')->constrained('kanboards')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['member', 'manager', 'admin'])->default('member');
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('invited_at')->useCurrent();
            $table->timestamp('joined_at')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->text('permissions')->nullable(); // JSON for specific permissions
            $table->timestamps();
            
            $table->unique(['kanboard_id', 'user_id']);
            $table->index(['kanboard_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanboard_users');
    }
};
