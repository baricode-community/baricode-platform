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
        Schema::table('course_attendances', function (Blueprint $table) {
            $table->time('waktu_absensi')->nullable()->after('status')->comment('Waktu pengingat yang memicu pembuatan absensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_attendances', function (Blueprint $table) {
            $table->dropColumn('waktu_absensi');
        });
    }
};
