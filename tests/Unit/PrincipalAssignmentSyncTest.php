<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\Position;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PrincipalAssignmentSyncTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test auto-assigning principal position and closing previous principal assignment.
     */
    public function test_principal_assignment_sync_and_deactivation(): void
    {
        // 1. Create school
        $school = School::create([
            'name' => 'SMK Pembda Nias',
            'type' => 'SMK',
            'is_active' => true,
        ]);

        // 2. Create position KASEK-SMK
        $position = Position::create([
            'school_id' => $school->id,
            'position_name' => 'Kasek SMK',
            'position_code' => 'KASEK-SMK',
            'position_category' => 'structural',
            'position_level' => 1,
            'allowance_amount' => 3500000,
            'is_active' => true,
        ]);

        // 3. Create active academic year
        $academicYear = AcademicYear::create([
            'year' => '2026/2027',
            'semester' => 'ganjil',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        // 4. Create two teachers with employee records using factory
        $teacherOld = \App\Models\Teacher::factory()->create([
            'school_id' => $school->id,
            'teacher_code' => 'T-OLD',
            'full_name' => 'Principal Old, M.Pd',
            'is_active' => true,
        ]);
        $employeeOld = $teacherOld->employee;
        $employeeOld->update([
            'school_id' => $school->id,
            'employee_type' => 'guru',
        ]);

        $teacherNew = \App\Models\Teacher::factory()->create([
            'school_id' => $school->id,
            'teacher_code' => 'T-NEW',
            'full_name' => 'Principal New, S.Pd',
            'is_active' => true,
        ]);
        $employeeNew = $teacherNew->employee;
        $employeeNew->update([
            'school_id' => $school->id,
            'employee_type' => 'guru',
        ]);

        // 5. Assign old teacher as principal active record
        DB::table('employee_positions')->insert([
            'employee_id' => $employeeOld->id,
            'position_id' => $position->id,
            'academic_year_id' => $academicYear->id,
            'semester' => 'full_year',
            'start_date' => '2026-07-01',
            'end_date' => null,
            'is_primary' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Verify old principal has active position
        $this->assertTrue(
            DB::table('employee_positions')
                ->where('employee_id', $employeeOld->id)
                ->where('position_id', $position->id)
                ->whereNull('end_date')
                ->exists()
        );

        // 6. Use reflection to invoke the private assignPrincipalPosition method in SchoolController
        $controller = new \App\Http\Controllers\Admin\SchoolController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('assignPrincipalPosition');
        $method->setAccessible(true);
        
        $method->invokeArgs($controller, [$teacherNew->id, $school->id]);

        // Verify:
        // A. Old principal's assignment is closed (end_date is set to today)
        $oldRecord = DB::table('employee_positions')
            ->where('employee_id', $employeeOld->id)
            ->where('position_id', $position->id)
            ->first();
        
        $this->assertNotNull($oldRecord->end_date);
        $this->assertEquals(now()->format('Y-m-d'), $oldRecord->end_date);

        // B. New principal has active assignment record
        $newRecordExists = DB::table('employee_positions')
            ->where('employee_id', $employeeNew->id)
            ->where('position_id', $position->id)
            ->whereNull('end_date')
            ->exists();

        $this->assertTrue($newRecordExists);
    }

    /**
     * Test that if the teacher has an active assignment in an older academic year,
     * assigning them as principal in a new academic year creates a new record for the new academic year.
     */
    public function test_principal_assignment_sync_creates_new_record_for_new_academic_year(): void
    {
        // 1. Create school
        $school = School::create([
            'name' => 'SMA Pembda Nias',
            'type' => 'SMA',
            'is_active' => true,
        ]);

        // 2. Create position KASEK-SMA
        $position = Position::create([
            'school_id' => $school->id,
            'position_name' => 'Kasek SMA',
            'position_code' => 'KASEK-SMA',
            'position_category' => 'structural',
            'position_level' => 1,
            'allowance_amount' => 3500000,
            'is_active' => true,
        ]);

        // 3. Create two academic years: Year 1 (inactive) and Year 2 (active)
        $academicYear1 = AcademicYear::create([
            'year' => '2025/2026',
            'semester' => 'ganjil',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'is_active' => false,
        ]);

        $academicYear2 = AcademicYear::create([
            'year' => '2026/2027',
            'semester' => 'ganjil',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        // 4. Create a teacher
        $teacher = \App\Models\Teacher::factory()->create([
            'school_id' => $school->id,
            'teacher_code' => 'T-TEST',
            'full_name' => 'Principal Test, S.Pd',
            'is_active' => true,
        ]);
        $employee = $teacher->employee;
        $employee->update([
            'school_id' => $school->id,
            'employee_type' => 'guru',
        ]);

        // 5. Assign teacher to the position in Year 1 (active, end_date = null)
        DB::table('employee_positions')->insert([
            'employee_id' => $employee->id,
            'position_id' => $position->id,
            'academic_year_id' => $academicYear1->id,
            'semester' => 'full_year',
            'start_date' => '2025-07-01',
            'end_date' => null,
            'is_primary' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Invoke assignPrincipalPosition
        $controller = new \App\Http\Controllers\Admin\SchoolController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('assignPrincipalPosition');
        $method->setAccessible(true);
        
        $method->invokeArgs($controller, [$teacher->id, $school->id]);

        // Verify:
        // A. The old academic year record is STILL open (end_date = null) because it belongs to an older year.
        $oldRecord = DB::table('employee_positions')
            ->where('employee_id', $employee->id)
            ->where('position_id', $position->id)
            ->where('academic_year_id', $academicYear1->id)
            ->first();
        
        $this->assertNull($oldRecord->end_date);

        // B. A new record is created for the new active academic year.
        $newRecord = DB::table('employee_positions')
            ->where('employee_id', $employee->id)
            ->where('position_id', $position->id)
            ->where('academic_year_id', $academicYear2->id)
            ->first();

        $this->assertNotNull($newRecord);
        $this->assertNull($newRecord->end_date);
    }
}
