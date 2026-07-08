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
        // Skip if column already exists (added in create_time_slots_table migration)
        if (Schema::hasColumn('time_slots', 'day_of_week')) {
            return;
        }

        Schema::table('time_slots', function (Blueprint $table) {
            $table->string('day_of_week', 10)->default('monday')->after('school_id');
            $table->dropIndex(['school_id', 'slot_order']);
            $table->index(['school_id', 'day_of_week', 'slot_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_slots', function (Blueprint $table) {
            $table->dropIndex(['school_id', 'day_of_week', 'slot_order']);
            $table->index(['school_id', 'slot_order']);
            $table->dropColumn('day_of_week');
        });
    }
};
