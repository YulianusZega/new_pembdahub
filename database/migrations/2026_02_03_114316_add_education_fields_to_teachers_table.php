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
        Schema::table('teachers', function (Blueprint $table) {
            $table->enum('education_level', ['SMA/SMK', 'D3', 'S1', 'S2', 'S3'])
                  ->nullable()
                  ->after('gender')
                  ->comment('Jenjang pendidikan terakhir guru');
            
            $table->string('major', 100)
                  ->nullable()
                  ->after('education_level')
                  ->comment('Jurusan pendidikan terakhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['education_level', 'major']);
        });
    }
};
