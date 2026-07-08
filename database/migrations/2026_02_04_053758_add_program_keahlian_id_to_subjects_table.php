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
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('program_keahlian_id')->nullable()->after('major_id');
            $table->string('code', 20)->nullable()->after('school_id'); // Add code column
            $table->string('name', 100)->nullable()->after('code'); // Add name column
            $table->string('category', 50)->nullable()->after('name'); // Wajib or Produktif
            $table->integer('hours_per_week')->default(2)->after('category');
            
            $table->foreign('program_keahlian_id')->references('id')->on('program_keahlians')->onDelete('set null');
            $table->index('program_keahlian_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['program_keahlian_id']);
            $table->dropIndex(['program_keahlian_id']);
            $table->dropColumn(['program_keahlian_id', 'code', 'name', 'category', 'hours_per_week']);
        });
    }
};
