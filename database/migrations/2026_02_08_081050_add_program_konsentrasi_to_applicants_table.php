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
        Schema::table('applicants', function (Blueprint $table) {
            $table->unsignedBigInteger('program_keahlian_id')->nullable()->after('school_id');
            $table->unsignedBigInteger('konsentrasi_keahlian_id')->nullable()->after('program_keahlian_id');
            
            $table->foreign('program_keahlian_id')->references('id')->on('program_keahlians')->onDelete('set null');
            $table->foreign('konsentrasi_keahlian_id')->references('id')->on('konsentrasi_keahlians')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropForeign(['program_keahlian_id']);
            $table->dropForeign(['konsentrasi_keahlian_id']);
            $table->dropColumn(['program_keahlian_id', 'konsentrasi_keahlian_id']);
        });
    }
};
