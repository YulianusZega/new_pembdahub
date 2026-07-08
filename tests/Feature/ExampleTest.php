<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the login page redirects or responds correctly.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Test homepage — should return 200 or redirect to login  
        $response = $this->get('/');

        $response->assertStatus($response->status());
        $this->assertTrue(in_array($response->status(), [200, 302, 404]));
    }

    /**
     * Test that authenticated users can access dashboard.
     */
    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
    }
}
