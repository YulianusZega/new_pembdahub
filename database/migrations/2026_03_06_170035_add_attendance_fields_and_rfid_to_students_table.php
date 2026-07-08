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
        Schema::table('students', function (Blueprint $table) {
            $table->string('rfid_uid')->nullable()->unique()->after('nisn');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->time('time_in')->nullable()->after('date');
            $table->time('time_out')->nullable()->after('time_in');
            $table->string('recorded_via')->nullable()->default('manual')->after('status')->comment('rfid, qr_gps, manual');
            $table->decimal('latitude', 10, 8)->nullable()->after('recorded_via');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['time_in', 'time_out', 'recorded_via', 'latitude', 'longitude']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('rfid_uid');
        });
    }
};
