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
        Schema::table('training_modules', function (Blueprint $table) {
            $table->string('thumbnail_image')->nullable()->after('pdf_file');
            $table->integer('reading_time')->default(15)->after('thumbnail_image');
            $table->enum('difficulty', ['Pemula', 'Menengah', 'Mahir'])->default('Pemula')->after('reading_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_modules', function (Blueprint $table) {
            $table->dropColumn(['thumbnail_image', 'reading_time', 'difficulty']);
        });
    }
};
