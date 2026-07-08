<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\School;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentBill;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreasurerModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $bendahara;
    protected User $adminUser;
    protected School $school;
    protected Student $student;
    protected AcademicYear $academicYear;
    protected Semester $semester;
    protected PaymentType $paymentType;
    protected Classroom $classroom;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::create([
            'name' => 'SMA Pembda Test',
            'type' => 'SMA',
            'npsn' => '12345678',
            'address' => 'Jl. Test',
        ]);

        $this->bendahara = User::factory()->bendahara()->create([
            'school_id' => $this->school->id,
        ]);

        $this->adminUser = User::factory()->adminSekolah()->create([
            'school_id' => $this->school->id,
        ]);

        $this->academicYear = AcademicYear::factory()->create(['is_active' => true]);
        $this->semester = Semester::factory()->create([
            'academic_year_id' => $this->academicYear->id,
        ]);

        $this->classroom = Classroom::factory()->create([
            'school_id' => $this->school->id,
        ]);

        $this->student = Student::factory()->create([
            'school_id' => $this->school->id,
            'status' => 'aktif',
        ]);

        StudentClass::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->academicYear->id,
            'status' => 'aktif',
        ]);

        $this->paymentType = PaymentType::create([
            'school_id' => $this->school->id,
            'type_code' => 'SPP',
            'type_name' => 'SPP Bulanan',
            'amount' => 500000,
            'is_recurring' => true,
            'allow_installment' => true,
            'is_active' => true,
        ]);
    }

    // ─── AUTHENTICATION / AUTHORIZATION ────────────────────

    public function test_bendahara_can_access_dashboard()
    {
        $response = $this->actingAs($this->bendahara)
            ->get(route('treasurer.dashboard'));

        $response->assertStatus(200);
    }

    public function test_non_bendahara_cannot_access_treasurer_routes()
    {
        $guru = User::factory()->guru()->create(['school_id' => $this->school->id]);

        $response = $this->actingAs($guru)
            ->get(route('treasurer.dashboard'));

        $response->assertStatus(403);
    }

    public function test_guest_redirected_from_treasurer_routes()
    {
        $response = $this->get(route('treasurer.dashboard'));

        $response->assertRedirect(route('login'));
    }

    // ─── PAYMENT INDEX ─────────────────────────────────────

    public function test_bendahara_can_view_payments_index()
    {
        $bill = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'month' => now()->month,
            'year' => now()->year,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
        ]);

        Payment::create([
            'bill_id' => $bill->id,
            'student_id' => $this->student->id,
            'amount_paid' => 500000,
            'payment_method' => 'cash',
            'payment_date' => now(),
            'receipt_number' => 'KWT-TEST-0001',
            'is_verified' => true,
            'verified_by' => $this->adminUser->id,
            'verified_at' => now(),
            'processed_by' => $this->bendahara->id,
        ]);

        $response = $this->actingAs($this->bendahara)
            ->get(route('treasurer.payments.index'));

        $response->assertStatus(200);
        $response->assertSee('KWT-TEST-0001');
    }

    // ─── PAYMENT STORE ─────────────────────────────────────

    public function test_bendahara_can_store_payment()
    {
        $bill = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'month' => now()->month,
            'year' => now()->year,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
        ]);

        $response = $this->actingAs($this->bendahara)->post(route('treasurer.payments.store'), [
            'bill_id' => $bill->id,
            'student_id' => $this->student->id,
            'amount_paid' => 500000,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('treasurer.payments.index'));

        $this->assertDatabaseHas('payments', [
            'student_id' => $this->student->id,
            'amount_paid' => 500000,
            'payment_method' => 'cash',
        ]);

        // Bill should be marked as lunas
        $bill->refresh();
        $this->assertEquals('lunas', $bill->status);
    }

    public function test_payment_store_validation()
    {
        $response = $this->actingAs($this->bendahara)->post(route('treasurer.payments.store'), [
            // Missing required fields
        ]);

        $response->assertSessionHasErrors(['student_id', 'amount_paid', 'payment_method', 'payment_date']);
    }

    public function test_bendahara_cannot_pay_for_other_school_student()
    {
        $otherSchool = School::create([
            'name' => 'Sekolah Lain',
            'type' => 'SMP',
            'npsn' => '99999999',
            'address' => 'Jl. Lain',
        ]);

        $otherStudent = Student::factory()->create([
            'school_id' => $otherSchool->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->bendahara)->post(route('treasurer.payments.store'), [
            'student_id' => $otherStudent->id,
            'amount_paid' => 500000,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    // ─── PARTIAL PAYMENT (CICILAN) ─────────────────────────

    public function test_partial_payment_sets_status_to_cicilan()
    {
        $bill = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'month' => now()->month,
            'year' => now()->year,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
        ]);

        $this->actingAs($this->bendahara)->post(route('treasurer.payments.store'), [
            'bill_id' => $bill->id,
            'student_id' => $this->student->id,
            'amount_paid' => 200000,
            'payment_method' => 'transfer',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $bill->refresh();
        $this->assertEquals('cicilan', $bill->status);
        $this->assertEquals(200000, $bill->paid_amount);
    }

    // ─── BILLS INDEX ───────────────────────────────────────

    public function test_bendahara_can_view_bills_index()
    {
        StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'month' => now()->month,
            'year' => now()->year,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
        ]);

        $response = $this->actingAs($this->bendahara)
            ->get(route('treasurer.bills.index'));

        $response->assertStatus(200);
    }

    // ─── REPORTS ───────────────────────────────────────────

    public function test_bendahara_can_view_reports()
    {
        $response = $this->actingAs($this->bendahara)
            ->get(route('treasurer.reports.index'));

        $response->assertStatus(200);
    }

    // ─── PAYMENT SHOW ──────────────────────────────────────

    public function test_bendahara_can_view_payment_detail()
    {
        $bill = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'month' => now()->month,
            'year' => now()->year,
            'amount' => 500000,
            'paid_amount' => 500000,
            'status' => 'lunas',
        ]);

        $payment = Payment::create([
            'bill_id' => $bill->id,
            'student_id' => $this->student->id,
            'amount_paid' => 500000,
            'payment_method' => 'cash',
            'payment_date' => now(),
            'receipt_number' => 'KWT-TEST-0002',
            'is_verified' => true,
            'verified_by' => $this->adminUser->id,
            'verified_at' => now(),
            'processed_by' => $this->bendahara->id,
        ]);

        $response = $this->actingAs($this->bendahara)
            ->get(route('treasurer.payments.show', $payment));

        $response->assertStatus(200);
        $response->assertSee('KWT-TEST-0002');
    }

    public function test_bendahara_cannot_view_other_school_payment()
    {
        $otherSchool = School::create([
            'name' => 'Sekolah Lain',
            'type' => 'SMK',
            'npsn' => '88888888',
            'address' => 'Jl. Lain',
        ]);

        $otherStudent = Student::factory()->create([
            'school_id' => $otherSchool->id,
        ]);

        $otherPaymentType = PaymentType::create([
            'school_id' => $otherSchool->id,
            'type_code' => 'SPP-OTHER',
            'type_name' => 'SPP Lain',
            'amount' => 500000,
            'is_recurring' => true,
        ]);

        $otherBill = StudentBill::create([
            'student_id' => $otherStudent->id,
            'payment_type_id' => $otherPaymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'month' => 1,
            'year' => now()->year,
            'amount' => 500000,
            'paid_amount' => 500000,
            'status' => 'lunas',
        ]);

        $payment = Payment::create([
            'student_id' => $otherStudent->id,
            'bill_id' => $otherBill->id,
            'amount_paid' => 500000,
            'payment_method' => 'cash',
            'payment_date' => now(),
            'receipt_number' => 'KWT-OTHER-0001',
            'is_verified' => true,
            'processed_by' => $this->bendahara->id,
        ]);

        $response = $this->actingAs($this->bendahara)
            ->get(route('treasurer.payments.show', $payment));

        $response->assertStatus(403);
    }

    // ─── BULK PAYMENT ──────────────────────────────────────

    public function test_bendahara_can_bulk_store_payments()
    {
        $bills = [];
        for ($i = 1; $i <= 3; $i++) {
            $bills[] = StudentBill::create([
                'student_id' => $this->student->id,
                'payment_type_id' => $this->paymentType->id,
                'academic_year_id' => $this->academicYear->id,
                'semester_id' => $this->semester->id,
                'month' => $i,
                'year' => now()->year,
                'amount' => 500000,
                'paid_amount' => 0,
                'status' => 'belum_bayar',
            ]);
        }

        $response = $this->actingAs($this->bendahara)->post(route('treasurer.payments.bulk-store'), [
            'bill_ids' => array_map(fn($b) => $b->id, $bills),
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('treasurer.payments.index'));

        // All bills should now be lunas
        foreach ($bills as $bill) {
            $bill->refresh();
            $this->assertEquals('lunas', $bill->status);
        }

        // Should have 3 payments
        $this->assertEquals(3, Payment::where('student_id', $this->student->id)->count());
    }

    public function test_bendahara_can_perform_batch_payment()
    {
        $bill1 = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'month' => 11,
            'year' => now()->year,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
        ]);

        $bill2 = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'month' => 12,
            'year' => now()->year,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
        ]);

        $response = $this->actingAs($this->bendahara)->post(route('treasurer.payments.batch-store'), [
            'bill_ids' => implode(',', [$bill1->id, $bill2->id]),
            'student_id' => $this->student->id,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('treasurer.bills.index'));

        // Assert 2 separate payments created
        $this->assertEquals(2, Payment::where('student_id', $this->student->id)->count());
        $this->assertDatabaseHas('payments', [
            'bill_id' => $bill1->id,
            'student_id' => $this->student->id,
            'amount_paid' => 500000,
        ]);
        $this->assertDatabaseHas('payments', [
            'bill_id' => $bill2->id,
            'student_id' => $this->student->id,
            'amount_paid' => 500000,
        ]);

        $bill1->refresh();
        $bill2->refresh();
        $this->assertEquals('lunas', $bill1->status);
        $this->assertEquals('lunas', $bill2->status);
    }
}
