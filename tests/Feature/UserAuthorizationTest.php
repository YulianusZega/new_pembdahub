<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_admin_can_access_users_index()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($guru)
            ->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_user()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $school = School::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'username' => 'newuser',
                'email' => 'newuser@test.com',
                'password' => 'Password123A',
                'password_confirmation' => 'Password123A',
                'role' => 'guru',
                'school_id' => $school->id,
                'is_active' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);
    }

    /** @test */
    public function guru_cannot_create_user()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $school = School::factory()->create();

        $response = $this->actingAs($guru)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'newuser@test.com',
                'password' => 'password123',
                'role' => 'siswa',
                'school_id' => $school->id,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_delete_superadmin()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $superadmin));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_non_superadmin_users()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $guru));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $guru->id]);
    }

    /** @test */
    public function superadmin_can_delete_any_user()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin_sekolah']);

        $response = $this->actingAs($superadmin)
            ->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    /** @test */
    public function admin_cannot_update_superadmin()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $superadmin), [
                'name' => 'Hacked Name',
                'username' => $superadmin->username,
                'email' => $superadmin->email,
                'role' => $superadmin->role,
                'school_id' => $superadmin->school_id,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_update_own_profile()
    {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)
            ->put(route('admin.users.update', $user), [
                'name' => 'Updated Name',
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'school_id' => $user->school_id,
                'is_active' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    /** @test */
    public function user_can_reset_own_password()
    {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)
            ->post(route('admin.users.reset-password', $user), [
                'password' => 'NewPassword1A',
                'password_confirmation' => 'NewPassword1A',
            ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword1A', $user->password));
    }

    /** @test */
    public function admin_can_reset_any_user_password()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.reset-password', $guru), [
                'password' => 'NewPassword1A',
                'password_confirmation' => 'NewPassword1A',
            ]);

        $response->assertRedirect();
        $guru->refresh();
        $this->assertTrue(Hash::check('NewPassword1A', $guru->password));
    }

    /** @test */
    public function regular_user_cannot_reset_others_password()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($guru)
            ->post(route('admin.users.reset-password', $otherUser), [
                'password' => 'hackedpassword',
                'password_confirmation' => 'hackedpassword',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function role_hierarchy_prevents_unauthorized_updates()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        // Admin can update guru
        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $guru), [
                'name' => 'Updated Guru',
                'username' => $guru->username,
                'email' => $guru->email,
                'role' => 'guru',
                'school_id' => $guru->school_id,
                'is_active' => true,
            ]);
        $response->assertRedirect();

        // Guru cannot update siswa
        $response = $this->actingAs($guru)
            ->put(route('admin.users.update', $siswa), [
                'name' => 'Hacked Siswa',
            ]);
        $response->assertStatus(403);
    }
}
