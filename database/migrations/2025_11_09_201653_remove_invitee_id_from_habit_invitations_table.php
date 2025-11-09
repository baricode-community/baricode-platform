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
        Schema::table('habit_invitations', function (Blueprint $table) {
            $table->dropForeign(['invitee_id']);
            $table->dropColumn('invitee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habit_invitations', function (Blueprint $table) {
            $table->foreignId('invitee_id')->constrained('users')->onDelete('cascade');
        });
    }
};
