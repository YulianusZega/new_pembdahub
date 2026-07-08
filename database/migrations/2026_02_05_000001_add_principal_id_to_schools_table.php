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
        Schema::table('schools', function (Blueprint $table) {
            // Add principal_id foreign key
            $table->unsignedBigInteger('principal_id')->nullable()->after('website');
            
            // Add foreign key constraint
            $table->foreign('principal_id')
                  ->references('id')
                  ->on('teachers')
                  ->onDelete('set null');
            
            // Keep principal_name for backward compatibility (optional - bisa dihapus nanti)
            // $table->dropColumn('principal_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign(['principal_id']);
            $table->dropColumn('principal_id');
        });
    }
};
