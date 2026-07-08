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
        Schema::create('tefa_employees', function (Blueprint $table) {
            $table->id();
            $table->string('unit_name')->default('Bengkelin Tefa SMKS Pembda Nias');
            $table->string('name');
            $table->string('rfid_uid')->nullable()->unique();
            $table->string('position')->default('Mekanik / Teknisi');
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tefa_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tefa_employee_id')->constrained('tefa_employees')->cascadeOnDelete();
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->string('status')->default('hadir'); // hadir, terlambat, izin, sakit, dll
            $table->string('notes')->nullable();
            $table->string('recorded_via')->default('rfid');
            $table->string('device_id')->nullable();
            $table->timestamps();

            $table->unique(['tefa_employee_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tefa_attendances');
        Schema::dropIfExists('tefa_employees');
    }
};
