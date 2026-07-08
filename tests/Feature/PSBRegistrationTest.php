<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Applicant;
use App\Models\ApplicantAchievement;
use App\Models\ApplicantDocument;
use App\Models\ProgramKeahlian;
use App\Models\KonsentrasiKeahlian;
use App\Models\RegistrationWave;
use App\Models\School;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PSBRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected School $smpSchool;
    protected School $smaSchool;
    protected School $smkSchool;
    protected AcademicYear $academicYear;
    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create 3 schools
        $this->smpSchool = School::create([
            'name' => 'SMPS Pembda 2 Gunungsitoli',
            'type' => 'SMP',
            'npsn' => '20220001',
            'address' => 'Jl. Pelita No.31',
            'city' => 'Gunungsitoli',
            'province' => 'Sumatera Utara',
            'postal_code' => '22812',
            'phone' => '082168532567',
            'email' => 'info@smp2pembda.sch.id',
            'is_active' => true,
            'requires_test' => false,
        ]);

        $this->smaSchool = School::create([
            'name' => 'SMA Pembda 1 Gunungsitoli',
            'type' => 'SMA',
            'npsn' => '20220002',
            'address' => 'Jl. Pelita No.31',
            'city' => 'Gunungsitoli',
            'province' => 'Sumatera Utara',
            'postal_code' => '22812',
            'phone' => '082168532567',
            'email' => 'info@sma1pembda.sch.id',
            'is_active' => true,
            'requires_test' => true,
            'test_type' => 'Tes Tertulis',
        ]);

        $this->smkSchool = School::create([
            'name' => 'SMKS Pembda Nias',
            'type' => 'SMK',
            'npsn' => '20220003',
            'address' => 'Jl. Pelita No.31',
            'city' => 'Gunungsitoli',
            'province' => 'Sumatera Utara',
            'postal_code' => '22812',
            'phone' => '082168532567',
            'email' => 'info@smkpembda.sch.id',
            'is_active' => true,
            'requires_test' => false,
        ]);

        // Create active academic year
        $this->academicYear = AcademicYear::create([
            'year' => 'TP. 2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'semester_start' => '2025-07-01',
            'semester_end' => '2025-12-15',
            'is_active' => true,
        ]);

        // Create superadmin
        $this->superadmin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);
    }

    // ========================================
    // PUBLIC REGISTRATION PAGE TESTS
    // ========================================

    /** @test */
    public function public_registration_page_loads_successfully()
    {
        $response = $this->get(route('public.registration.index'));

        $response->assertStatus(200);
        $response->assertViewIs('public.registration');
        $response->assertViewHas('schools');
        $response->assertViewHas('academicYear');
    }

    /** @test */
    public function public_registration_page_shows_error_when_no_active_academic_year()
    {
        // Deactivate all academic years
        AcademicYear::query()->update(['is_active' => false]);

        $response = $this->get(route('public.registration.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function public_registration_page_shows_active_schools()
    {
        $response = $this->get(route('public.registration.index'));

        $response->assertStatus(200);
        $response->assertSee('SMPS Pembda 2');
        $response->assertSee('SMA Pembda 1');
        $response->assertSee('SMKS Pembda Nias');
    }

    /** @test */
    public function public_registration_page_shows_registration_waves()
    {
        // Create registration wave
        $wave = RegistrationWave::create([
            'school_id' => $this->smpSchool->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Gelombang 1',
            'wave_number' => 1,
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(30),
            'quota' => 100,
            'registered_count' => 0,
            'is_active' => true,
        ]);

        $response = $this->get(route('public.registration.index'));

        $response->assertStatus(200);
        $response->assertViewHas('schools', function ($schools) use ($wave) {
            $smp = $schools->firstWhere('id', $wave->school_id);
            return $smp && $smp->registrationWaves->contains($wave->id);
        });
    }

    // ========================================
    // PUBLIC REGISTRATION STORE (SMP) TESTS
    // ========================================

    /** @test */
    public function can_register_smp_student_reguler_path()
    {
        Storage::fake('public');

        $data = $this->getBaseSMPRegistrationData();

        $response = $this->post(route('public.registration.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('applicants', [
            'nisn' => '1234567890',
            'full_name' => 'Budi Santoso',
            'school_id' => $this->smpSchool->id,
            'status' => 'submitted',
            'admission_path' => 'reguler',
        ]);

        // Check registration number format: SMP-YY-XXXX
        $applicant = Applicant::where('nisn', '1234567890')->first();
        $this->assertNotNull($applicant);
        $this->assertMatchesRegularExpression('/^SMP-\d{2}-\d{4}$/', $applicant->registration_number);
    }

    /** @test */
    public function can_register_smp_student_prestasi_path()
    {
        Storage::fake('public');

        $data = $this->getBaseSMPRegistrationData();
        $data['admission_path'] = 'prestasi';
        $data['achievement_rank'] = '1';
        $data['achievement_grade'] = '6';
        $data['achievement_year'] = '2025/2026';
        $data['achievement_school'] = 'SD Negeri 1 Gunungsitoli';
        $data['achievement_certificate'] = UploadedFile::fake()->create('sertifikat.pdf', 1024, 'application/pdf');

        $response = $this->post(route('public.registration.store'), $data);

        $response->assertRedirect();

        $applicant = Applicant::where('nisn', '1234567890')->first();
        $this->assertNotNull($applicant);
        $this->assertEquals('prestasi', $applicant->admission_path);

        // Check achievement was created
        $achievement = ApplicantAchievement::where('applicant_id', $applicant->id)->first();
        $this->assertNotNull($achievement);
        $this->assertEquals('Juara 1 Kelas 6', $achievement->achievement_name);
        $this->assertEquals('academic', $achievement->achievement_type);
        $this->assertEquals(20.0, (float)$achievement->points); // Juara 1 = 20 points
    }

    /** @test */
    public function registration_fails_with_duplicate_nisn()
    {
        // Create existing applicant
        Applicant::create([
            'school_id' => $this->smpSchool->id,
            'academic_year_id' => $this->academicYear->id,
            'registration_number' => 'SMP-26-0001',
            'nisn' => '1234567890',
            'full_name' => 'Existing Student',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'religion' => 'Kristen',
            'address' => 'Jl. Test',
            'father_name' => 'Test',
            'mother_name' => 'Test',
            'previous_school' => 'SD Test',
            'admission_path' => 'reguler',
            'status' => 'submitted',
            'submission_date' => now(),
        ]);

        $data = $this->getBaseSMPRegistrationData();

        $response = $this->post(route('public.registration.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors('nisn');
    }

    /** @test */
    public function registration_fails_without_required_fields()
    {
        $response = $this->post(route('public.registration.store'), []);

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'school_id',
            'nisn',
            'full_name',
            'gender',
            'birth_place',
            'birth_date',
            'religion',
            'address',
            'father_name',
            'mother_name',
            'previous_school',
            'admission_path',
        ]);
    }

    /** @test */
    public function registration_generates_unique_registration_numbers()
    {
        // Create first applicant
        Applicant::create([
            'school_id' => $this->smpSchool->id,
            'academic_year_id' => $this->academicYear->id,
            'registration_number' => 'SMP-26-0001',
            'nisn' => '0000000001',
            'full_name' => 'First Student',
            'gender' => 'L',
            'birth_place' => 'Test',
            'birth_date' => '2010-01-01',
            'religion' => 'Kristen',
            'address' => 'Jl. Test',
            'father_name' => 'Test',
            'mother_name' => 'Test',
            'previous_school' => 'SD Test',
            'admission_path' => 'reguler',
            'status' => 'submitted',
            'submission_date' => now(),
        ]);

        $data = $this->getBaseSMPRegistrationData();
        $this->post(route('public.registration.store'), $data);

        $newApplicant = Applicant::where('nisn', '1234567890')->first();
        $this->assertNotNull($newApplicant);
        $this->assertNotEquals('SMP-26-0001', $newApplicant->registration_number);
    }

    /** @test */
    public function registration_with_photo_upload()
    {
        Storage::fake('public');

        $data = $this->getBaseSMPRegistrationData();
        $data['photo'] = UploadedFile::fake()->image('foto.jpg', 800, 600);

        $response = $this->post(route('public.registration.store'), $data);

        $applicant = Applicant::where('nisn', '1234567890')->first();
        $this->assertNotNull($applicant);
        $this->assertNotNull($applicant->photo_path);

        Storage::disk('public')->assertExists($applicant->photo_path);
    }

    // ========================================
    // SMK REGISTRATION TESTS
    // ========================================

    /** @test */
    public function smk_registration_requires_program_keahlian()
    {
        $data = $this->getBaseSMPRegistrationData();
        $data['school_id'] = $this->smkSchool->id;
        // Don't send program_keahlian_id

        $response = $this->post(route('public.registration.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors('program_keahlian_id');
    }

    /** @test */
    public function smk_registration_with_program_keahlian_succeeds()
    {
        Storage::fake('public');

        $program = ProgramKeahlian::create([
            'school_id' => $this->smkSchool->id,
            'nama' => 'Teknik Komputer dan Jaringan',
            'kode' => 'TKJ',
            'is_active' => true,
        ]);

        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'nama' => 'Teknik Jaringan Komputer',
            'kode' => 'TJK',
            'is_active' => true,
        ]);

        $data = $this->getBaseSMPRegistrationData();
        $data['school_id'] = $this->smkSchool->id;
        $data['program_keahlian_id'] = $program->id;
        $data['konsentrasi_keahlian_id'] = $konsentrasi->id;

        $response = $this->post(route('public.registration.store'), $data);

        $response->assertRedirect();

        $applicant = Applicant::where('nisn', '1234567890')->first();
        $this->assertNotNull($applicant);
        $this->assertEquals($program->id, $applicant->program_keahlian_id);
        $this->assertEquals($konsentrasi->id, $applicant->konsentrasi_keahlian_id);
        $this->assertMatchesRegularExpression('/^SMK-\d{2}-\d{4}$/', $applicant->registration_number);
    }

    // ========================================
    // SMA REGISTRATION TESTS
    // ========================================

    /** @test */
    public function can_register_sma_student()
    {
        $data = $this->getBaseSMPRegistrationData();
        $data['school_id'] = $this->smaSchool->id;

        $response = $this->post(route('public.registration.store'), $data);

        $response->assertRedirect();

        $applicant = Applicant::where('nisn', '1234567890')->first();
        $this->assertNotNull($applicant);
        $this->assertMatchesRegularExpression('/^SMA-\d{2}-\d{4}$/', $applicant->registration_number);
    }

    // ========================================
    // CHECK STATUS TESTS
    // ========================================

    /** @test */
    public function check_status_page_loads()
    {
        $response = $this->get(route('public.registration.check'));

        $response->assertStatus(200);
        $response->assertViewIs('public.check-status');
    }

    /** @test */
    public function can_check_status_with_valid_data()
    {
        $applicant = Applicant::create([
            'school_id' => $this->smpSchool->id,
            'academic_year_id' => $this->academicYear->id,
            'registration_number' => 'SMP-26-0001',
            'nisn' => '1234567890',
            'full_name' => 'Budi Santoso',
            'gender' => 'L',
            'birth_place' => 'Gunungsitoli',
            'birth_date' => '2010-05-15',
            'religion' => 'Kristen',
            'address' => 'Jl. Test',
            'father_name' => 'Ama Budi',
            'mother_name' => 'Ina Budi',
            'previous_school' => 'SD Test',
            'admission_path' => 'reguler',
            'status' => 'submitted',
            'submission_date' => now(),
        ]);

        $response = $this->post(route('public.registration.check.submit'), [
            'registration_number' => 'SMP-26-0001',
            'nisn' => '1234567890',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('public.status-result');
        $response->assertViewHas('applicant');
        $response->assertSee('Budi Santoso');
    }

    /** @test */
    public function check_status_fails_with_wrong_credentials()
    {
        $response = $this->post(route('public.registration.check.submit'), [
            'registration_number' => 'INVALID-001',
            'nisn' => '9999999999',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ========================================
    // DOCUMENT UPLOAD TESTS
    // ========================================

    /** @test */
    public function can_upload_document_for_applicant()
    {
        Storage::fake('public');

        $applicant = $this->createSubmittedApplicant();

        $response = $this->post(route('public.registration.upload-document'), [
            'applicant_id' => $applicant->id,
            'registration_number' => $applicant->registration_number,
            'nisn' => $applicant->nisn,
            'document_type' => 'kk',
            'document_file' => UploadedFile::fake()->create('kk.pdf', 1024, 'application/pdf'),
        ]);

        $response->assertRedirect(route('public.registration.check'));

        $this->assertDatabaseHas('applicant_documents', [
            'applicant_id' => $applicant->id,
            'document_type' => 'kk',
            'verified' => false,
        ]);
    }

    /** @test */
    public function cannot_upload_duplicate_document_type()
    {
        Storage::fake('public');

        $applicant = $this->createSubmittedApplicant();

        // Upload first document
        ApplicantDocument::create([
            'applicant_id' => $applicant->id,
            'document_type' => 'kk',
            'file_name' => 'kk.pdf',
            'file_path' => 'documents/test.pdf',
            'file_size' => 1024,
            'verified' => false,
        ]);

        // Try to upload duplicate
        $response = $this->post(route('public.registration.upload-document'), [
            'applicant_id' => $applicant->id,
            'registration_number' => $applicant->registration_number,
            'nisn' => $applicant->nisn,
            'document_type' => 'kk',
            'document_file' => UploadedFile::fake()->create('kk2.pdf', 1024, 'application/pdf'),
        ]);

        $response->assertRedirect(route('public.registration.check'));
        $response->assertSessionHas('document_error');
    }

    /** @test */
    public function document_upload_fails_with_invalid_applicant()
    {
        Storage::fake('public');

        $response = $this->post(route('public.registration.upload-document'), [
            'applicant_id' => 999,
            'registration_number' => 'FAKE-001',
            'nisn' => '0000000000',
            'document_type' => 'kk',
            'document_file' => UploadedFile::fake()->create('kk.pdf', 1024, 'application/pdf'),
        ]);

        $response->assertRedirect();
    }

    // ========================================
    // API ENDPOINTS TESTS
    // ========================================

    /** @test */
    public function api_returns_program_keahlian_for_school()
    {
        $program = ProgramKeahlian::create([
            'school_id' => $this->smkSchool->id,
            'nama' => 'Teknik Komputer dan Jaringan',
            'kode' => 'TKJ',
            'is_active' => true,
        ]);

        $response = $this->get(route('api.program', $this->smkSchool->id));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['nama' => 'Teknik Komputer dan Jaringan']);
    }

    /** @test */
    public function api_returns_konsentrasi_keahlian_for_program()
    {
        $program = ProgramKeahlian::create([
            'school_id' => $this->smkSchool->id,
            'nama' => 'TKJ',
            'kode' => 'TKJ',
            'is_active' => true,
        ]);

        $konsentrasi = KonsentrasiKeahlian::create([
            'program_keahlian_id' => $program->id,
            'nama' => 'Teknik Jaringan Komputer',
            'kode' => 'TJK',
            'is_active' => true,
        ]);

        $response = $this->get(route('api.konsentrasi', $program->id));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['nama' => 'Teknik Jaringan Komputer']);
    }

    // ========================================
    // ADMIN PSB MANAGEMENT TESTS
    // ========================================

    /** @test */
    public function admin_can_view_psb_index()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.psb.index');
    }

    /** @test */
    public function admin_can_view_psb_index_with_filters()
    {
        $this->createSubmittedApplicant();

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.index', [
                'school_id' => $this->smpSchool->id,
                'status' => 'submitted',
                'search' => 'Budi',
            ]));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_applicant_details()
    {
        $applicant = $this->createSubmittedApplicant();

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.show', $applicant));

        $response->assertStatus(200);
        $response->assertViewIs('admin.psb.show');
        $response->assertSee('Budi Santoso');
    }

    /** @test */
    public function admin_can_create_offline_registration()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.psb.create');
    }

    /** @test */
    public function admin_can_store_offline_registration()
    {
        $data = [
            'school_id' => $this->smpSchool->id,
            'nisn' => '9876543210',
            'full_name' => 'Offline Student',
            'gender' => 'P',
            'birth_place' => 'Gunungsitoli',
            'birth_date' => '2010-03-15',
            'religion' => 'Kristen',
            'address' => 'Jl. Offline No.1',
            'phone' => '081234567890',
            'father_name' => 'Ama Offline',
            'mother_name' => 'Ina Offline',
            'previous_school' => 'SD Negeri 1',
            'admission_path' => 'reguler',
        ];

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.store'), $data);

        $response->assertRedirect();

        $this->assertDatabaseHas('applicants', [
            'nisn' => '9876543210',
            'full_name' => 'Offline Student',
            'registration_type' => 'offline',
        ]);
    }

    /** @test */
    public function admin_can_store_offline_registration_with_skip_verification()
    {
        $data = [
            'school_id' => $this->smpSchool->id, // doesn't require test
            'nisn' => '9876543211',
            'full_name' => 'Direct Accept Student',
            'gender' => 'L',
            'birth_place' => 'Gunungsitoli',
            'birth_date' => '2010-06-01',
            'religion' => 'Kristen',
            'address' => 'Jl. Direct No.1',
            'father_name' => 'Ama Direct',
            'mother_name' => 'Ina Direct',
            'previous_school' => 'SD Negeri 2',
            'admission_path' => 'reguler',
            'skip_verification' => true,
        ];

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.store'), $data);

        $response->assertRedirect();

        $this->assertDatabaseHas('applicants', [
            'nisn' => '9876543211',
            'status' => 'accepted',
        ]);
    }

    // ========================================
    // PSB WORKFLOW TESTS
    // ========================================

    /** @test */
    public function admin_can_verify_payment()
    {
        $applicant = $this->createSubmittedApplicant();

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-payment', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $applicant->refresh();
        $this->assertEquals('payment_verified', $applicant->status);
        $this->assertNotNull($applicant->payment_verified_at);
    }

    /** @test */
    public function verify_payment_fails_if_status_is_not_submitted()
    {
        $applicant = $this->createSubmittedApplicant();
        $applicant->update(['status' => 'accepted']);

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-payment', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function admin_can_verify_document()
    {
        $applicant = $this->createSubmittedApplicant();
        $applicant->update([
            'status' => 'payment_verified',
            'payment_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-document', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $applicant->refresh();
        $this->assertEquals('document_verified', $applicant->status);
        $this->assertNotNull($applicant->document_verified_at);
    }

    /** @test */
    public function verify_document_fails_if_status_is_not_payment_verified()
    {
        $applicant = $this->createSubmittedApplicant();

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-document', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function admin_can_accept_applicant_from_document_verified()
    {
        $applicant = $this->createSubmittedApplicant();
        $applicant->update([
            'status' => 'document_verified',
            'document_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.accept', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $applicant->refresh();
        $this->assertEquals('accepted', $applicant->status);
        $this->assertNotNull($applicant->accepted_at);
    }

    /** @test */
    public function admin_can_accept_applicant_from_scored()
    {
        $applicant = $this->createSubmittedApplicant();
        $applicant->update(['status' => 'scored']);

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.accept', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $applicant->refresh();
        $this->assertEquals('accepted', $applicant->status);
    }

    /** @test */
    public function accept_fails_if_status_is_not_valid()
    {
        $applicant = $this->createSubmittedApplicant();
        // status is 'submitted' - not valid for acceptance

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.accept', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function admin_can_reject_applicant()
    {
        $applicant = $this->createSubmittedApplicant();
        $applicant->update(['status' => 'scored']);

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.reject', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $applicant->refresh();
        $this->assertEquals('rejected', $applicant->status);
        $this->assertNotNull($applicant->rejected_at);
    }

    /** @test */
    public function reject_fails_if_status_is_not_scored()
    {
        $applicant = $this->createSubmittedApplicant();

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.reject', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ========================================
    // PSB FULL WORKFLOW TEST (end-to-end)
    // ========================================

    /** @test */
    public function full_psb_workflow_without_test()
    {
        // Step 1: Public registration
        $data = $this->getBaseSMPRegistrationData();
        $response = $this->post(route('public.registration.store'), $data);
        $response->assertRedirect();

        $applicant = Applicant::where('nisn', '1234567890')->first();
        $this->assertNotNull($applicant);
        $this->assertEquals('submitted', $applicant->status);

        // Step 2: Admin verifies payment
        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-payment', $applicant));
        $response->assertSessionHas('success');
        $applicant->refresh();
        $this->assertEquals('payment_verified', $applicant->status);

        // Step 3: Admin verifies documents
        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-document', $applicant));
        $response->assertSessionHas('success');
        $applicant->refresh();
        $this->assertEquals('document_verified', $applicant->status);

        // Step 4: Admin accepts (no test required for SMP)
        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.accept', $applicant));
        $response->assertSessionHas('success');
        $applicant->refresh();
        $this->assertEquals('accepted', $applicant->status);
    }

    // ========================================
    // EXPORT TESTS
    // ========================================

    /** @test */
    public function admin_can_export_applicants_csv()
    {
        $this->createSubmittedApplicant();

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.export', ['format' => 'csv']));

        $response->assertStatus(200);
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'text/csv') || str_contains($contentType, 'text/plain')
        );
    }

    // ========================================
    // MODEL TESTS
    // ========================================

    /** @test */
    public function applicant_model_calculates_final_score()
    {
        $applicant = new Applicant([
            'average_raport_score' => 80,
            'test_total_score' => 90,
            'interview_score' => 75,
            'achievement_score' => 85,
        ]);

        // Note: model uses raport_score, test_score etc. Let's test the method
        $applicant->raport_score = 80;
        $applicant->test_score = 90;
        $applicant->interview_score = 75;
        $applicant->achievement_score = 85;

        $finalScore = $applicant->calculateFinalScore();

        // 80*0.4 + 90*0.3 + 75*0.2 + 85*0.1 = 32 + 27 + 15 + 8.5 = 82.5
        $this->assertEquals(82.5, $finalScore);
    }

    /** @test */
    public function applicant_status_labels_are_correct()
    {
        $applicant = new Applicant();

        $applicant->status = 'submitted';
        $this->assertEquals('Menunggu Verifikasi', $applicant->getStatusLabel());

        $applicant->status = 'accepted';
        $this->assertEquals('Diterima', $applicant->getStatusLabel());

        $applicant->status = 'rejected';
        $this->assertEquals('Ditolak', $applicant->getStatusLabel());
    }

    /** @test */
    public function applicant_status_badge_colors_are_correct()
    {
        $applicant = new Applicant();

        $applicant->status = 'submitted';
        $this->assertEquals('blue', $applicant->getStatusBadgeColor());

        $applicant->status = 'accepted';
        $this->assertEquals('green', $applicant->getStatusBadgeColor());

        $applicant->status = 'rejected';
        $this->assertEquals('red', $applicant->getStatusBadgeColor());
    }

    /** @test */
    public function registration_wave_isOpen_method_works()
    {
        $wave = RegistrationWave::create([
            'school_id' => $this->smpSchool->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Gelombang 1',
            'wave_number' => 1,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
            'quota' => 50,
            'is_active' => true,
        ]);

        $this->assertTrue($wave->isOpen());

        // Deactivate
        $wave->update(['is_active' => false]);
        $this->assertFalse($wave->isOpen());
    }

    /** @test */
    public function registration_wave_isFull_method_works()
    {
        $wave = RegistrationWave::create([
            'school_id' => $this->smpSchool->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Gelombang 1',
            'wave_number' => 1,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
            'quota' => 2,
            'registered_count' => 2,
            'is_active' => true,
        ]);

        $this->assertTrue($wave->isFull());

        // Increase quota
        $wave->update(['quota' => 10]);
        $this->assertFalse($wave->isFull());
    }

    /** @test */
    public function registration_wave_unlimited_quota()
    {
        $wave = RegistrationWave::create([
            'school_id' => $this->smpSchool->id,
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Gelombang 1',
            'wave_number' => 1,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
            'quota' => null, // Unlimited
            'is_active' => true,
        ]);

        $this->assertFalse($wave->isFull());
        $this->assertNull($wave->getRemainingQuota());
    }

    // ========================================
    // APPLICANT RELATIONSHIP TESTS
    // ========================================

    /** @test */
    public function applicant_belongs_to_school()
    {
        $applicant = $this->createSubmittedApplicant();

        $this->assertInstanceOf(School::class, $applicant->school);
        $this->assertEquals($this->smpSchool->id, $applicant->school->id);
    }

    /** @test */
    public function applicant_belongs_to_academic_year()
    {
        $applicant = $this->createSubmittedApplicant();

        $this->assertInstanceOf(AcademicYear::class, $applicant->academicYear);
        $this->assertEquals($this->academicYear->id, $applicant->academicYear->id);
    }

    /** @test */
    public function applicant_has_many_documents()
    {
        $applicant = $this->createSubmittedApplicant();

        ApplicantDocument::create([
            'applicant_id' => $applicant->id,
            'document_type' => 'kk',
            'file_name' => 'kk.pdf',
            'file_path' => 'documents/kk.pdf',
            'file_size' => 1024,
            'verified' => false,
        ]);

        $this->assertCount(1, $applicant->documents);
    }

    /** @test */
    public function applicant_has_many_achievements()
    {
        $applicant = $this->createSubmittedApplicant();

        ApplicantAchievement::create([
            'applicant_id' => $applicant->id,
            'achievement_name' => 'Juara 1 Kelas 6',
            'achievement_type' => 'academic',
            'achievement_level' => 'school',
            'rank' => '1',
            'year' => 2025,
            'points' => 20.0,
        ]);

        $this->assertCount(1, $applicant->achievements);
    }

    // ========================================
    // INPUT SCORE TESTS
    // ========================================

    /** @test */
    public function admin_can_view_input_score_form()
    {
        $applicant = $this->createSubmittedApplicant();
        $applicant->update([
            'status' => 'document_verified',
            'document_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.input-score', $applicant));

        $response->assertStatus(200);
        $response->assertViewIs('admin.psb.input-score');
    }

    /** @test */
    public function input_score_fails_if_status_not_document_verified()
    {
        $applicant = $this->createSubmittedApplicant();

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.input-score', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ========================================
    // EDGE CASE / SECURITY TESTS
    // ========================================

    /** @test */
    public function registration_success_page_shows_applicant_data()
    {
        $applicant = $this->createSubmittedApplicant();

        $response = $this->get(route('public.registration.success', $applicant->registration_number));

        $response->assertStatus(200);
        $response->assertViewIs('public.registration-success');
        $response->assertSee($applicant->registration_number);
    }

    /** @test */
    public function registration_success_page_404_for_invalid_number()
    {
        $response = $this->get(route('public.registration.success', 'INVALID-999'));

        $response->assertStatus(404);
    }

    /** @test */
    public function prestasi_registration_creates_achievement_with_correct_points()
    {
        $data = $this->getBaseSMPRegistrationData();
        $data['admission_path'] = 'prestasi';
        $data['achievement_rank'] = '2';
        $data['achievement_grade'] = '8';
        $data['achievement_year'] = '2025/2026';
        $data['achievement_school'] = 'SMP Negeri 1';
        $data['achievement_certificate'] = UploadedFile::fake()->create('certificate.pdf', 1024, 'application/pdf');

        $response = $this->post(route('public.registration.store'), $data);

        $applicant = Applicant::where('nisn', '1234567890')->first();
        $achievement = ApplicantAchievement::where('applicant_id', $applicant->id)->first();

        $this->assertNotNull($achievement);
        $this->assertEquals('Juara 2 Kelas 8', $achievement->achievement_name);
        $this->assertEquals(15.0, (float)$achievement->points); // Juara 2 = 15 points
    }

    /** @test */
    public function prestasi_rank_3_gives_10_points()
    {
        $data = $this->getBaseSMPRegistrationData();
        $data['admission_path'] = 'prestasi';
        $data['achievement_rank'] = '3';
        $data['achievement_grade'] = '9';
        $data['achievement_year'] = '2025/2026';
        $data['achievement_school'] = 'SMP Negeri 1';
        $data['achievement_certificate'] = UploadedFile::fake()->create('certificate.pdf', 1024, 'application/pdf');

        $this->post(route('public.registration.store'), $data);

        $applicant = Applicant::where('nisn', '1234567890')->first();
        $achievement = ApplicantAchievement::where('applicant_id', $applicant->id)->first();

        $this->assertEquals(10.0, (float)$achievement->points); // Juara 3 = 10 points
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    private function getBaseSMPRegistrationData(): array
    {
        return [
            'school_id' => $this->smpSchool->id,
            'nisn' => '1234567890',
            'full_name' => 'Budi Santoso',
            'gender' => 'L',
            'birth_place' => 'Gunungsitoli',
            'birth_date' => '2010-05-15',
            'religion' => 'Kristen',
            'address' => 'Jl. Pelita No. 10, Gunungsitoli',
            'phone' => '081234567890',
            'email' => 'budi@test.com',
            'father_name' => 'Ama Budi',
            'father_phone' => '081234567891',
            'father_occupation' => 'Wiraswasta',
            'mother_name' => 'Ina Budi',
            'mother_phone' => '081234567892',
            'mother_occupation' => 'Ibu Rumah Tangga',
            'previous_school' => 'SD Negeri 1 Gunungsitoli',
            'admission_path' => 'reguler',
        ];
    }

    private function createSubmittedApplicant(): Applicant
    {
        return Applicant::create([
            'school_id' => $this->smpSchool->id,
            'academic_year_id' => $this->academicYear->id,
            'registration_number' => 'SMP-26-0001',
            'nisn' => '1234567890',
            'full_name' => 'Budi Santoso',
            'gender' => 'L',
            'birth_place' => 'Gunungsitoli',
            'birth_date' => '2010-05-15',
            'religion' => 'Kristen',
            'address' => 'Jl. Pelita No. 10',
            'phone' => '081234567890',
            'email' => 'budi@test.com',
            'father_name' => 'Ama Budi',
            'father_phone' => '081234567891',
            'mother_name' => 'Ina Budi',
            'mother_phone' => '081234567892',
            'previous_school' => 'SD Negeri 1',
            'admission_path' => 'reguler',
            'status' => 'submitted',
            'submission_date' => now(),
        ]);
    }

    private function createPrestasiApplicant(): Applicant
    {
        $applicant = Applicant::create([
            'school_id' => $this->smpSchool->id,
            'academic_year_id' => $this->academicYear->id,
            'registration_number' => 'SMP-26-0002',
            'nisn' => '9876543210',
            'full_name' => 'Sari Prestasi',
            'gender' => 'P',
            'birth_place' => 'Gunungsitoli',
            'birth_date' => '2010-08-20',
            'religion' => 'Kristen',
            'address' => 'Jl. Merdeka No. 5',
            'phone' => '081299887766',
            'email' => 'sari@test.com',
            'father_name' => 'Ama Sari',
            'father_phone' => '081299887701',
            'mother_name' => 'Ina Sari',
            'mother_phone' => '081299887702',
            'previous_school' => 'SD Negeri 2 Gunungsitoli',
            'admission_path' => 'prestasi',
            'status' => 'submitted',
            'submission_date' => now(),
        ]);

        // Add achievement data
        ApplicantAchievement::create([
            'applicant_id' => $applicant->id,
            'achievement_name' => 'Juara 1 Kelas 9',
            'achievement_type' => 'academic',
            'achievement_level' => 'school',
            'rank' => '1',
            'organizer' => 'SD Negeri 2 Gunungsitoli',
            'year' => 2025,
            'points' => 20.0,
        ]);

        return $applicant;
    }

    // ========================================
    // PRESTASI FLOW TESTS
    // ========================================

    /** @test */
    public function admin_sees_verify_prestasi_button_for_prestasi_applicant()
    {
        $applicant = $this->createPrestasiApplicant();

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.show', $applicant));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Data Prestasi');
        $response->assertSee('Tolak Prestasi');
        $response->assertDontSee('Verifikasi Pembayaran');
    }

    /** @test */
    public function admin_sees_verify_payment_button_for_reguler_applicant()
    {
        $applicant = $this->createSubmittedApplicant();

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.show', $applicant));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Pembayaran');
        $response->assertDontSee('Verifikasi Data Prestasi');
    }

    /** @test */
    public function admin_can_verify_prestasi()
    {
        $applicant = $this->createPrestasiApplicant();

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-prestasi', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $applicant->refresh();
        $this->assertEquals('prestasi_verified', $applicant->status);
        $this->assertNotNull($applicant->prestasi_verified_at);
    }

    /** @test */
    public function verify_prestasi_fails_for_non_submitted_status()
    {
        $applicant = $this->createPrestasiApplicant();
        $applicant->update(['status' => 'payment_verified']);

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-prestasi', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function verify_prestasi_fails_for_reguler_applicant()
    {
        $applicant = $this->createSubmittedApplicant();

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-prestasi', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function admin_can_reject_prestasi_with_reason()
    {
        $applicant = $this->createPrestasiApplicant();

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.reject-prestasi', $applicant), [
                'rejection_reason' => 'Data raport tidak sesuai dengan sertifikat yang diupload.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $applicant->refresh();
        // After rejection, path is switched to reguler
        $this->assertEquals('reguler', $applicant->admission_path);
        $this->assertEquals('Data raport tidak sesuai dengan sertifikat yang diupload.', $applicant->prestasi_rejection_reason);
        // Status stays submitted so they can continue via reguler flow
        $this->assertEquals('submitted', $applicant->status);
    }

    /** @test */
    public function reject_prestasi_requires_reason()
    {
        $applicant = $this->createPrestasiApplicant();

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.reject-prestasi', $applicant), [
                'rejection_reason' => '',
            ]);

        $response->assertSessionHasErrors('rejection_reason');
    }

    /** @test */
    public function reject_prestasi_fails_for_reguler_applicant()
    {
        $applicant = $this->createSubmittedApplicant();

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.reject-prestasi', $applicant), [
                'rejection_reason' => 'Some reason',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function verify_payment_blocked_for_prestasi_applicant()
    {
        $applicant = $this->createPrestasiApplicant();

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-payment', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function verify_document_accepts_prestasi_verified_status()
    {
        $applicant = $this->createPrestasiApplicant();
        $applicant->update([
            'status' => 'prestasi_verified',
            'prestasi_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-document', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $applicant->refresh();
        $this->assertEquals('document_verified', $applicant->status);
        $this->assertNotNull($applicant->document_verified_at);
    }

    /** @test */
    public function full_prestasi_flow_without_test()
    {
        // Step 1: Prestasi student registers
        $applicant = $this->createPrestasiApplicant();
        $this->assertEquals('submitted', $applicant->status);
        $this->assertEquals('prestasi', $applicant->admission_path);

        // Step 2: Admin verifies prestasi (NOT payment)
        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-prestasi', $applicant));
        $response->assertSessionHas('success');
        $applicant->refresh();
        $this->assertEquals('prestasi_verified', $applicant->status);

        // Step 3: Admin verifies documents (prestasi_verified → document_verified)
        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-document', $applicant));
        $response->assertSessionHas('success');
        $applicant->refresh();
        $this->assertEquals('document_verified', $applicant->status);

        // Step 4: Admin accepts directly (SMP doesn't require test)
        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.accept', $applicant));
        $response->assertRedirect();
        $applicant->refresh();
        $this->assertEquals('accepted', $applicant->status);
        $this->assertNotNull($applicant->accepted_at);
    }

    /** @test */
    public function full_prestasi_rejected_then_reguler_flow()
    {
        // Step 1: Prestasi student registers
        $applicant = $this->createPrestasiApplicant();

        // Step 2: Admin rejects prestasi → switched to reguler
        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.reject-prestasi', $applicant), [
                'rejection_reason' => 'Data tidak valid',
            ]);
        $applicant->refresh();
        $this->assertEquals('reguler', $applicant->admission_path);
        $this->assertEquals('submitted', $applicant->status);

        // Step 3: Now follows reguler flow — verify payment
        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-payment', $applicant));
        $response->assertSessionHas('success');
        $applicant->refresh();
        $this->assertEquals('payment_verified', $applicant->status);

        // Step 4: Verify documents
        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.verify-document', $applicant));
        $response->assertSessionHas('success');
        $applicant->refresh();
        $this->assertEquals('document_verified', $applicant->status);

        // Step 5: Accept
        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.psb.applicants.accept', $applicant));
        $applicant->refresh();
        $this->assertEquals('accepted', $applicant->status);
    }

    /** @test */
    public function prestasi_show_page_displays_achievement_data()
    {
        $applicant = $this->createPrestasiApplicant();

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.show', $applicant));

        $response->assertStatus(200);
        $response->assertSee('Data Prestasi');
        $response->assertSee('Juara 1 Kelas 9');
        $response->assertSee('SD Negeri 2 Gunungsitoli');
        $response->assertSee('Menunggu Verifikasi');
    }

    /** @test */
    public function prestasi_show_page_displays_verified_badge_after_verification()
    {
        $applicant = $this->createPrestasiApplicant();
        $applicant->update([
            'status' => 'prestasi_verified',
            'prestasi_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.show', $applicant));

        $response->assertStatus(200);
        $response->assertSee('Diverifikasi');
    }

    /** @test */
    public function flow_tracker_shows_prestasi_step_for_prestasi_applicant()
    {
        $applicant = $this->createPrestasiApplicant();

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.show', $applicant));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Prestasi');
        $response->assertSee('Jalur Prestasi');
    }

    /** @test */
    public function flow_tracker_shows_payment_step_for_reguler_applicant()
    {
        $applicant = $this->createSubmittedApplicant();

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.show', $applicant));

        $response->assertStatus(200);
        $response->assertSee('Pembayaran');
        $response->assertSee('Jalur Reguler');
    }

    /** @test */
    public function applicant_model_has_correct_prestasi_status_label()
    {
        $applicant = $this->createPrestasiApplicant();
        $applicant->status = 'prestasi_verified';

        $this->assertEquals('Prestasi Terverifikasi', $applicant->getStatusLabel());
        $this->assertEquals('amber', $applicant->getStatusBadgeColor());
    }

    /** @test */
    public function applicant_model_is_prestasi_path_helper()
    {
        $prestasiApplicant = $this->createPrestasiApplicant();
        $regulerApplicant = $this->createSubmittedApplicant();

        $this->assertTrue($prestasiApplicant->isPrestasiPath());
        $this->assertFalse($regulerApplicant->isPrestasiPath());
    }

    /** @test */
    public function prestasi_timeline_shows_prestasi_verified_event()
    {
        $applicant = $this->createPrestasiApplicant();
        $applicant->update([
            'status' => 'prestasi_verified',
            'prestasi_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.psb.applicants.show', $applicant));

        $response->assertStatus(200);
        $response->assertSee('Prestasi Diverifikasi');
    }
}
