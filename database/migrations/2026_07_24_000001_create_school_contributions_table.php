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
        Schema::create('school_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->decimal('authorized_expense', 15, 2)->default(0)->comment('Belanja Otorisasi dari Yayasan untuk unit sekolah');
            $table->json('spp_rates')->nullable()->comment('Nominal SPP per level, misal {"7": 350000, "8": 350000}');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'academic_year_id'], 'school_academic_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_contributions');
    }
};
