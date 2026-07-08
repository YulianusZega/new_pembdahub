<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $paymentMethod;
    protected $isVerified;
    protected $schoolId;
    protected $classroomId;
    protected $paymentTypeId;
    protected $academicYearId;
    protected $studentBills;

    public function __construct(
        $startDate = null,
        $endDate = null,
        $paymentMethod = null,
        $isVerified = null,
        $schoolId = null,
        $classroomId = null,
        $paymentTypeId = null,
        $academicYearId = null
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->paymentMethod = $paymentMethod;
        $this->isVerified = $isVerified;
        $this->schoolId = $schoolId;
        $this->classroomId = $classroomId;
        $this->paymentTypeId = $paymentTypeId;
        $this->academicYearId = $academicYearId;
    }

    public function collection()
    {
        $query = Payment::with(['student.school', 'student.currentClassroom', 'student.classrooms', 'student.classroom', 'bill.paymentType', 'processedBy']);

        if ($this->academicYearId) {
            $query->whereHas('bill', function ($q) {
                $q->where('academic_year_id', $this->academicYearId);
            });
        }

        if ($this->schoolId) {
            $query->whereHas('student', function ($q) {
                $q->where('school_id', $this->schoolId);
            });
        }

        if ($this->classroomId) {
            $query->whereHas('student.studentClasses', function ($q) {
                $q->where('classroom_id', $this->classroomId);
            });
        }

        if ($this->paymentTypeId) {
            $query->whereHas('bill', function ($q) {
                $q->where('payment_type_id', $this->paymentTypeId);
            });
        }

        if ($this->startDate) {
            $query->whereDate('payment_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('payment_date', '<=', $this->endDate);
        }

        if ($this->paymentMethod) {
            $query->where('payment_method', $this->paymentMethod);
        }

        if ($this->isVerified !== null) {
            $query->where('is_verified', $this->isVerified);
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        // Pre-fetch student bills to show Jan-Dec paid status checkmarks without N+1 queries
        $studentIds = $payments->pluck('student_id')->unique();
        $billPaymentTypeIds = $payments->pluck('bill.payment_type_id')->filter()->unique();

        if ($studentIds->isNotEmpty() && $billPaymentTypeIds->isNotEmpty()) {
            $this->studentBills = \App\Models\StudentBill::whereIn('student_id', $studentIds)
                ->whereIn('payment_type_id', $billPaymentTypeIds)
                ->get()
                ->groupBy(function ($bill) {
                    return $bill->student_id . '_' . $bill->payment_type_id . '_' . $bill->year;
                });
        } else {
            $this->studentBills = collect();
        }

        return $payments;
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Kwitansi',
            'Tanggal Pembayaran',
            'Sekolah',
            'Kelas',
            'NISN',
            'Nama Siswa',
            'Jenis Pembayaran',
            'Periode Tagihan',
            'Jumlah Bayar (Transaksi Ini)',
            'Total Tagihan',
            'Total Sudah Dibayar',
            'Sisa Tunggakan (Belum Dibayar)',
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Ags',
            'Sep',
            'Okt',
            'Nov',
            'Des',
            'Metode Pembayaran',
            'No. Referensi',
            'Status Verifikasi',
            'Diproses Oleh',
            'Catatan',
        ];
    }

    public function map($payment): array
    {
        static $counter = 0;
        $counter++;

        $methods = [
            'cash' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            'card' => 'Kartu Kredit',
            'check' => 'Cek',
        ];

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $period = '-';
        $totalBill = 0;
        $totalPaid = 0;
        $remaining = 0;

        if ($payment->bill) {
            $totalBill = $payment->bill->amount;
            $totalPaid = $payment->bill->paid_amount;
            $remaining = max(0, $totalBill - $totalPaid);

            if ($payment->bill->month) {
                $monthName = $monthNames[$payment->bill->month] ?? '';
                $period = $monthName . ' ' . $payment->bill->year;
            } else {
                $period = '1 Kali Bayar (' . $payment->bill->year . ')';
            }
        } else {
            $totalPaid = $payment->amount_paid;
        }

        // Get classroom safely
        $classroom = $payment->student->currentClassroom->first() 
            ?? $payment->student->classrooms->first() 
            ?? $payment->student->classroom;
        
        $className = $classroom ? $classroom->class_name : '-';
        $schoolName = $payment->student->school ? $payment->student->school->name : '-';

        // Resolve 12 months checkmarks for this student, payment type and year
        $monthStatuses = array_fill(1, 12, '-');
        if ($payment->bill && $payment->bill->payment_type_id) {
            $key = $payment->student_id . '_' . $payment->bill->payment_type_id . '_' . $payment->bill->year;
            $billsForYear = $this->studentBills && $this->studentBills->has($key)
                ? $this->studentBills->get($key)
                : collect();

            for ($m = 1; $m <= 12; $m++) {
                $billForMonth = $billsForYear->firstWhere('month', $m);
                if ($billForMonth) {
                    $monthStatuses[$m] = $billForMonth->status === 'lunas' ? '✓' : '✗';
                }
            }
        }

        return [
            $counter,
            $payment->receipt_number ?? '-',
            $payment->payment_date->format('d/m/Y H:i'),
            $schoolName,
            $className,
            $payment->student->nisn,
            $payment->student->full_name,
            $payment->bill ? $payment->bill->paymentType->type_name : 'Pembayaran Umum',
            $period,
            $payment->amount_paid,
            $totalBill,
            $totalPaid,
            $remaining,
            $monthStatuses[1],
            $monthStatuses[2],
            $monthStatuses[3],
            $monthStatuses[4],
            $monthStatuses[5],
            $monthStatuses[6],
            $monthStatuses[7],
            $monthStatuses[8],
            $monthStatuses[9],
            $monthStatuses[10],
            $monthStatuses[11],
            $monthStatuses[12],
            $methods[$payment->payment_method] ?? $payment->payment_method,
            $payment->reference_number ?? '-',
            $payment->is_verified ? 'Terverifikasi' : 'Belum Verifikasi',
            $payment->processedBy->name ?? '-',
            $payment->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
