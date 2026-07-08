<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\User;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PositionAssignmentClassroomFilterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test classrooms are filtered by active academic year when assigning positions.
     */
    public function test_classrooms_are_filtered_by_active_academic_year(): void
    {
        // 1. Create school
        $school = School::create([
            'name' => 'SMP Swasta Pembda 2',
            'type' => 'SMP',
            'is_active' => true,
        ]);

        // 2. Create two academic years: Year 1 (inactive) and Year 2 (active)
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
            'is_active' => true, // ACTIVE
        ]);

        // 3. Create classrooms
        // Classroom in old year
        $classroomOld = Classroom::create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear1->id,
            'class_code' => 'VII-A-2025',
            'class_name' => 'VII-A Old',
            'class_type' => 'reguler',
            'grade_level' => 7,
            'capacity' => 30,
            'is_active' => true,
        ]);

        // Classroom in active year
        $classroomActive = Classroom::create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear2->id,
            'class_code' => 'VII-A-2026',
            'class_name' => 'VII-A Active',
            'class_type' => 'reguler',
            'grade_level' => 7,
            'capacity' => 30,
            'is_active' => true,
        ]);

        // 4. Create teacher / employee
        $teacher = Teacher::factory()->create([
            'school_id' => $school->id,
            'teacher_code' => 'T-FILTER',
            'full_name' => 'Teacher Filter, S.Pd',
            'is_active' => true,
        ]);
        $employee = $teacher->employee;
        $employee->update([
            'school_id' => $school->id,
            'employee_type' => 'guru',
        ]);

        // Create SuperAdmin user to authenticate
        $adminUser = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@pembdahub.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->actingAs($adminUser);

        // 5. Test create page classrooms list
        $responseCreate = $this->get(route('admin.assignments.positions.create', [
            'employee_id' => $employee->id
        ]));
        
        $responseCreate->assertStatus(200);
        $viewClassroomsCreate = $responseCreate->viewData('classrooms');
        
        // Assert only active academic year classrooms are shown
        $this->assertCount(1, $viewClassroomsCreate);
        $this->assertEquals($classroomActive->id, $viewClassroomsCreate->first()->id);

        // 6. Test edit page classrooms list
        $responseEdit = $this->get(route('admin.assignments.positions.edit', $employee->id));
        
        $responseEdit->assertStatus(200);
        $viewClassroomsEdit = $responseEdit->viewData('classrooms');
        
        // Assert only active academic year classrooms are shown
        $this->assertCount(1, $viewClassroomsEdit);
        $this->assertEquals($classroomActive->id, $viewClassroomsEdit->first()->id);
    }

    /**
     * Test allowances and total allowances are hidden for admin_sekolah,
     * but shown for superadmin and treasurer.
     */
    public function test_allowances_visibility_by_role(): void
    {
        // 1. Create school
        $school = School::create([
            'name' => 'SMP Swasta Pembda 2',
            'type' => 'SMP',
            'is_active' => true,
        ]);

        // 2. Create academic year
        $academicYear = AcademicYear::create([
            'year' => '2026/2027',
            'semester' => 'ganjil',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        // 3. Create position with allowance
        $position = Position::create([
            'school_id' => $school->id,
            'position_name' => 'Wali Kelas SMP',
            'position_code' => 'WALIKELAS-SMP',
            'position_category' => 'structural',
            'position_level' => 1,
            'allowance_amount' => 750000,
            'is_active' => true,
        ]);

        // 4. Create teacher / employee
        $teacher = Teacher::factory()->create([
            'school_id' => $school->id,
            'teacher_code' => 'T-VISIBILITY',
            'full_name' => 'Teacher Visibility, S.Pd',
            'is_active' => true,
        ]);
        $employee = $teacher->employee;
        $employee->update([
            'school_id' => $school->id,
            'employee_type' => 'guru',
        ]);

        // Assign teacher to the position
        \DB::table('employee_positions')->insert([
            'employee_id' => $employee->id,
            'position_id' => $position->id,
            'academic_year_id' => $academicYear->id,
            'semester' => 'full_year',
            'start_date' => '2026-07-01',
            'end_date' => null,
            'is_primary' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Authenticate as Admin Sekolah
        $adminSekolah = User::create([
            'name' => 'Admin Sekolah',
            'username' => 'adminsekolah',
            'email' => 'adminsekolah@pembdahub.com',
            'password' => bcrypt('password'),
            'role' => 'admin_sekolah',
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $this->actingAs($adminSekolah);

        // Access index page
        $responseIndexAdmin = $this->get(route('admin.assignments.positions.index'));
        $responseIndexAdmin->assertStatus(200);
        $responseIndexAdmin->assertDontSee('Total Tunjangan');
        $responseIndexAdmin->assertDontSee('Rp 750.000');

        // Access create page
        $responseCreateAdmin = $this->get(route('admin.assignments.positions.create', ['employee_id' => $employee->id]));
        $responseCreateAdmin->assertStatus(200);
        $responseCreateAdmin->assertDontSee('Rp 750.000');

        // Access edit page
        $responseEditAdmin = $this->get(route('admin.assignments.positions.edit', $employee->id));
        $responseEditAdmin->assertStatus(200);
        $responseEditAdmin->assertDontSee('Tunjangan: Rp 750.000');

        // 6. Authenticate as Treasurer (Bendahara)
        $treasurer = User::create([
            'name' => 'Bendahara',
            'username' => 'bendahara',
            'email' => 'bendahara@pembdahub.com',
            'password' => bcrypt('password'),
            'role' => 'bendahara',
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $this->actingAs($treasurer);

        // Access treasurer index page
        $responseIndexTreasurer = $this->get(route('treasurer.assignments.positions.index'));
        $responseIndexTreasurer->assertStatus(200);
        $responseIndexTreasurer->assertSee('Total Tunjangan');
        $responseIndexTreasurer->assertSee('Rp 750.000');
        
        // Assert that treasurer has no edit/delete buttons
        $responseIndexTreasurer->assertDontSee('Edit Penugasan');
        $responseIndexTreasurer->assertDontSee('Hapus');
    }
}
