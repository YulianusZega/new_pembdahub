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
        Schema::create('registration_waves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->string('name'); // Gelombang 1, Gelombang 2, etc
            $table->integer('wave_number')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('quota')->nullable(); // Kuota pendaftar, null = unlimited
            $table->integer('registered_count')->default(0); // Counter otomatis
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Index
            $table->index(['school_id', 'academic_year_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_waves');
    }
};
