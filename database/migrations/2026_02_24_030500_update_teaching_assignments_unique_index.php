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
        // 1. Fill NULL semester_id with default (1)
        DB::table('teaching_assignments')
            ->whereNull('semester_id')
            ->update(['semester_id' => 1]);

        Schema::table('teaching_assignments', function (Blueprint $table) {
            // 2. Drop the old unique index
            // The constraint name as seen in the migration and error is 'unique_teaching_assignment'
            $table->dropUnique('unique_teaching_assignment');

            // 3. Create the new unique index including semester_id
            $table->unique(['teacher_id', 'subject_id', 'classroom_id', 'academic_year_id', 'semester_id'], 'unique_teaching_assignment_semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teaching_assignments', function (Blueprint $table) {
            $table->dropUnique('unique_teaching_assignment_semester');
            $table->unique(['teacher_id', 'subject_id', 'classroom_id', 'academic_year_id'], 'unique_teaching_assignment');
        });
    }
};
