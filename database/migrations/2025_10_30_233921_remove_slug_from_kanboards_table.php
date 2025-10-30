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
        // For SQLite, we need to drop the unique index first
        Schema::table('kanboards', function (Blueprint $table) {
            $table->dropIndex('kanboards_slug_unique');
        });
        
        Schema::table('kanboards', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kanboards', function (Blueprint $table) {
            $table->string('slug')->unique()->after('title');
        });
    }
};
