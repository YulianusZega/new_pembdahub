<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->unsignedBigInteger('major_id')->nullable()->after('school_id');
            $table->foreign('major_id')->references('id')->on('majors')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropForeign(['major_id']);
            $table->dropColumn('major_id');
        });
    }
};
