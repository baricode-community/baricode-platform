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
        Schema::table('daily_quotes', function (Blueprint $table) {
            $table->unsignedBigInteger('whatsapp_group_id')->after('id');
            $table->foreign('whatsapp_group_id')->references('id')->on('whatsapp_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_quotes', function (Blueprint $table) {
            $table->dropForeign(['whatsapp_group_id']);
            $table->dropColumn('whatsapp_group_id');
        });
    }
};
