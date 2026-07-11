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
        Schema::create('performance_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->constrained()->onDelete('cascade'); // Khusus SMK
            
            // Tipe Kontrak
            $table->enum('contract_type', ['pkg_kejuruan', 'pkg_umum', 'jabatan_tambahan']);
            
            // Jika tipe = jabatan_tambahan, referensi ke posisi yang diincar
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null');
            
            // Isi Kontrak (Data Target)
            $table->json('target_data')->nullable(); // Menyimpan target omzet TEFA, judul PBL, dll.
            
            // Status Persetujuan
            $table->enum('status', [
                'draft', 
                'submitted_to_kepsek', 
                'approved_by_kepsek', 
                'approved_by_yayasan', 
                'rejected'
            ])->default('draft');
            
            // Catatan Evaluasi (Jika ditolak)
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_contracts');
    }
};
