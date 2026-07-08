<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Aturan Pembebasan Biaya Pendaftaran Berdasarkan Prestasi:
     * 1. Juara 1, 2, 3 dari SMPS Pembda 2 → SMA Swasta Pembda 1 / SMK
     * 2. Juara 1 dari SMP luar → SMA/SMK Pembda
     * 3. Juara 1 dari SD → SMPS Pembda 2
     */
    public function up(): void
    {
        Schema::create('achievement_fee_exemption_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            
            // Kriteria Asal Sekolah
            $table->string('previous_school_type'); // 'pembda' atau 'external'
            $table->string('previous_school_name')->nullable(); // Nama spesifik jika pembda
            $table->string('previous_school_level'); // 'SD', 'SMP'
            
            // Kriteria Prestasi
            $table->json('eligible_ranks'); // ['1', '2', '3'] atau ['1']
            $table->enum('proof_type', ['raport', 'certificate', 'both'])->default('both');
            
            // Jenis Pembebasan
            $table->string('exemption_fee_type'); // 'registration' (Rp 50.000)
            $table->decimal('exemption_amount', 15, 2)->default(50000);
            $table->enum('exemption_type', ['full', 'percentage'])->default('full');
            
            // Status & Deskripsi
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['target_school_id', 'academic_year_id'], 'exemption_rules_school_year_idx');
        });
        
        // Tabel untuk tracking pembebasan biaya yang diterapkan
        Schema::create('applicant_fee_exemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exemption_rule_id')->constrained('achievement_fee_exemption_rules')->cascadeOnDelete();
            $table->foreignId('achievement_id')->nullable()->constrained('applicant_achievements')->nullOnDelete();
            
            $table->string('rank_achieved'); // '1', '2', atau '3'
            $table->string('proof_document_type'); // 'raport', 'certificate'
            $table->string('proof_document_path')->nullable();
            
            $table->decimal('original_fee_amount', 15, 2);
            $table->decimal('exemption_amount', 15, 2);
            $table->decimal('final_fee_amount', 15, 2);
            
            $table->boolean('verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->index('applicant_id');
            $table->unique(['applicant_id', 'exemption_rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_fee_exemptions');
        Schema::dropIfExists('achievement_fee_exemption_rules');
    }
};
