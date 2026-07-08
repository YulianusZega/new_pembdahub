<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('relation', ['suami', 'istri', 'anak', 'ayah', 'ibu']);
            $table->string('full_name');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->string('occupation')->nullable();
            $table->string('education_level')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_family_members');
    }
};
