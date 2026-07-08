<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminMenuRoleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_sees_schools_link_and_search()
    {
        $user = User::factory()->create(['role' => 'superadmin']);
        $this->assertEquals('superadmin', $user->role);
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $response = $this->get(route('admin.students.index'));
        $response->assertStatus(200);
        $response->assertSee('Sekolah');
    }

    public function test_admin_sekolah_does_not_see_schools_but_sees_students()
    {
        $user = User::factory()->create(['role' => 'admin_sekolah']);
        $this->assertEquals('admin_sekolah', $user->role);
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $response = $this->get(route('admin.students.index'));
        $response->assertStatus(200);
        $response->assertDontSee('href="' . route('admin.schools.index') . '"', false);
        $response->assertSee('href="' . route('admin.students.index') . '"', false);
    }

    public function test_guru_sees_jadwal_and_not_schools()
    {
        $user = User::factory()->create(['role' => 'guru']);
        $this->assertEquals('guru', $user->role);
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $response = $this->get(route('admin.students.index'));
        $response->assertStatus(200);
        $response->assertSee('href="' . route('admin.schedules.grid') . '"', false);
        $response->assertDontSee('href="' . route('admin.schools.index') . '"', false);
    }
}
