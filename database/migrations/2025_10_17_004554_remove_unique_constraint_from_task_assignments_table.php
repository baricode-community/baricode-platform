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
        // Migration ini tidak diperlukan lagi karena create_task_assignments_table
        // sudah dibuat tanpa unique constraint dan sudah include title & description
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada yang perlu di-rollback
    }
};
