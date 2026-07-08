<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel sesi meeting (satu record per kali guru mulai kelas live)
        Schema::create('lms_meeting_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->foreignId('started_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedSmallInteger('total_attendees')->default(0);
            $table->timestamps();
        });

        // Tabel kehadiran siswa per sesi
        Schema::create('lms_meeting_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('lms_meeting_sessions')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('lms_courses')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->timestamps();

            // Satu siswa hanya bisa satu record per sesi
            $table->unique(['session_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_meeting_attendances');
        Schema::dropIfExists('lms_meeting_sessions');
    }
};
