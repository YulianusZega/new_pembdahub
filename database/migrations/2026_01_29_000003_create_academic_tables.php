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
        // ================================================================
        // 3. AKADEMIK
        // ================================================================

        // Tabel Mata Pelajaran
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('major_id')->nullable()->constrained('majors')->onDelete('set null');
            $table->string('subject_code', 20)->nullable();
            $table->string('subject_name', 100)->nullable();
            $table->tinyInteger('kkm')->default(75)->comment('Kriteria Ketuntasan Minimal (default 75)');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->index('school_id');
            $table->index('major_id');
        });

        // Tabel Kelas/Rombongan Belajar
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('major_id')->nullable()->constrained('majors')->onDelete('set null');
            $table->string('class_code', 20)->comment('VII-A, X-IPA-1, X-TKJ, dll');
            $table->string('class_name', 100);
            $table->tinyInteger('grade_level')->comment('7,8,9 untuk SMP; 10,11,12 untuk SMA/SMK');
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->integer('capacity')->nullable()->comment('NULL = no limit (sesuai BR: tidak ada batasan max siswa)');

            $table->index('school_id');
            $table->index('academic_year_id');
            $table->index('grade_level');
        });

        // Tabel Penempatan Siswa di Kelas
        Schema::create('student_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            if (\DB::connection()->getDriverName() === 'sqlite') {
                $table->string('status', 30)->default('aktif');
            } else {
                $table->enum('status', ['aktif', 'pindah', 'keluar', 'naik', 'tinggal', 'lulus'])->default('aktif');
            }
            $table->timestamp('promoted_at')->nullable()->comment('Waktu naik kelas/lulus (Juni)');

            $table->unique(['student_id', 'academic_year_id']);
            $table->index(['student_id', 'classroom_id']);
        });

        // Tabel Jadwal Pelajaran
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->tinyInteger('day_of_week')->nullable()->comment('1=Senin, 2=Selasa, ..., 7=Minggu');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('room', 50)->nullable()->comment('Nama/nomor ruangan');

            $table->index('classroom_id');
            $table->index('teacher_id');
            $table->index('day_of_week');
        });

        // Tabel Absensi
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha']);
            $table->text('notes')->nullable()->comment('Catatan tambahan (opsional)');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->unique(['student_id', 'schedule_id', 'date']);
            $table->index('date');
            $table->index('student_id');
            $table->index('status');
        });

        // Tabel Nilai (Input per jenis: Tugas, UTS, UAS, Sikap)
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->enum('grade_type', ['tugas', 'uts', 'uas', 'sikap']);
            $table->decimal('score', 5, 2)->comment('Nilai 0-100');
            $table->boolean('is_remedial')->default(false)->comment('TRUE jika nilai remedial');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('student_id');
            $table->index('subject_id');
            $table->index('semester_id');
        });

        // Tabel Nilai Akhir (Auto-calculate dari grades)
        Schema::create('final_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->decimal('tugas_score', 5, 2)->default(0);
            $table->decimal('uts_score', 5, 2)->default(0);
            $table->decimal('uas_score', 5, 2)->default(0);
            $table->decimal('sikap_score', 5, 2)->default(0);
            $table->decimal('final_score', 5, 2)->nullable()->comment('Tugas*20% + UTS*30% + UAS*40% + Sikap*10%');
            $table->boolean('is_passed')->nullable()->comment('TRUE jika final_score >= KKM (75)');
            $table->enum('predicate', ['A', 'B', 'C', 'D'])->nullable()->comment('A: 90-100, B: 80-89, C: 75-79, D: <75');
            $table->text('description')->nullable()->comment('Deskripsi nilai (untuk raport)');
            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'semester_id']);
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_grades');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('student_classes');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('subjects');
    }
};
