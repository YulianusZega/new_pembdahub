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
        Schema::create('lms_material_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('lms_materials')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('reaction_type'); // 'like', 'confused', 'insightful'
            $table->timestamps();
            
            // Prevent multiple reactions from same student on same material
            $table->unique(['material_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_material_reactions');
    }
};
