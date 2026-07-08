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
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->boolean('is_primary')->default(false)->comment('Mata pelajaran utama/keahlian utama guru');
            $table->text('notes')->nullable()->comment('Catatan tambahan, misal: Sertifikasi, Tahun lulus, dll');
            $table->timestamps();
            
            // Unique constraint: satu guru tidak bisa punya duplikat subject
            $table->unique(['teacher_id', 'subject_id'], 'unique_teacher_subject');
            
            // Index untuk performa query
            $table->index('teacher_id');
            $table->index('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_teacher');
    }
};
