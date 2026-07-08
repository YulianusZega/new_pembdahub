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
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // For MySQL specifically, we use DB::statement to update ENUM values
        DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM(
            'draft', 
            'submitted', 
            'payment_verified', 
            'prestasi_verified',
            'document_verified', 
            'tested', 
            'scored', 
            'accepted', 
            'rejected', 
            'reregistered', 
            'registered'
        ) DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM(
            'draft', 
            'submitted', 
            'payment_verified', 
            'document_verified', 
            'tested', 
            'scored', 
            'accepted', 
            'rejected', 
            'reregistered', 
            'registered'
        ) DEFAULT 'draft'");
    }
};
