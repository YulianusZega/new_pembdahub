<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_students_pages_use_admin_layout()
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $this->actingAs($user)
            ->get(route('admin.students.index'))
            ->assertStatus(200)
            ->assertSee('Daftar Siswa')
            ->assertSee('Logout');

        $this->actingAs($user)
            ->get(route('admin.students.create'))
            ->assertStatus(200)
            ->assertSee('Tambah Siswa')
            ->assertSee('Logout');
    }
}
