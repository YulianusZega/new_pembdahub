<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_counseling_records', function (Blueprint $table) {
            $table->enum('achievement_level', ['sekolah', 'kabupaten', 'propinsi', 'nasional', 'internasional'])
                  ->nullable()
                  ->after('category')
                  ->comment('Tingkat prestasi (untuk record_type=penghargaan)');
        });
    }

    public function down(): void
    {
        Schema::table('student_counseling_records', function (Blueprint $table) {
            $table->dropColumn('achievement_level');
        });
    }
};
