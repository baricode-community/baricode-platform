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
        Schema::table('meets', function (Blueprint $table) {
            // Change youtube_link from text to string and make it nullable
            $table->string('youtube_link')->nullable()->change();
            
            // Add is_finished column
            $table->boolean('is_finished')->default(false)->after('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meets', function (Blueprint $table) {
            // Revert youtube_link back to text and not nullable
            $table->text('youtube_link')->change();
            
            // Drop is_finished column
            $table->dropColumn('is_finished');
        });
    }
};
