<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemesterCrudTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    public function test_superadmin_can_perform_crud_on_semesters()
    {
        $super = User::factory()->create(['role' => 'superadmin']);
        $ay = AcademicYear::first();

        $this->actingAs($super)
            ->get(route('admin.semesters.index'))
            ->assertStatus(200)
            ->assertSee('Semester');

        $this->actingAs($super)
            ->get(route('admin.semesters.create'))
            ->assertStatus(200)
            ->assertSee('Tambah Semester');

        $resp = $this->actingAs($super)->post(route('admin.semesters.store'), [
            'academic_year_id' => $ay->id,
            'semester_number' => 1,
            'semester_name' => 'Ganjil',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(5)->format('Y-m-d'),
            'is_active' => 1,
        ]);

        $resp->assertRedirect(route('admin.semesters.index'));
        $this->assertDatabaseHas('semesters', ['semester_name' => 'Ganjil', 'academic_year_id' => $ay->id]);

        $s = Semester::where('semester_name', 'Ganjil')->first();

        $this->actingAs($super)
            ->get(route('admin.semesters.edit', $s))
            ->assertStatus(200)
            ->assertSee('Edit Semester');

        $this->actingAs($super)
            ->put(route('admin.semesters.update', $s), [
                'academic_year_id' => $ay->id,
                'semester_number' => 2,
                'semester_name' => 'Genap',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(5)->format('Y-m-d'),
            ])->assertRedirect(route('admin.semesters.index'));

        $this->assertDatabaseHas('semesters', ['id' => $s->id, 'semester_name' => 'Genap']);

        $this->actingAs($super)
            ->delete(route('admin.semesters.destroy', $s))
            ->assertRedirect(route('admin.semesters.index'));

        $this->assertDatabaseMissing('semesters', ['id' => $s->id]);
    }

    public function test_non_superadmin_cannot_access_semester_crud()
    {
        $user = User::factory()->create(['role' => 'siswa']);
        $this->actingAs($user)
            ->get(route('admin.semesters.index'))
            ->assertStatus(403);
    }
}
