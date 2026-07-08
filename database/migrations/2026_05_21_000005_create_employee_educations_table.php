<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('education_level', [
                'SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'
            ]);
            $table->string('institution_name');
            $table->string('major')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('certificate_file')->nullable();
            $table->timestamps();

            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_educations');
    }
};
