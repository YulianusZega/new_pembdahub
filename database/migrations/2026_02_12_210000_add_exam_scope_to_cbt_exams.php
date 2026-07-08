<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add exam_scope column to distinguish school-level vs class-level exams.
 *
 * school: UAS, UTS, Test Masuk, Ujian Khusus - managed by Super Admin / Admin Sekolah
 * class: Quiz, Tugas, Remedial - managed by individual teachers for their own classes
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cbt_exams', function (Blueprint $table) {
            $table->enum('exam_scope', ['school', 'class'])->default('class')
                ->after('exam_type')
                ->comment('school=ujian sekolah (admin), class=ujian kelas (guru)');
            $table->index('exam_scope', 'idx_ce_exam_scope');
        });

        // Update existing exams: set school-level for UAS/UTS/tryout
        \DB::table('cbt_exams')
            ->whereIn('exam_type', ['uas', 'uts', 'tryout'])
            ->update(['exam_scope' => 'school']);
    }

    public function down(): void
    {
        Schema::table('cbt_exams', function (Blueprint $table) {
            $table->dropIndex('idx_ce_exam_scope');
            $table->dropColumn('exam_scope');
        });
    }
};
