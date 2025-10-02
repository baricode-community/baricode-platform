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
        Schema::table('personal_flash_cards', function (Blueprint $table) {
            $table->boolean('is_skipped')->default(false)->comment('Menandakan flashcard dilewati saat play');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_flash_cards', function (Blueprint $table) {
            $table->dropColumn('is_skipped');
        });
    }
};
