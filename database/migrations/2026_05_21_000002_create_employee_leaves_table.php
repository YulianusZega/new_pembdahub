<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained();
            $table->enum('leave_type', [
                'cuti_tahunan', 'sakit', 'izin', 'cuti_besar', 'dinas_luar', 'lainnya'
            ]);
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('days_count');
            $table->text('reason');
            $table->string('attachment')->nullable();
            $table->enum('status', [
                'pending', 'approved_kepsek', 'approved_yayasan', 'approved', 'rejected'
            ])->default('pending');
            $table->foreignId('approved_by_kepsek')->nullable()->constrained('users');
            $table->dateTime('approved_at_kepsek')->nullable();
            $table->foreignId('approved_by_yayasan')->nullable()->constrained('users');
            $table->dateTime('approved_at_yayasan')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['school_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_leaves');
    }
};
