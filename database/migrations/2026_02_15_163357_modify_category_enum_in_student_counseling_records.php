<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('student_counseling_records', function (Blueprint $table) {
            // Re-define ENUM with all possible values from Masalah & Prestasi
            $table->enum('category', [
                // Masalah
                'perilaku', 
                'kedisiplinan', 
                'absensi', 
                'akademik', 
                'sosial', 
                'pribadi', 
                
                // Prestasi
                'olahraga', 
                'seni', 
                'keagamaan', 
                'karir',

                // Umum
                'lainnya'
            ])->default('lainnya')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('student_counseling_records', function (Blueprint $table) {
            // Revert to original limited set (might cause data loss if new values used)
            $table->enum('category', [
                'akademik', 'perilaku', 'sosial', 'karir', 'pribadi', 'lainnya'
            ])->default('lainnya')->change();
        });
    }
};
