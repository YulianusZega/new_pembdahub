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
        Schema::create('block_student_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_schedule_id')->constrained('block_schedules')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->char('group', 1); // 'A' or 'B'
            $table->timestamps();

            $table->unique(['block_schedule_id', 'student_id'], 'block_sg_bsid_stid_unique');
            $table->index(['block_schedule_id', 'classroom_id', 'group'], 'block_sg_bsid_clsid_grp_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_student_groups');
    }
};
