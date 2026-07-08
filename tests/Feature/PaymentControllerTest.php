<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Student;
use App\Models\Payment;
use App\Models\StudentBill;
use App\Models\PaymentType;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private School $school;
    private Student $student;
    private PaymentType $paymentType;
    private AcademicYear $academicYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::factory()->create();
        $this->admin = User::factory()->create([
            'role' => 'superadmin',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);
        $this->student = Student::factory()->create([
            'school_id' => $this->school->id,
        ]);
        $this->academicYear = AcademicYear::factory()->create();
        $this->paymentType = PaymentType::create([
            'school_id' => $this->school->id,
            'type_code' => 'SPP',
            'type_name' => 'SPP Bulanan',
            'amount' => 500000,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_view_payments_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_payment()
    {
        $bill = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), [
                'bill_id' => $bill->id,
                'student_id' => $this->student->id,
                'amount_paid' => 500000,
                'payment_method' => 'cash',
                'payment_date' => '2026-01-15',
            ]);

        $response->assertRedirect(route('admin.payments.index'));
        $this->assertDatabaseHas('payments', [
            'student_id' => $this->student->id,
            'amount_paid' => 500000,
        ]);

        // Bill should be marked as lunas
        $bill->refresh();
        $this->assertEquals('lunas', $bill->status);
        $this->assertEquals(500000, $bill->paid_amount);
    }

    /** @test */
    public function payment_store_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), []);

        $response->assertSessionHasErrors(['student_id', 'amount_paid', 'payment_method', 'payment_date']);
    }

    /** @test */
    public function payment_store_validates_payment_method()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), [
                'student_id' => $this->student->id,
                'amount_paid' => 100000,
                'payment_method' => 'bitcoin',
                'payment_date' => '2026-01-15',
            ]);

        $response->assertSessionHasErrors('payment_method');
    }

    /** @test */
    public function partial_payment_marks_bill_as_cicilan()
    {
        $bill = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), [
                'bill_id' => $bill->id,
                'student_id' => $this->student->id,
                'amount_paid' => 200000,
                'payment_method' => 'cash',
                'payment_date' => '2026-01-15',
            ]);

        $bill->refresh();
        $this->assertEquals('cicilan', $bill->status);
        $this->assertEquals(200000, $bill->paid_amount);
    }

    /** @test */
    public function admin_can_delete_payment_and_bill_reverts()
    {
        $bill = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 500000,
            'status' => 'lunas',
            'month' => 1,
            'year' => 2026,
        ]);

        $payment = Payment::create([
            'bill_id' => $bill->id,
            'student_id' => $this->student->id,
            'amount_paid' => 500000,
            'payment_method' => 'cash',
            'payment_date' => '2026-01-15',
            'is_verified' => true,
            'verified_by' => $this->admin->id,
            'verified_at' => now(),
            'processed_by' => $this->admin->id,
            'receipt_number' => 'KWT-TEST-001',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.payments.destroy', $payment));

        $response->assertRedirect(route('admin.payments.index'));
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);

        // Bill should revert to belum_bayar
        $bill->refresh();
        $this->assertEquals('belum_bayar', $bill->status);
        $this->assertEquals(0, $bill->paid_amount);
    }

    /** @test */
    public function admin_can_perform_batch_payment()
    {
        \Carbon\Carbon::setTestNow('2026-06-05 09:50:00');
        
        $bill1 = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 6,
            'year' => 2026,
        ]);

        $bill2 = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 7,
            'year' => 2026,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.batch-store'), [
                'bill_ids' => implode(',', [$bill1->id, $bill2->id]),
                'student_id' => $this->student->id,
                'payment_method' => 'cash',
                'payment_date' => '2026-06-05',
            ]);

        $response->assertRedirect(route('admin.bills.index'));
        
        // Assert there are 2 separate payments created
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

    /** @test */
    public function superadmin_can_export_all_payments()
    {
        \Carbon\Carbon::setTestNow('2026-06-05 09:50:00');
        \Maatwebsite\Excel\Facades\Excel::fake();

        // Create student in another school
        $anotherSchool = School::factory()->create();
        $anotherStudent = Student::factory()->create(['school_id' => $anotherSchool->id]);

        $bill1 = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);

        $anotherPaymentType = PaymentType::create([
            'school_id' => $anotherSchool->id,
            'type_code' => 'SPP',
            'type_name' => 'SPP Bulanan',
            'amount' => 500000,
            'is_active' => true,
        ]);

        $bill2 = StudentBill::create([
            'student_id' => $anotherStudent->id,
            'payment_type_id' => $anotherPaymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);
        
        $payment1 = Payment::create([
            'bill_id' => $bill1->id,
            'student_id' => $this->student->id,
            'amount_paid' => 100000,
            'payment_method' => 'cash',
            'payment_date' => '2026-06-05',
            'is_verified' => true,
        ]);
        
        $payment2 = Payment::create([
            'bill_id' => $bill2->id,
            'student_id' => $anotherStudent->id,
            'amount_paid' => 200000,
            'payment_method' => 'cash',
            'payment_date' => '2026-06-05',
            'is_verified' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.export'));

        $response->assertStatus(200);
        \Maatwebsite\Excel\Facades\Excel::assertDownloaded('Pembayaran_2026-06-05_095000.xlsx', function (\App\Exports\PaymentsExport $export) {
            $collection = $export->collection();
            return $collection->count() === 2;
        });

        \Carbon\Carbon::setTestNow();
    }

    /** @test */
    public function admin_sekolah_only_exports_own_school_payments()
    {
        \Carbon\Carbon::setTestNow('2026-06-05 09:50:00');
        \Maatwebsite\Excel\Facades\Excel::fake();

        $adminSekolah = User::factory()->create([
            'role' => 'admin_sekolah',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        // Create student in another school
        $anotherSchool = School::factory()->create();
        $anotherStudent = Student::factory()->create(['school_id' => $anotherSchool->id]);

        $bill1 = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);

        $anotherPaymentType = PaymentType::create([
            'school_id' => $anotherSchool->id,
            'type_code' => 'SPP',
            'type_name' => 'SPP Bulanan',
            'amount' => 500000,
            'is_active' => true,
        ]);

        $bill2 = StudentBill::create([
            'student_id' => $anotherStudent->id,
            'payment_type_id' => $anotherPaymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);
        
        $payment1 = Payment::create([
            'bill_id' => $bill1->id,
            'student_id' => $this->student->id,
            'amount_paid' => 100000,
            'payment_method' => 'cash',
            'payment_date' => '2026-06-05',
            'is_verified' => true,
        ]);
        
        $payment2 = Payment::create([
            'bill_id' => $bill2->id,
            'student_id' => $anotherStudent->id,
            'amount_paid' => 200000,
            'payment_method' => 'cash',
            'payment_date' => '2026-06-05',
            'is_verified' => true,
        ]);

        $response = $this->actingAs($adminSekolah)
            ->get(route('admin.payments.export'));

        $response->assertStatus(200);
        \Maatwebsite\Excel\Facades\Excel::assertDownloaded('Pembayaran_2026-06-05_095000.xlsx', function (\App\Exports\PaymentsExport $export) use ($payment1) {
            $collection = $export->collection();
            return $collection->count() === 1 && $collection->first()->id === $payment1->id;
        });

        \Carbon\Carbon::setTestNow();
    }

    /** @test */
    public function treasurer_only_exports_own_school_payments()
    {
        \Carbon\Carbon::setTestNow('2026-06-05 09:50:00');
        \Maatwebsite\Excel\Facades\Excel::fake();

        $treasurer = User::factory()->create([
            'role' => 'bendahara',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        // Create student in another school
        $anotherSchool = School::factory()->create();
        $anotherStudent = Student::factory()->create(['school_id' => $anotherSchool->id]);

        $bill1 = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);

        $anotherPaymentType = PaymentType::create([
            'school_id' => $anotherSchool->id,
            'type_code' => 'SPP',
            'type_name' => 'SPP Bulanan',
            'amount' => 500000,
            'is_active' => true,
        ]);

        $bill2 = StudentBill::create([
            'student_id' => $anotherStudent->id,
            'payment_type_id' => $anotherPaymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);
        
        $payment1 = Payment::create([
            'bill_id' => $bill1->id,
            'student_id' => $this->student->id,
            'amount_paid' => 100000,
            'payment_method' => 'cash',
            'payment_date' => '2026-06-05',
            'is_verified' => true,
        ]);
        
        $payment2 = Payment::create([
            'bill_id' => $bill2->id,
            'student_id' => $anotherStudent->id,
            'amount_paid' => 200000,
            'payment_method' => 'cash',
            'payment_date' => '2026-06-05',
            'is_verified' => true,
        ]);

        $response = $this->actingAs($treasurer)
            ->get(route('treasurer.payments.export'));

        $response->assertStatus(200);
        \Maatwebsite\Excel\Facades\Excel::assertDownloaded('Pembayaran_2026-06-05_095000.xlsx', function (\App\Exports\PaymentsExport $export) use ($payment1) {
            $collection = $export->collection();
            return $collection->count() === 1 && $collection->first()->id === $payment1->id;
        });

        \Carbon\Carbon::setTestNow();
    }

    /** @test */
    public function admin_can_filter_payments_by_academic_year()
    {
        $anotherYear = AcademicYear::factory()->create();

        $bill1 = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);

        $bill2 = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $anotherYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);

        $payment1 = Payment::create([
            'bill_id' => $bill1->id,
            'student_id' => $this->student->id,
            'amount_paid' => 100000,
            'payment_method' => 'cash',
            'payment_date' => '2026-06-05',
        ]);

        $payment2 = Payment::create([
            'bill_id' => $bill2->id,
            'student_id' => $this->student->id,
            'amount_paid' => 200000,
            'payment_method' => 'cash',
            'payment_date' => '2026-06-05',
        ]);

        // Request with academic_year_id filter for $this->academicYear->id
        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.index', ['academic_year_id' => $this->academicYear->id]));

        $response->assertStatus(200);
        $payments = $response->viewData('payments');
        $this->assertEquals(1, $payments->count());
        $this->assertEquals($payment1->id, $payments->first()->id);

        // Request with academic_year_id filter for $anotherYear->id
        $response2 = $this->actingAs($this->admin)
            ->get(route('admin.payments.index', ['academic_year_id' => $anotherYear->id]));

        $response2->assertStatus(200);
        $payments2 = $response2->viewData('payments');
        $this->assertEquals(1, $payments2->count());
        $this->assertEquals($payment2->id, $payments2->first()->id);
    }

    /** @test */
    public function admin_can_export_payments_filtered_by_academic_year()
    {
        \Carbon\Carbon::setTestNow('2026-06-05 09:50:00');
        \Maatwebsite\Excel\Facades\Excel::fake();

        $anotherYear = AcademicYear::factory()->create();

        $bill1 = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);

        $bill2 = StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $this->paymentType->id,
            'academic_year_id' => $anotherYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'month' => 1,
            'year' => 2026,
        ]);

        $payment1 = Payment::create([
            'bill_id' => $bill1->id,
            'student_id' => $this->student->id,
            'amount_paid' => 100000,
            'payment_method' => 'cash',
            'payment_date' => '2026-06-05',
        ]);

        $payment2 = Payment::create([
            'bill_id' => $bill2->id,
            'student_id' => $this->student->id,
            'amount_paid' => 200000,
            'payment_method' => 'cash',
            'payment_date' => '2026-06-05',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.export', ['academic_year_id' => $this->academicYear->id]));

        $response->assertStatus(200);
        \Maatwebsite\Excel\Facades\Excel::assertDownloaded('Pembayaran_2026-06-05_095000.xlsx', function (\App\Exports\PaymentsExport $export) use ($payment1) {
            $collection = $export->collection();
            return $collection->count() === 1 && $collection->first()->id === $payment1->id;
        });

        \Carbon\Carbon::setTestNow();
    }
}
