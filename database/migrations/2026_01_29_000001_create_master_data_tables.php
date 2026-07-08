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
        // 1. MASTER DATA
        // ================================================================

        // Tabel Sekolah
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('type');
            $table->string('npsn', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 50)->nullable();
            $table->string('province', 50)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('principal_name', 100)->nullable();
            $table->integer('school_year_start')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('type');
        });

        // Tabel Tahun Ajaran
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('year', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('semester_start')->nullable();
            $table->date('semester_end')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index('is_active');
            $table->index('school_id');
        });

        // Tabel Semester
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->tinyInteger('semester_number')->comment('1=Ganjil(Jul-Des), 2=Genap(Jan-Jun)');
            $table->string('semester_name', 50);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);

            $table->index('is_active');
            $table->index('academic_year_id');
        });

        // Tabel Jurusan
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('major_code', 20)->comment('IPA, IPS, DPIB, TJKT, ACP, TKR, TSM, TAV, TE, TO');
            $table->string('major_name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->index('school_id');
            $table->index('major_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('schools');
    }
};
