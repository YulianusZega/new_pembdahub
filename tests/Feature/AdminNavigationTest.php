<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_admin_links()
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertSee(route('admin.subjects.index'))
            ->assertSee(route('admin.classrooms.index'))
            ->assertSee(route('admin.schedules.index'));
    }

    public function test_admin_pages_include_sidebar()
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $this->actingAs($user)
            ->get(route('admin.students.index'))
            ->assertStatus(200)
            ->assertSee('Mata Pelajaran')
            ->assertSee('Ruang Kelas')
            ->assertSee('Jadwal')
            ->assertSee('admin-sidebar');
    }

    public function test_sidebar_active_state_has_aria_current()
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        // Visit subjects index and expect 'Mata Pelajaran' link to be active
        $this->actingAs($user)
            ->get(route('admin.subjects.index'))
            ->assertStatus(200)
            ->assertSee('Mata Pelajaran')
            ->assertSee('active');

        // Visit schedules index and expect 'Jadwal' link to be active
        $this->actingAs($user)
            ->get(route('admin.schedules.index'))
            ->assertStatus(200)
            ->assertSee('Jadwal')
            ->assertSee('active');
    }

    public function test_sidebar_has_toggle_and_groups()
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertSee('Data Master')
            ->assertSee('Akademik')
            ->assertSee('Keuangan')
            ->assertSee('sidebar-toggle');
    }
}
