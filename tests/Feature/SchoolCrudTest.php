<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function superadmin_can_create_school_and_is_redirected_to_index()
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($user)
            ->post(route('admin.schools.store'), [
                'name' => 'Sekolah Test',
                'type' => 'SMA',
                'npsn' => '12345',
                'address' => 'Jl. Test',
                'city' => 'Kota',
                'province' => 'Prov',
                'postal_code' => '12345',
                'phone' => '08123456789',
                'email' => 'test@school.test',
                'website' => 'https://example.test',
                'principal_name' => 'Kepala Test',
                'school_year_start' => 2024,
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.schools.index'));
        $this->assertDatabaseHas('schools', ['name' => 'Sekolah Test']);
    }

    /** @test */
    public function superadmin_can_update_school_and_is_redirected_to_index()
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $school = School::create([
            'name' => 'Sekolah Lama',
            'type' => 'SMA',
            'is_active' => true
        ]);

        $response = $this->actingAs($user)
            ->put(route('admin.schools.update', $school), [
                'name' => 'Sekolah Baru',
                'type' => 'SMK',
                'npsn' => '99999',
                'address' => 'Jl. Baru',
                'city' => 'KotaBaru',
                'province' => 'ProvBaru',
                'postal_code' => '54321',
                'phone' => '08987654321',
                'email' => 'updated@school.test',
                'website' => 'https://updated.test',
                'principal_name' => 'Kepala Baru',
                'school_year_start' => 2025,
                'is_active' => 0,
            ]);

        $response->assertRedirect(route('admin.schools.index'));
        $this->assertDatabaseHas('schools', ['id' => $school->id, 'name' => 'Sekolah Baru', 'type' => 'SMK']);
    }

    /** @test */
    public function superadmin_can_delete_school_and_is_redirected_to_index()
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $school = School::create([
            'name' => 'Sekolah Untuk Dihapus',
            'type' => 'SMP',
            'is_active' => true
        ]);

        $response = $this->actingAs($user)
            ->delete(route('admin.schools.destroy', $school));

        $response->assertRedirect(route('admin.schools.index'));
        $this->assertDatabaseMissing('schools', ['id' => $school->id]);
    }
}
