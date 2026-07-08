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
        Schema::table('final_projects', function (Blueprint $table) {
            $table->foreignId('examiner2_id')->nullable()->after('examiner_id')->constrained('teachers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_projects', function (Blueprint $table) {
            $table->dropForeign(['examiner2_id']);
            $table->dropColumn('examiner2_id');
        });
    }
};
