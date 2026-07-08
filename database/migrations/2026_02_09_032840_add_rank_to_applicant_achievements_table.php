<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applicant_achievements', function (Blueprint $table) {
            // Tambah kolom untuk jenis prestasi
            $table->string('achievement_type', 50)->nullable()->after('achievement_name');
            // Tambah kolom organizer
            $table->string('organizer', 255)->nullable()->after('year');
            // Tambah kolom untuk juara (1, 2, 3, harapan_1, dll)
            $table->string('rank', 20)->nullable()->after('level');
        });
        
        // Rename kolom - use Schema for SQLite compatibility
        Schema::table('applicant_achievements', function (Blueprint $table) {
            $table->renameColumn('level', 'achievement_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback rename
        Schema::table('applicant_achievements', function (Blueprint $table) {
            $table->renameColumn('achievement_level', 'level');
        });
        
        Schema::table('applicant_achievements', function (Blueprint $table) {
            $table->dropColumn(['rank', 'achievement_type', 'organizer']);
        });
    }
};
