<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\StudentBill;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\AcademicYear;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Filter options
        $paymentTypes = PaymentType::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('type_name')
            ->get();

        $academicYears = AcademicYear::orderBy('year', 'desc')->get();

        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        $classrooms = Classroom::where('school_id', $schoolId)
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->where('academic_year_id', $activeAcademicYear->id);
            })
            ->orderBy('class_name')
            ->get();

        // Default filters
        $paymentTypeId = $request->payment_type_id;
        $academicYearId = $request->academic_year_id ?? AcademicYear::where('is_active', true)->first()?->id;
        $classroomId = $request->classroom_id;
        $periodType = $request->period_type ?? 'ytd'; // yearly, ytd, month
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $showAll = $request->boolean('show_all'); // Toggle untuk tampilkan semua termasuk non-aktif

        // Build query
        $query = StudentBill::with(['student', 'paymentType', 'academicYear'])
            ->whereHas('student', function ($q) use ($schoolId, $showAll) {
                $q->where('school_id', $schoolId);
                if (!$showAll) {
                    $q->where('status', 'aktif'); // Default: hanya siswa aktif
                }
            });

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        if ($paymentTypeId) {
            $query->where('payment_type_id', $paymentTypeId);
        }

        if ($classroomId) {
            $studentIds = DB::table('student_classes')
                ->where('classroom_id', $classroomId)
                ->where('academic_year_id', $academicYearId)
                ->pluck('student_id');
            $query->whereIn('student_id', $studentIds);
        }

        // Period filter
        if ($periodType == 'month') {
            $query->where('month', $month)->where('year', $year);
        } elseif ($periodType == 'ytd') {
            $query->where(function($q) use ($year, $month) {
                $q->where('year', '<', $year)
                  ->orWhere(function($q2) use ($year, $month) {
                      $q2->where('year', '=', $year)
                         ->where('month', '<=', $month);
                  });
            });
        }
        // yearly = no additional filter

        $bills = $query->orderBy('year')->orderBy('month')->get();

        // Calculate statistics
        $totalBills = $bills->count();
        $totalAmount = $bills->sum('amount');
        $totalPaid = $bills->sum('paid_amount');
        $totalOutstanding = $bills->sum(function($bill) {
            return ($bill->amount - $bill->paid_amount) + $bill->late_fee;
        });

        // Group by student for matrix view (student per row, months as columns)
        $studentsData = $bills->groupBy('student_id')->map(function($studentBills) use ($academicYearId) {
            $student = $studentBills->first()->student;
            
            // Get classroom for this academic year
            $classroom = $student->classrooms->where('pivot.academic_year_id', $academicYearId)->first();
            
            // Group bills by month
            $monthlyBills = [];
            foreach ($studentBills as $bill) {
                $monthKey = $bill->month;
                $monthlyBills[$monthKey] = [
                    'status' => $bill->status,
                    'paid_amount' => $bill->paid_amount,
                    'amount' => $bill->amount,
                    'is_paid' => $bill->status == 'lunas'
                ];
            }
            
            return [
                'student'          => $student,
                'classroom'        => $classroom,
                'monthly_bills'    => $monthlyBills,
                'total_bills'      => $studentBills->count(),
                'paid_count'       => $studentBills->where('status', 'lunas')->count(),
                'total_amount'     => $studentBills->sum('amount'),
                'total_paid'       => $studentBills->sum('paid_amount'),
                'total_outstanding'=> max(0, $studentBills->sum('amount') - $studentBills->sum('paid_amount')),
            ];
        });

        // Get filter info for display
        $selectedPaymentType = $paymentTypeId ? PaymentType::find($paymentTypeId) : null;
        $selectedAcademicYear = $academicYearId ? AcademicYear::find($academicYearId) : null;
        $selectedClassroom = $classroomId ? Classroom::find($classroomId) : null;

        return view('treasurer.reports.index', compact(
            'paymentTypes',
            'academicYears',
            'classrooms',
            'paymentTypeId',
            'academicYearId',
            'classroomId',
            'periodType',
            'month',
            'year',
            'showAll',
            'totalBills',
            'totalAmount',
            'totalPaid',
            'totalOutstanding',
            'studentsData',
            'selectedPaymentType',
            'selectedAcademicYear',
            'selectedClassroom'
        ));
    }

    public function export(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Same filter logic as index
        $paymentTypeId = $request->payment_type_id;
        $academicYearId = $request->academic_year_id;
        $classroomId = $request->classroom_id;
        $periodType = $request->period_type ?? 'ytd';
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $showAll = $request->boolean('show_all');

        $query = StudentBill::with(['student', 'paymentType', 'academicYear'])
            ->whereHas('student', function ($q) use ($schoolId, $showAll) {
                $q->where('school_id', $schoolId);
                if (!$showAll) {
                    $q->where('status', 'aktif');
                }
            });

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        if ($paymentTypeId) {
            $query->where('payment_type_id', $paymentTypeId);
        }

        if ($classroomId) {
            $studentIds = DB::table('student_classes')
                ->where('classroom_id', $classroomId)
                ->where('academic_year_id', $academicYearId)
                ->pluck('student_id');
            $query->whereIn('student_id', $studentIds);
        }

        if ($periodType == 'month') {
            $query->where('month', $month)->where('year', $year);
        } elseif ($periodType == 'ytd') {
            $query->where(function($q) use ($year, $month) {
                $q->where('year', '<', $year)
                  ->orWhere(function($q2) use ($year, $month) {
                      $q2->where('year', '=', $year)
                         ->where('month', '<=', $month);
                  });
            });
        }

        $bills = $query->orderBy('year')->orderBy('month')->get();

        // Get filter info
        $filters = [
            'payment_type'       => $paymentTypeId ? PaymentType::find($paymentTypeId) : null,
            'academic_year'      => $academicYearId ? AcademicYear::find($academicYearId) : null,
            'classroom'          => $classroomId ? Classroom::find($classroomId) : null,
            'school'             => null,
            'period_type'        => $periodType,
            'month'              => $month,
            'year'               => $year,
            'show_school_column' => false,
        ];

        return \Excel::download(
            new \App\Exports\BillsReportExport($bills, $filters),
            'laporan-pembayaran-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
