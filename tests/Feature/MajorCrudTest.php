<?php

namespace Tests\Feature;

use App\Models\Major;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MajorCrudTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    public function test_superadmin_can_perform_crud_on_majors()
    {
        $super = User::factory()->create(['role' => 'superadmin']);
        $school = School::first();

        $this->actingAs($super)
            ->get(route('admin.majors.index'))
            ->assertStatus(200)
            ->assertSee('Jurusan');

        $this->actingAs($super)
            ->get(route('admin.majors.create'))
            ->assertStatus(200)
            ->assertSee('Tambah Jurusan');

        $uniqueCode = 'MJR' . mt_rand(1000, 9999);
        $resp = $this->actingAs($super)
            ->post(route('admin.majors.store'), [
                'school_id' => $school->id,
                'major_code' => $uniqueCode,
                'major_name' => 'Ilmu Pengetahuan Alam',
                'description' => 'Program IPA',
                'is_active' => 1,
            ]);

        $this->assertDatabaseHas('majors', ['major_code' => $uniqueCode, 'major_name' => 'Ilmu Pengetahuan Alam']);

        $resp->assertRedirect(route('admin.majors.index'));
        $this->assertDatabaseHas('majors', ['major_code' => 'IPA', 'major_name' => 'Ilmu Pengetahuan Alam']);

        $major = Major::where('major_code', 'IPA')->first();

        $this->actingAs($super)
            ->get(route('admin.majors.edit', $major))
            ->assertStatus(200)
            ->assertSee('Edit Jurusan');

        $updateCode = 'MJRUPD' . mt_rand(100, 999);
        $this->actingAs($super)
            ->put(route('admin.majors.update', $major), [
                'school_id' => $school->id,
                'major_code' => $updateCode,
                'major_name' => 'Ilmu Pengetahuan Sosial',
            ])->assertRedirect(route('admin.majors.index'));

        $this->assertDatabaseHas('majors', ['id' => $major->id, 'major_code' => $updateCode]);

        $this->actingAs($super)
            ->delete(route('admin.majors.destroy', $major))
            ->assertRedirect(route('admin.majors.index'));

        $this->assertDatabaseMissing('majors', ['id' => $major->id]);
    }

    public function test_non_superadmin_cannot_access_majors()
    {
        $user = User::factory()->create(['role' => 'siswa']);
        $this->actingAs($user)
            ->get(route('admin.majors.index'))
            ->assertStatus(403);
    }
}
