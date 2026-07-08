<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('program_keahlians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('kode', 20);
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        Schema::create('konsentrasi_keahlians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_keahlian_id');
            $table->string('kode', 20);
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreign('program_keahlian_id')->references('id')->on('program_keahlians')->onDelete('cascade');
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('class_type', 20)->default('reguler')->after('class_name');
            $table->unsignedBigInteger('program_keahlian_id')->nullable()->after('major_id');
            $table->unsignedBigInteger('konsentrasi_keahlian_id')->nullable()->after('program_keahlian_id');
            $table->foreign('program_keahlian_id')->references('id')->on('program_keahlians')->onDelete('set null');
            $table->foreign('konsentrasi_keahlian_id')->references('id')->on('konsentrasi_keahlians')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropForeign(['program_keahlian_id']);
            $table->dropForeign(['konsentrasi_keahlian_id']);
            $table->dropColumn(['class_type', 'program_keahlian_id', 'konsentrasi_keahlian_id']);
        });
        Schema::dropIfExists('konsentrasi_keahlians');
        Schema::dropIfExists('program_keahlians');
    }
};
