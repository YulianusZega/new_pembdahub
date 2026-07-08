<?php

namespace Tests\Feature;

use Database\Seeders\ParentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_seeder_creates_parents_and_users()
    {
        // Create minimal data: a school and a student
        $school = \App\Models\School::create(['name' => 'Sekolah Parent Test', 'type' => 'SMA']);
        $user = \App\Models\User::create([
            'name' => 'Student User',
            'email' => 'student@example.test',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'siswa',
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        \App\Models\Student::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'nisn' => '99990001',
            'nis' => '9001',
            'full_name' => 'Student ParentTest',
            'gender' => 'L',
            'entry_year' => 2022,
            'status' => 'aktif',
        ]);

        // ensure parent seeder runs
        $this->seed(ParentSeeder::class);

        $parentsCount = \DB::table('parents')->count();
        $usersCount = \DB::table('users')->where('role', 'orang_tua')->count();

        $this->assertGreaterThan(0, $parentsCount);
        $this->assertGreaterThan(0, $usersCount);
    }
}
