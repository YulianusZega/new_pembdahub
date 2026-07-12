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
        Schema::table('alumni_directories', function (Blueprint $table) {
            $table->string('alias_name')->nullable()->after('full_name');
            $table->string('marital_status')->nullable()->after('gender');
            $table->integer('children_count')->nullable()->after('marital_status');
            $table->string('company_name')->nullable()->after('occupation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumni_directories', function (Blueprint $table) {
            $table->dropColumn(['alias_name', 'marital_status', 'children_count', 'company_name']);
        });
    }
};
