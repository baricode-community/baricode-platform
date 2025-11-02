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
        Schema::table('proyek_bareng_polls', function (Blueprint $table) {
            $table->unsignedBigInteger('poll_id')->after('proyek_bareng_id');
            $table->string('title')->after('poll_id');
            $table->text('description')->nullable()->after('title');

            $table->foreign('proyek_bareng_id')->references('id')->on('proyek_bareng')->onDelete('cascade');
            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
            
            $table->unique(['proyek_bareng_id', 'poll_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyek_bareng_polls', function (Blueprint $table) {
            $table->dropForeign(['proyek_bareng_id']);
            $table->dropForeign(['poll_id']);
            $table->dropUnique(['proyek_bareng_id', 'poll_id']);
            $table->dropColumn(['proyek_bareng_id', 'poll_id', 'title', 'description']);
        });
    }
};
