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
        Schema::table('tasks', function (Blueprint $table) {
            // Remove old columns that are no longer needed
            $table->dropForeign(['assigned_by']);
            $table->dropColumn(['assigned_by', 'approved_at']);
            
            // Add new columns
            $table->boolean('is_active')->default(true)->after('content');
            $table->integer('max_submissions_per_user')->default(1)->after('is_active');
            $table->json('attachments')->nullable()->after('max_submissions_per_user');
            $table->text('instructions')->nullable()->after('attachments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Restore old columns
            $table->foreignId('assigned_by')->constrained('users')->onDelete('set null')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            // Remove new columns
            $table->dropColumn(['is_active', 'max_submissions_per_user', 'attachments', 'instructions']);
        });
    }
};
