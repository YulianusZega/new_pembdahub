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
        // 1. Tambah kolom classroom_id ke employee_positions untuk link wali kelas dengan kelas
        Schema::table('employee_positions', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->after('position_id')
                ->constrained('classrooms')->onDelete('set null')
                ->comment('Untuk posisi Wali Kelas - kelas yang diwali');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_positions', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });
    }
};
