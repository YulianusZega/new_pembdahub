<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\User as UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_students_and_users()
    {
        $this->seed(); // ensure schools exist from DatabaseSeeder

        $school = School::first();

        $csv = "school_id,nisn,nis,full_name,gender,birth_place,birth_date,religion,previous_school,guardian_name,guardian_phone,guardian_occupation,guardian_address,hobby,health_history,entry_year,address,email\n";
        $csv .= "{$school->id},20240001,1001,Import Siswa,L,Medan,2008-05-01,Islam,SMP Negeri 1,Ayah Import,08123456789,Pegawai,\"Jl. Merdeka 1\",Sepakbola,Sehat,2024,\"Jl. Merdeka 1\",import1@example.test\n";
        $csv .= "{$school->id},20240002,1002,Import Siswa Dua,P,Bandung,2007-08-02,Kristen,SMP Negeri 2,Orang Tua Dua,08129876543,Buruh,\"Jl. Sudirman 2\",Basket,Asma,2024,\"Jl. Sudirman 2\",\n";

        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('students.csv', $csv);

        $admin = \App\Models\User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)
            ->post(route('admin.students.import'), ['csv' => $file]);

        $response->assertRedirect(route('admin.students.index'));

        $this->assertDatabaseHas('students', [
            'nisn' => '20240001',
            'full_name' => 'Import Siswa',
            'birth_place' => 'Medan',
            'birth_date' => '2008-05-01 00:00:00',
            'religion' => 'Islam',
            'previous_school' => 'SMP Negeri 1',
            'guardian_name' => 'Ayah Import',
            'guardian_phone' => '08123456789',
            'guardian_occupation' => 'Pegawai',
            'guardian_address' => 'Jl. Merdeka 1',
            'hobby' => 'Sepakbola',
            'health_history' => 'Sehat',
            'address' => 'Jl. Merdeka 1',
        ]);
        $this->assertDatabaseHas('users', ['email' => 'import1@example.test']);

        // second row had no email, should still create user with nisn-based email and fields persisted
        $this->assertDatabaseHas('students', [
            'nisn' => '20240002',
            'birth_place' => 'Bandung',
            'birth_date' => '2007-08-02 00:00:00',
            'religion' => 'Kristen',
            'previous_school' => 'SMP Negeri 2',
            'guardian_name' => 'Orang Tua Dua',
            'guardian_phone' => '08129876543',
            'guardian_occupation' => 'Buruh',
            'guardian_address' => 'Jl. Sudirman 2',
            'hobby' => 'Basket',
            'health_history' => 'Asma',
            'address' => 'Jl. Sudirman 2',
        ]);
        $this->assertDatabaseHas('users', ['email' => '20240002@students.local']);

        // confirm association
        $student = Student::where('nisn', '20240001')->first();
        $this->assertNotNull($student->user_id);
        $this->assertDatabaseHas('users', ['id' => $student->user_id, 'role' => 'siswa']);
    }

    public function test_download_sample_excel_works()
    {
        $admin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)
            ->get(route('admin.students.import.sample'));

        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_import_excel_creates_students_and_users()
    {
        $this->seed();
        $school = School::first();

        // Gunakan disk local untuk menyimpan file excel sementara
        Storage::fake('local');

        \Maatwebsite\Excel\Facades\Excel::store(
            new \App\Exports\StudentSampleExport,
            'test_students.xlsx',
            'local'
        );

        $filePath = Storage::disk('local')->path('test_students.xlsx');
        $file = new \Illuminate\Http\UploadedFile(
            $filePath,
            'test_students.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $admin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)
            ->post(route('admin.students.import'), ['file' => $file]);

        $response->assertRedirect(route('admin.students.index'));
        
        $this->assertDatabaseHas('students', [
            'nisn' => '20210001',
            'full_name' => 'Andi Wijaya',
        ]);
        
        $this->assertDatabaseHas('users', [
            'email' => 'andi@example.com',
            'name' => 'Andi Wijaya',
            'role' => 'siswa',
        ]);
    }
}
