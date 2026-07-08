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
        // 1. Admission Fees (Biaya Pendaftaran per Sekolah)
        Schema::create('admission_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('fee_type'); // registration, equipment, reregistration
            $table->string('fee_name'); // "Biaya Pendaftaran", "Uang Alat", "Uang Pangkal"
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['school_id', 'academic_year_id', 'fee_type']);
        });

        // 2. Admission Tests (Jenis Tes per Sekolah)
        Schema::create('admission_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('test_name'); // "Matematika", "IPA", "Bahasa Indonesia"
            $table->integer('max_score')->default(100);
            $table->decimal('weight', 3, 2)->default(1.00); // Bobot nilai
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['school_id', 'academic_year_id']);
        });

        // 3. Admission Discounts (Diskon per Sekolah)
        Schema::create('admission_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('discount_name'); // "Best 3", "Sibling", "Scholarship"
            $table->string('discount_type'); // percentage, fixed
            $table->decimal('discount_value', 15, 2); // 50 untuk 50%, atau 500000 untuk Rp 500,000
            $table->enum('applies_to', ['spp', 'pangkal', 'all'])->default('spp');
            $table->integer('duration_months')->default(12); // Berlaku untuk berapa bulan
            $table->text('criteria')->nullable(); // JSON criteria
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['school_id', 'academic_year_id']);
        });

        // 4. Applicants (Calon Siswa)
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete(); // Link setelah registered
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('registration_number', 20)->unique(); // REG-2026-001
            
            // Step 1: Student Data
            $table->string('nisn', 20)->unique();
            $table->string('full_name', 100);
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('photo_path', 255)->nullable();
            
            // Parent Data
            $table->string('father_name', 100)->nullable();
            $table->string('father_occupation', 100)->nullable();
            $table->string('father_phone', 20)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('mother_occupation', 100)->nullable();
            $table->string('mother_phone', 20)->nullable();
            $table->string('parent_email', 255)->nullable();
            $table->decimal('parent_income', 15, 2)->nullable();
            
            // Guardian (optional)
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_phone', 20)->nullable();
            $table->string('guardian_relation', 50)->nullable();
            
            // School Info
            $table->string('previous_school', 100)->nullable();
            $table->year('graduation_year')->nullable();
            
            // Admission Path
            $table->enum('admission_path', ['reguler', 'prestasi', 'afirmasi', 'zonasi'])->default('reguler');
            $table->foreignId('first_major_choice_id')->nullable()->constrained('majors')->nullOnDelete(); // untuk SMK
            $table->foreignId('second_major_choice_id')->nullable()->constrained('majors')->nullOnDelete();
            
            // Status Flow
            $table->enum('status', [
                'draft', 
                'submitted', 
                'payment_verified', 
                'document_verified', 
                'tested', 
                'scored', 
                'accepted', 
                'rejected', 
                'reregistered', 
                'registered'
            ])->default('draft');
            
            // Scoring
            $table->decimal('average_raport_score', 5, 2)->nullable(); // Nilai rata-rata raport
            $table->decimal('test_total_score', 5, 2)->nullable(); // Total nilai tes
            $table->decimal('interview_score', 5, 2)->nullable();
            $table->decimal('achievement_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable(); // Score akhir (weighted)
            $table->integer('rank')->nullable(); // Ranking per jalur
            
            // Dates
            $table->timestamp('submission_date')->nullable();
            $table->timestamp('payment_verified_at')->nullable();
            $table->timestamp('document_verified_at')->nullable();
            $table->date('test_date')->nullable();
            $table->timestamp('announcement_date')->nullable();
            $table->timestamp('reregistration_date')->nullable();
            $table->timestamp('registration_date')->nullable();
            
            // Notes
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            $table->index(['school_id', 'academic_year_id', 'status']);
            $table->index('rank');
        });

        // 5. Applicant Documents
        Schema::create('applicant_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', ['kk', 'akta', 'ijazah', 'photo', 'raport', 'certificate', 'other']);
            $table->string('file_path', 255);
            $table->string('file_name', 255);
            $table->integer('file_size')->nullable();
            $table->boolean('verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('applicant_id');
        });

        // 6. Applicant Test Scores
        Schema::create('applicant_test_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admission_test_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('applicant_id');
            $table->unique(['applicant_id', 'admission_test_id']);
        });

        // 7. Applicant Achievements
        Schema::create('applicant_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('achievement_name', 255);
            $table->enum('level', ['international', 'national', 'provincial', 'district', 'school']);
            $table->year('year');
            $table->string('certificate_path', 255)->nullable();
            $table->decimal('points', 5, 2); // Auto-calculated dari level
            $table->timestamps();
            
            $table->index('applicant_id');
        });

        // 8. Applicant Payments
        Schema::create('applicant_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admission_fee_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'virtual_account', 'qris'])->default('transfer');
            $table->string('receipt_path', 255)->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->boolean('verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('applicant_id');
        });

        // 9. Applicant Discounts (Diskon yang applied ke applicant)
        Schema::create('applicant_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admission_discount_id')->constrained()->cascadeOnDelete();
            $table->string('reason')->nullable(); // "Rank 1", "Sibling", dll
            $table->decimal('discount_amount', 15, 2); // Jumlah diskon dalam Rupiah
            $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();
            
            $table->index('applicant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_discounts');
        Schema::dropIfExists('applicant_payments');
        Schema::dropIfExists('applicant_achievements');
        Schema::dropIfExists('applicant_test_scores');
        Schema::dropIfExists('applicant_documents');
        Schema::dropIfExists('applicants');
        Schema::dropIfExists('admission_discounts');
        Schema::dropIfExists('admission_tests');
        Schema::dropIfExists('admission_fees');
    }
};
