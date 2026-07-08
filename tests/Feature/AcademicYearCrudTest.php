<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // seed basic data if needed
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    public function test_superadmin_can_perform_crud_on_academic_years()
    {
        $super = User::factory()->create(['role' => 'superadmin']);

        $this->actingAs($super)
            ->get(route('admin.academic-years.index'))
            ->assertStatus(200)
            ->assertSee('Tahun Ajaran');

        $this->actingAs($super)
            ->get(route('admin.academic-years.create'))
            ->assertStatus(200)
            ->assertSee('Tambah Tahun Ajaran');

        $response = $this->actingAs($super)
            ->post(route('admin.academic-years.store'), [
                'year' => '2025/2026',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(12)->format('Y-m-d'),
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.academic-years.index'));
        $this->assertDatabaseHas('academic_years', ['year' => '2025/2026']);

        $ay = AcademicYear::where('year', '2025/2026')->first();
        $this->actingAs($super)
            ->get(route('admin.academic-years.edit', $ay))
            ->assertStatus(200)
            ->assertSee('Edit Tahun Ajaran');

        $this->actingAs($super)
            ->put(route('admin.academic-years.update', $ay), [
                'year' => '2025/2026',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(11)->format('Y-m-d'),
                'is_active' => 0,
            ])->assertRedirect(route('admin.academic-years.index'));

        $this->assertDatabaseHas('academic_years', ['id' => $ay->id, 'is_active' => 0]);

        $this->actingAs($super)
            ->delete(route('admin.academic-years.destroy', $ay))
            ->assertRedirect(route('admin.academic-years.index'));

        $this->assertDatabaseMissing('academic_years', ['id' => $ay->id]);
    }

    public function test_validation_error_on_duplicate_year_for_same_school()
    {
        $super = User::factory()->create(['role' => 'superadmin']);

        // DatabaseSeeder already creates '2024/2025', so just try to create a duplicate
        $resp = $this->actingAs($super)
            ->post(route('admin.academic-years.store'), [
                'year' => '2024/2025',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addYear()->format('Y-m-d'),
            ]);

        $resp->assertSessionHasErrors('year');
    }

    public function test_non_superadmin_cannot_access_routes_if_role_restricted()
    {
        $user = User::factory()->create(['role' => 'siswa']);
        $this->actingAs($user)
            ->get(route('admin.academic-years.index'))
            ->assertStatus(403);
    }
}
