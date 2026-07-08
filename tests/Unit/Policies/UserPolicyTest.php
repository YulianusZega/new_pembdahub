<?php

namespace Tests\Unit\Policies;

use Tests\TestCase;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    private UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserPolicy();
    }

    /** @test */
    public function only_admin_can_view_any_users()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($this->policy->viewAny($superadmin));
        $this->assertTrue($this->policy->viewAny($admin));
        $this->assertFalse($this->policy->viewAny($guru));
    }

    /** @test */
    public function admin_can_view_any_user()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $otherUser = User::factory()->create();

        $this->assertTrue($this->policy->view($admin, $otherUser));
    }

    /** @test */
    public function user_can_view_own_profile()
    {
        $user = User::factory()->create(['role' => 'guru']);
        $otherUser = User::factory()->create();

        $this->assertTrue($this->policy->view($user, $user));
        $this->assertFalse($this->policy->view($user, $otherUser));
    }

    /** @test */
    public function only_admin_can_create_users()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($this->policy->create($superadmin));
        $this->assertTrue($this->policy->create($admin));
        $this->assertFalse($this->policy->create($guru));
    }

    /** @test */
    public function superadmin_can_update_any_user()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $otherUser = User::factory()->create();

        $this->assertTrue($this->policy->update($superadmin, $otherUser));
    }

    /** @test */
    public function admin_cannot_update_superadmin()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $this->assertFalse($this->policy->update($admin, $superadmin));
    }

    /** @test */
    public function admin_can_update_non_superadmin_users()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($this->policy->update($admin, $guru));
    }

    /** @test */
    public function user_can_update_own_profile()
    {
        $user = User::factory()->create(['role' => 'guru']);
        $otherUser = User::factory()->create();

        $this->assertTrue($this->policy->update($user, $user));
        $this->assertFalse($this->policy->update($user, $otherUser));
    }

    /** @test */
    public function superadmin_can_delete_any_user()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin_sekolah']);

        $this->assertTrue($this->policy->delete($superadmin, $admin));
    }

    /** @test */
    public function admin_cannot_delete_superadmin()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $this->assertFalse($this->policy->delete($admin, $superadmin));
    }

    /** @test */
    public function admin_can_delete_non_superadmin_users()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($this->policy->delete($admin, $guru));
    }

    /** @test */
    public function regular_user_cannot_delete_users()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $otherUser = User::factory()->create();

        $this->assertFalse($this->policy->delete($guru, $otherUser));
    }

    /** @test */
    public function admin_can_reset_password()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $otherUser = User::factory()->create();

        $this->assertTrue($this->policy->resetPassword($admin, $otherUser));
    }

    /** @test */
    public function user_can_reset_own_password()
    {
        $user = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($this->policy->resetPassword($user, $user));
    }

    /** @test */
    public function regular_user_cannot_reset_others_password()
    {
        $user = User::factory()->create(['role' => 'guru']);
        $otherUser = User::factory()->create();

        $this->assertFalse($this->policy->resetPassword($user, $otherUser));
    }

    /** @test */
    public function only_superadmin_can_manage_roles()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $otherUser = User::factory()->create();

        $this->assertTrue($this->policy->manageRoles($superadmin, $otherUser));
        $this->assertFalse($this->policy->manageRoles($admin, $otherUser));
    }

    /** @test */
    public function role_hierarchy_is_enforced()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        // Admin can manage guru and siswa
        $this->assertTrue($this->policy->update($admin, $guru));
        $this->assertTrue($this->policy->update($admin, $siswa));
        $this->assertTrue($this->policy->delete($admin, $guru));
        $this->assertTrue($this->policy->delete($admin, $siswa));

        // Guru cannot manage other users
        $this->assertFalse($this->policy->update($guru, $siswa));
        $this->assertFalse($this->policy->delete($guru, $siswa));
    }
}
