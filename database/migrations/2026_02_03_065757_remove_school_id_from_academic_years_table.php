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
        // First, delete duplicate academic years, keeping only one per year
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('
                DELETE FROM academic_years
                WHERE id NOT IN (
                    SELECT MIN(id) FROM academic_years GROUP BY year
                )
            ');
        } else {
            DB::statement('
                DELETE t1 FROM academic_years t1
                INNER JOIN academic_years t2 
                WHERE t1.id > t2.id 
                AND t1.year = t2.year
            ');
        }
        
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: need to recreate the table without school_id
            // because SQLite doesn't support dropping columns with FK constraints
            Schema::dropIfExists('academic_years_backup');
            DB::statement('CREATE TABLE academic_years_backup AS SELECT id, year, start_date, end_date, semester_start, semester_end, is_active, created_at, updated_at FROM academic_years');
            
            // Remove duplicates from backup
            DB::statement('
                DELETE FROM academic_years_backup
                WHERE id NOT IN (
                    SELECT MIN(id) FROM academic_years_backup GROUP BY year
                )
            ');

            Schema::drop('academic_years');
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('year', 20)->unique();
                $table->date('start_date');
                $table->date('end_date');
                $table->date('semester_start')->nullable();
                $table->date('semester_end')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();
                $table->index('is_active');
            });

            DB::statement('INSERT INTO academic_years (id, year, start_date, end_date, semester_start, semester_end, is_active, created_at, updated_at) SELECT id, year, start_date, end_date, semester_start, semester_end, is_active, created_at, updated_at FROM academic_years_backup');
            Schema::dropIfExists('academic_years_backup');
        } else {
            Schema::table('academic_years', function (Blueprint $table) {
                // Drop foreign key first
                $table->dropForeign(['school_id']);
                // Then drop the column
                $table->dropColumn('school_id');
                // Add unique constraint on year
                $table->unique('year');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            // Remove unique constraint
            $table->dropUnique(['year']);
            // Add back school_id
            $table->foreignId('school_id')->after('id')->constrained('schools')->onDelete('cascade');
        });
    }
};
