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
        // Rename table and add new columns
        Schema::rename('group_whatsapps', 'whatsapp_groups');
        
        Schema::table('whatsapp_groups', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('group_id');
            $table->foreignId('created_by')->nullable()->after('is_active')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_groups', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['is_active', 'created_by']);
        });
        
        Schema::rename('whatsapp_groups', 'group_whatsapps');
    }
};
