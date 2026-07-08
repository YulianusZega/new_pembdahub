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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('day_of_week', 10); // monday, tuesday, wednesday, thursday, friday
            $table->string('slot_name', 50); // Apel, 5S, Les 1, Les 2, dst
            $table->string('slot_type', 20); // lesson, break, ceremony
            $table->integer('slot_order'); // Urutan slot (1, 2, 3, dst)
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes'); // Durasi dalam menit
            $table->boolean('is_teaching_slot')->default(true); // Apakah slot untuk mengajar
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['school_id', 'day_of_week', 'slot_order']);
            $table->index('is_teaching_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
