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
        Schema::create('performance_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null'); // ID Kepsek yang menilai
            
            // Detail Nilai Evaluasi 1-5
            $table->json('evaluation_data')->nullable(); // format: {"target_item_key": 4, "target_item_key2": 5}
            
            // Nilai Akhir (Skala 1-5 atau Rata-Rata)
            $table->decimal('score', 4, 2)->nullable();
            
            $table->enum('status', [
                'draft', 
                'submitted_to_yayasan', 
                'approved_by_yayasan',
                'rejected'
            ])->default('draft');
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Satu kontrak hanya bisa dievaluasi satu kali per semester
            $table->unique(['performance_contract_id', 'semester_id'], 'perf_eval_unique_contract_semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_evaluations');
    }
};
