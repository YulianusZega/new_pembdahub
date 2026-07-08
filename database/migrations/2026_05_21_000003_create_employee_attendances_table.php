<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained();
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->enum('status', [
                'hadir', 'sakit', 'izin', 'alpha', 'dinas_luar', 'cuti'
            ])->default('hadir');
            $table->enum('recorded_via', ['manual', 'rfid'])->default('manual');
            $table->string('device_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
            $table->index(['school_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_attendances');
    }
};
