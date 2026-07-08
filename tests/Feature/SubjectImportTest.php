<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubjectImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_subjects()
    {
        $this->seed();
        $school = School::first();

        // create a major to reference
        $major = \App\Models\Major::create(['school_id' => $school->id, 'major_code' => 'IPA', 'major_name' => 'IPA', 'is_active' => true]);

        $csv = "school_id,major_id,subject_code,subject_name,description,kkm,is_active\n";
        $csv .= "{$school->id},{$major->id},MATH,Matematika,Basic math,75,1\n";
        $csv .= "{$school->id},{$major->id},PHY,Fisika,Physics,70,1\n";

        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('subjects.csv', $csv);

        $admin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)
            ->post(route('admin.subjects.import'), ['csv' => $file]);

        $response->assertRedirect(route('admin.subjects.index'));

        $this->assertDatabaseHas('subjects', ['subject_code' => 'MATH', 'subject_name' => 'Matematika']);
        $this->assertDatabaseHas('subjects', ['subject_code' => 'PHY', 'subject_name' => 'Fisika']);
    }
}
