<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * v2.5.0 — Payroll & Teaching Assignment Architecture Fix
 *
 * 1. Add marital_status and children_count to employees (required for tunjangan calculation)
 * 2. Add teaching_assignment_id FK to schedules (link schedule slots to teaching assignments)
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Add family data fields to employees for tunjangan calculation
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('marital_status', ['belum_menikah', 'menikah', 'cerai'])
                ->default('belum_menikah')
                ->after('employment_status')
                ->comment('Status pernikahan untuk tunjangan keluarga');

            $table->unsignedTinyInteger('children_count')
                ->default(0)
                ->after('marital_status')
                ->comment('Jumlah anak untuk tunjangan anak & beras');
        });

        // 2. Add teaching_assignment_id FK to schedules
        // This links each schedule slot back to its parent teaching assignment
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('teaching_assignment_id')
                ->nullable()
                ->after('id')
                ->constrained('teaching_assignments')
                ->nullOnDelete()
                ->comment('FK ke penugasan mengajar induk');

            $table->index('teaching_assignment_id');
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['teaching_assignment_id']);
            $table->dropColumn('teaching_assignment_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['marital_status', 'children_count']);
        });
    }
};
