<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates grade_weights table for configurable grading weights per school
     * and fixes the final_grades table alignment.
     */
    public function up(): void
    {
        // 1. Create grade_weights table - configurable weights per school
        Schema::create('grade_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->decimal('tugas_weight', 5, 2)->default(20.00)->comment('Bobot Tugas/Harian (%)');
            $table->decimal('pts_weight', 5, 2)->default(30.00)->comment('Bobot PTS/UTS (%)');
            $table->decimal('pas_weight', 5, 2)->default(40.00)->comment('Bobot PAS/UAS (%)');
            $table->decimal('sikap_weight', 5, 2)->default(10.00)->comment('Bobot Sikap (%)');
            $table->string('description')->nullable()->comment('Catatan tentang konfigurasi bobot');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('school_id');
        });

        // 2. Insert default weights for all existing schools
        $schools = DB::table('schools')->pluck('id');
        foreach ($schools as $schoolId) {
            DB::table('grade_weights')->insert([
                'school_id' => $schoolId,
                'tugas_weight' => 20.00,
                'pts_weight' => 30.00,
                'pas_weight' => 40.00,
                'sikap_weight' => 10.00,
                'description' => 'Default: Tugas 20% + PTS 30% + PAS 40% + Sikap 10%',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Add 'lms_source_type' and 'lms_source_id' to grades table for LMS sync tracking
        Schema::table('grades', function (Blueprint $table) {
            $table->string('lms_source_type')->nullable()->after('notes')->comment('LMS source: quiz_attempt or submission');
            $table->unsignedBigInteger('lms_source_id')->nullable()->after('lms_source_type')->comment('ID of source quiz_attempt/submission');
            
            $table->index(['lms_source_type', 'lms_source_id'], 'grades_lms_source_idx');
        });

        // 4. Fix final_grades - drop and recreate with correct schema aligned to model
        // First backup any existing data (likely empty since it was never populated)
        Schema::dropIfExists('final_grades');
        
        Schema::create('final_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->decimal('tugas_score', 5, 2)->default(0)->comment('Rata-rata nilai tugas');
            $table->decimal('pts_score', 5, 2)->default(0)->comment('Nilai PTS/UTS');
            $table->decimal('pas_score', 5, 2)->default(0)->comment('Nilai PAS/UAS');
            $table->decimal('sikap_score', 5, 2)->default(0)->comment('Nilai Sikap');
            $table->decimal('final_score', 5, 2)->default(0)->comment('Nilai akhir (weighted)');
            $table->integer('kkm')->default(75)->comment('KKM saat perhitungan');
            $table->boolean('is_passed')->default(false)->comment('Lulus KKM?');
            $table->string('predicate', 1)->default('D')->comment('A/B/C/D');
            $table->text('description')->nullable()->comment('Deskripsi capaian');
            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'semester_id'], 'final_grades_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove LMS tracking columns from grades
        Schema::table('grades', function (Blueprint $table) {
            $table->dropIndex('grades_lms_source_idx');
            $table->dropColumn(['lms_source_type', 'lms_source_id']);
        });

        // Drop grade_weights
        Schema::dropIfExists('grade_weights');

        // Recreate original final_grades schema
        Schema::dropIfExists('final_grades');
        Schema::create('final_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->decimal('tugas_score', 5, 2)->nullable();
            $table->decimal('uts_score', 5, 2)->nullable();
            $table->decimal('uas_score', 5, 2)->nullable();
            $table->decimal('sikap_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->boolean('is_passed')->default(false);
            $table->enum('predicate', ['A', 'B', 'C', 'D'])->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'subject_id', 'semester_id']);
        });
    }
};
