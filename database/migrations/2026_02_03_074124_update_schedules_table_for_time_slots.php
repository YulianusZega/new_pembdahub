<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Add time_slot_id foreign key
            $table->foreignId('time_slot_id')->nullable()->after('subject_id')->constrained('time_slots')->onDelete('cascade');
            
            // Keep existing day_of_week, start_time, end_time for backward compatibility
            // But make them nullable since we'll use time_slot data instead
            if (DB::connection()->getDriverName() !== 'sqlite') {
                $table->string('day_of_week', 20)->nullable()->change();
                $table->time('start_time')->nullable()->change();
                $table->time('end_time')->nullable()->change();
            }
            
            // Add composite index for efficient queries
            $table->index(['classroom_id', 'time_slot_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['time_slot_id']);
            $table->dropIndex(['classroom_id', 'time_slot_id', 'day_of_week']);
            $table->dropColumn('time_slot_id');
            
            // Restore original columns to NOT NULL
            if (DB::connection()->getDriverName() !== 'sqlite') {
                $table->string('day_of_week', 20)->nullable(false)->change();
                $table->time('start_time')->nullable(false)->change();
                $table->time('end_time')->nullable(false)->change();
            }
        });
    }
};
