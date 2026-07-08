<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClassroomAssignStudentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test assign students view contains search field and student items.
     */
    public function test_assign_students_view_contains_search_field(): void
    {
        // 1. Create school and active academic year
        $school = School::create([
            'name' => 'SMP Swasta Pembda 2',
            'type' => 'SMP',
            'is_active' => true,
        ]);

        $academicYear = AcademicYear::create([
            'year' => '2026/2027',
            'semester' => 'ganjil',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        // 2. Create classroom
        $classroom = Classroom::create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'class_code' => 'VII-A-2026',
            'class_name' => 'VII-A Active',
            'class_type' => 'reguler',
            'grade_level' => 7,
            'capacity' => 30,
            'is_active' => true,
        ]);

        // 3. Create some students using factory
        Student::factory()->create([
            'school_id' => $school->id,
            'nisn' => '1234567890',
            'full_name' => 'Budi Santoso',
            'status' => 'aktif',
        ]);

        // 4. Authenticate as SuperAdmin
        $adminUser = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@pembdahub.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->actingAs($adminUser);

        // 5. Get the page
        $response = $this->get("/admin/classrooms/{$classroom->id}/assign-students");
        
        $response->assertStatus(200);
        
        // Assert search field is rendered
        $response->assertSee('id="studentSearch"', false);
        $response->assertSee('placeholder="Cari nama atau NISN siswa..."', false);
        
        // Assert student items have classes for JS filtering
        $response->assertSee('class="student-item', false);
        $response->assertSee('class="student-name', false);
        $response->assertSee('class="student-nisn', false);
    }
}
