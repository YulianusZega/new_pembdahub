<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentBill;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $schoolId = $isSuperAdmin ? $request->school_id : $user->school_id;

        // Filter options
        $schools = $isSuperAdmin ? School::where('is_active', true)->schoolsOnly()->orderBy('name')->get() : collect();

        $paymentTypeQuery = PaymentType::where('is_active', true)->orderBy('type_name');
        if (!$isSuperAdmin) {
            $paymentTypeQuery->where('school_id', $user->school_id);
        } elseif ($schoolId) {
            $paymentTypeQuery->where('school_id', $schoolId);
        }
        $paymentTypes = $paymentTypeQuery->get();

        $academicYears = AcademicYear::orderBy('year', 'desc')->get();

        // Default filters
        $paymentTypeId = $request->payment_type_id;
        $academicYearId = $request->academic_year_id ?? AcademicYear::where('is_active', true)->first()?->id;
        $classroomId = $request->classroom_id;
        $periodType = $request->period_type ?? 'ytd';
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $showAll = $request->boolean('show_all');

        $classroomQuery = Classroom::orderBy('class_name');
        if (!$isSuperAdmin) {
            $classroomQuery->where('school_id', $user->school_id);
        } elseif ($schoolId) {
            $classroomQuery->where('school_id', $schoolId);
        }
        if ($academicYearId) {
            $classroomQuery->where('academic_year_id', $academicYearId);
        }
        $classrooms = $classroomQuery->get();

        // Build query
        $query = StudentBill::with(['student.school', 'paymentType', 'academicYear'])
            ->whereHas('student', function ($q) use ($isSuperAdmin, $schoolId, $showAll) {
                if (!$isSuperAdmin && $schoolId) {
                    $q->where('school_id', $schoolId);
                } elseif ($isSuperAdmin && $schoolId) {
                    $q->where('school_id', $schoolId);
                } elseif (!$isSuperAdmin) {
                    // Should not happen, but fallback
                    $q->where('school_id', 0);
                }
                if (!$showAll) {
                    $q->where('status', 'aktif');
                }
            });

        // For SuperAdmin without school filter, show all schools
        if ($isSuperAdmin && !$schoolId) {
            $query = StudentBill::with(['student.school', 'paymentType', 'academicYear'])
                ->whereHas('student', function ($q) use ($showAll) {
                    if (!$showAll) {
                        $q->where('status', 'aktif');
                    }
                });
        }

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
            $query->where(function ($q) use ($year, $month) {
                $q->where('year', '<', $year)
                  ->orWhere(function ($q2) use ($year, $month) {
                      $q2->where('year', '=', $year)
                         ->where('month', '<=', $month);
                  });
            });
        }

        $bills = $query->orderBy('year')->orderBy('month')->get();

        // Calculate statistics
        $totalBills = $bills->count();
        $totalAmount = $bills->sum('amount');
        $totalPaid = $bills->sum('paid_amount');
        $totalOutstanding = $bills->sum(function ($bill) {
            return max(0, $bill->amount - $bill->paid_amount);
        });

        // Group by student for matrix view
        $studentsData = $bills->groupBy('student_id')->map(function ($studentBills) use ($academicYearId) {
            $student = $studentBills->first()->student;

            // Get classroom for this academic year
            $classroom = $student->classrooms->where('pivot.academic_year_id', $academicYearId)->first();

            // Group bills by month
            $monthlyBills = [];
            foreach ($studentBills as $bill) {
                $monthKey = $bill->month;
                $monthlyBills[$monthKey] = [
                    'status'      => $bill->status,
                    'paid_amount' => $bill->paid_amount,
                    'amount'      => $bill->amount,
                    'is_paid'     => $bill->status == 'lunas',
                ];
            }

            $totalStudentAmount    = $studentBills->sum('amount');
            $totalStudentPaid      = $studentBills->sum('paid_amount');
            $totalStudentOutstanding = max(0, $totalStudentAmount - $totalStudentPaid);

            return [
                'student'          => $student,
                'classroom'        => $classroom,
                'monthly_bills'    => $monthlyBills,
                'total_bills'      => $studentBills->count(),
                'paid_count'       => $studentBills->where('status', 'lunas')->count(),
                'total_amount'     => $totalStudentAmount,
                'total_paid'       => $totalStudentPaid,
                'total_outstanding'=> $totalStudentOutstanding,
            ];
        });

        // Sort: aktif first, then by classroom, then by name
        $studentsData = $studentsData->sortBy(function ($data) {
            return [
                $data['student']->status != 'aktif' ? 1 : 0,
                $data['classroom']?->class_name ?? 'zzz',
                $data['student']->full_name,
            ];
        });

        // Get filter info for display
        $selectedPaymentType  = $paymentTypeId ? PaymentType::find($paymentTypeId) : null;
        $selectedAcademicYear = $academicYearId ? AcademicYear::find($academicYearId) : null;
        $selectedClassroom    = $classroomId ? Classroom::find($classroomId) : null;
        $selectedSchool       = $schoolId ? School::find($schoolId) : null;

        return view('admin.reports.index', compact(
            'schools',
            'paymentTypes',
            'academicYears',
            'classrooms',
            'schoolId',
            'paymentTypeId',
            'academicYearId',
            'classroomId',
            'periodType',
            'month',
            'year',
            'showAll',
            'isSuperAdmin',
            'totalBills',
            'totalAmount',
            'totalPaid',
            'totalOutstanding',
            'studentsData',
            'selectedPaymentType',
            'selectedAcademicYear',
            'selectedClassroom',
            'selectedSchool'
        ));
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $schoolId = $isSuperAdmin ? $request->school_id : $user->school_id;

        $paymentTypeId  = $request->payment_type_id;
        $academicYearId = $request->academic_year_id;
        $classroomId    = $request->classroom_id;
        $periodType     = $request->period_type ?? 'ytd';
        $month          = $request->month ?? now()->month;
        $year           = $request->year ?? now()->year;
        $showAll        = $request->boolean('show_all');

        $query = StudentBill::with(['student.school', 'student.classrooms', 'paymentType', 'academicYear'])
            ->whereHas('student', function ($q) use ($isSuperAdmin, $schoolId, $showAll) {
                if ($schoolId) {
                    $q->where('school_id', $schoolId);
                }
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
            $query->where(function ($q) use ($year, $month) {
                $q->where('year', '<', $year)
                  ->orWhere(function ($q2) use ($year, $month) {
                      $q2->where('year', '=', $year)
                         ->where('month', '<=', $month);
                  });
            });
        }

        $bills = $query->orderBy('year')->orderBy('month')->get();

        $filters = [
            'payment_type'  => $paymentTypeId ? PaymentType::find($paymentTypeId) : null,
            'academic_year' => $academicYearId ? AcademicYear::find($academicYearId) : null,
            'classroom'     => $classroomId ? Classroom::find($classroomId) : null,
            'school'        => $schoolId ? School::find($schoolId) : null,
            'period_type'   => $periodType,
            'month'         => $month,
            'year'          => $year,
            'show_school_column' => $isSuperAdmin,
        ];

        $schoolName = $schoolId ? School::find($schoolId)?->name : ($isSuperAdmin ? 'semua-sekolah' : 'sekolah');
        $fileName = 'laporan-tagihan-' . str_replace(' ', '-', strtolower($schoolName)) . '-' . now()->format('Y-m-d') . '.xlsx';

        return \Excel::download(
            new \App\Exports\BillsReportExport($bills, $filters),
            $fileName
        );
    }
}
