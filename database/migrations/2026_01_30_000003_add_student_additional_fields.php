<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('previous_school', 100)->nullable()->after('parent_phone');
            $table->string('guardian_name', 100)->nullable()->after('previous_school');
            $table->string('guardian_phone', 20)->nullable()->after('guardian_name');
            $table->string('guardian_occupation', 100)->nullable()->after('guardian_phone');
            $table->text('guardian_address')->nullable()->after('guardian_occupation');
            $table->string('hobby', 255)->nullable()->after('guardian_address');
            $table->text('health_history')->nullable()->after('hobby');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'previous_school',
                'guardian_name',
                'guardian_phone',
                'guardian_occupation',
                'guardian_address',
                'hobby',
                'health_history',
            ]);
        });
    }
};
