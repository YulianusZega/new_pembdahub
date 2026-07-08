<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearToggleTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    public function test_superadmin_can_toggle_active_status_and_it_unsets_others()
    {
        $super = User::factory()->create(['role' => 'superadmin']);

        $ay1 = AcademicYear::create([
            'year' => '2022/2023',
            'start_date' => '2022-07-01',
            'end_date' => '2023-06-30',
            'is_active' => false,
        ]);

        $ay2 = AcademicYear::create([
            'year' => '2023/2024',
            'start_date' => '2023-07-01',
            'end_date' => '2024-06-30',
            'is_active' => true,
        ]);

        $this->actingAs($super)
            ->post(route('admin.academic-years.toggle', $ay1), ['set_active' => 1], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson(['is_active' => true]);

        $this->assertDatabaseHas('academic_years', ['id' => $ay1->id, 'is_active' => 1]);
        $this->assertDatabaseHas('academic_years', ['id' => $ay2->id, 'is_active' => 0]);
    }

    public function test_non_superadmin_cannot_toggle()
    {
        $user = User::factory()->create(['role' => 'siswa']);
        $ay = AcademicYear::first();
        $this->actingAs($user)
            ->post(route('admin.academic-years.toggle', $ay), ['set_active' => 1])
            ->assertStatus(403);
    }
}
