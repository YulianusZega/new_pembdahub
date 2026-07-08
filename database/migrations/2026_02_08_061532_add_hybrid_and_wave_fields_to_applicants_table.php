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
            $table->enum('registration_type', ['online', 'offline'])->default('online')->after('registration_number');
            $table->foreignId('wave_id')->nullable()->after('academic_year_id')->constrained('registration_waves')->nullOnDelete();
            $table->string('registered_by')->nullable()->after('wave_id'); // Admin username untuk offline registration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropForeign(['wave_id']);
            $table->dropColumn(['registration_type', 'wave_id', 'registered_by']);
        });
    }
};
