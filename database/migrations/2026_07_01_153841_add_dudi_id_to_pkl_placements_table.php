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
        Schema::table('pkl_placements', function (Blueprint $table) {
            $table->foreignId('dudi_id')->nullable()->after('academic_year_id')->constrained('dudis')->onDelete('set null');
            
            // Make old text fields nullable since we might use dudi_id going forward
            $table->string('company_name')->nullable()->change();
            $table->string('company_address')->nullable()->change();
            $table->string('mentor_name')->nullable()->change();
            $table->string('mentor_phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkl_placements', function (Blueprint $table) {
            $table->dropForeign(['dudi_id']);
            $table->dropColumn('dudi_id');
            
            // Note: Reverting nullable() requires doctrine/dbal, 
            // but for simplicity we will just leave them nullable in down() 
            // or we could enforce it if we assume no nulls were added.
        });
    }
};
