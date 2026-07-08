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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('employee_code', 20)->unique()->comment('NIK/NIP/NUPTK');
            $table->string('full_name', 100);
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('photo', 255)->nullable()->comment('Path file foto');
            
            // Employee specific fields
            $table->enum('employee_type', [
                'guru', 
                'staff_tu', 
                'staff_keuangan', 
                'staff_perpustakaan',
                'security',
                'cleaning_service',
                'driver',
                'other'
            ])->default('guru')->comment('Jenis pegawai');
            
            $table->enum('employment_status', [
                'yayasan',
                'pns',
                'pppk',
                'honorer',
                'percobaan',
                'magang',
                'kontrak'
            ])->default('yayasan')->comment('Status kepegawaian');
            
            $table->date('tmt_date')->comment('Tanggal Mulai Tugas - untuk hitung masa kerja');
            $table->date('end_date')->nullable()->comment('Tanggal berhenti/pensiun');
            
            // Payroll related
            $table->decimal('basic_salary', 15, 2)->nullable()->comment('Gaji pokok');
            $table->string('bank_name', 50)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_account_name', 100)->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('employee_code');
            $table->index('school_id');
            $table->index('employee_type');
            $table->index('employment_status');
            $table->index('is_active');
            $table->index('tmt_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
