<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class StudentPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function createSuperAdmin()
    {
        return User::create([
            'name' => 'Test Admin',
            'email' => 'testadminphoto@example.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);
    }

    public function test_store_accepts_photo_and_saves_path()
    {
        $user = $this->createSuperAdmin();
        $school = School::create(['name' => 'Sekolah Test', 'type' => 'SMA']);

        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $post = [
            'school_id' => $school->id,
            'nisn' => '20219998',
            'nis' => '9988',
            'full_name' => 'Foto Test',
            'gender' => 'L',
            'entry_year' => '2021',
            'photo' => $file,
        ];

        $this->actingAs($user)
            ->post(route('admin.students.store'), $post)
            ->assertRedirect(route('admin.students.index'));

        $student = \App\Models\Student::where('nisn', '20219998')->first();
        $this->assertNotNull($student->photo);
        Storage::disk('public')->assertExists($student->photo);
    }
}
