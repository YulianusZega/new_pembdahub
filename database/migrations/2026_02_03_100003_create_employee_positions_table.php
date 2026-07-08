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
        Schema::create('employee_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            
            $table->date('start_date')->comment('TMT jabatan ini');
            $table->date('end_date')->nullable()->comment('NULL = masih aktif di jabatan ini');
            
            $table->string('sk_number', 50)->nullable()->comment('Nomor SK pengangkatan');
            $table->date('sk_date')->nullable()->comment('Tanggal SK');
            $table->text('notes')->nullable()->comment('Catatan mutasi/pengangkatan');
            
            $table->boolean('is_primary')->default(false)->comment('Jabatan utama/pokok');
            $table->timestamps();

            // Indexes
            $table->index('employee_id');
            $table->index('position_id');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('is_primary');
            
            // Unique constraint: satu employee tidak bisa punya 2 posisi sama yang aktif bersamaan
            $table->unique(['employee_id', 'position_id', 'start_date'], 'unique_active_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_positions');
    }
};
