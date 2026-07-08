<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('training_name');
            $table->string('organizer')->nullable();
            $table->enum('training_type', [
                'diklat', 'workshop', 'seminar', 'sertifikasi', 'bimtek', 'lainnya'
            ]);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedSmallInteger('hours')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('certificate_file')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_trainings');
    }
};
