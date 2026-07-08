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
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('subjects', function (Blueprint $table) {
            $table->string('subject_code', 20)->nullable()->change();
            $table->string('subject_name', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('subjects', function (Blueprint $table) {
            $table->string('subject_code', 20)->nullable(false)->change();
            $table->string('subject_name', 100)->nullable(false)->change();
        });
    }
};
