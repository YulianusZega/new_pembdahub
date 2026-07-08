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
        // Step 1: Add employee_id to teachers table
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('id')
                ->constrained('employees')->onDelete('cascade');
        });

        // Step 2: Migrate existing teachers to employees
        $teachers = DB::table('teachers')->get();
        
        foreach ($teachers as $teacher) {
            // Create employee record
            $employeeId = DB::table('employees')->insertGetId([
                'school_id' => $teacher->school_id,
                'user_id' => $teacher->user_id,
                'employee_code' => $teacher->teacher_code,
                'full_name' => $teacher->full_name,
                'gender' => $teacher->gender,
                'birth_place' => $teacher->birth_place,
                'birth_date' => $teacher->birth_date,
                'religion' => $teacher->religion,
                'address' => $teacher->address,
                'phone' => $teacher->phone,
                'photo' => $teacher->photo,
                'employee_type' => 'guru',
                'employment_status' => 'yayasan', // default
                'tmt_date' => $teacher->created_at ?? now(),
                'basic_salary' => null,
                'is_active' => $teacher->is_active,
                'created_at' => $teacher->created_at ?? now(),
                'updated_at' => $teacher->updated_at ?? now(),
            ]);

            // Update teacher with employee_id
            DB::table('teachers')
                ->where('id', $teacher->id)
                ->update(['employee_id' => $employeeId]);
        }

        // Step 3: Make employee_id NOT NULL after migration (skip on SQLite)
        if (DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('teachers', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
};
