<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set all existing positions to 'full_year' to consolidate them into a 1-year academic cycle
        DB::table('employee_positions')
            ->update(['semester' => 'full_year']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to undo consolidation without knowing previous values, 
        // but 'ganjil' is usually a safe default if needed.
    }
};
