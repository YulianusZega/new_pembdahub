<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: alter ENUM to include new exam types
        if (config('database.default') !== 'sqlite') {
            DB::statement("ALTER TABLE cbt_exams MODIFY COLUMN exam_type ENUM('tugas','quiz','uts','uas','remedial','tryout','test_masuk','ujian_khusus') NOT NULL");
        }
    }

    public function down(): void
    {
        if (config('database.default') !== 'sqlite') {
            DB::statement("ALTER TABLE cbt_exams MODIFY COLUMN exam_type ENUM('tugas','quiz','uts','uas','remedial','tryout') NOT NULL");
        }
    }
};
