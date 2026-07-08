<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\School;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $date = $request->get('date', today()->toDateString());

        $schoolId = $request->get('school_id');
        if (!$user->isSuperAdmin() && !$user->isKetuaYayasan()) {
            $schoolId = $user->school_id;
        }

        $employees = collect();
        $attendances = collect();
        $stats = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0, 'dinas_luar' => 0, 'cuti' => 0, 'belum' => 0];

        if ($schoolId) {
            $employees = Employee::with('school')
                ->where('is_active', true)
                ->where('school_id', $schoolId)
                ->orderBy('full_name')
                ->get();

            $attendances = EmployeeAttendance::where('date', $date)
                ->where('school_id', $schoolId)
                ->get()
                ->keyBy('employee_id');

            foreach ($employees as $emp) {
                $att = $attendances->get($emp->id);
                if ($att) {
                    $stats[$att->status] = ($stats[$att->status] ?? 0) + 1;
                } else {
                    $stats['belum']++;
                }
            }
        }

        $schools = $user->isSuperAdmin() || $user->isKetuaYayasan()
            ? School::orderBy('name')->get()
            : School::where('id', $user->school_id)->get();

        return view('admin.employees.attendance.index', compact(
            'employees', 'attendances', 'stats', 'schools', 'schoolId', 'date'
        ));
    }

    public function bulkInput(Request $request)
    {
        $user = auth()->user();
        $date = $request->get('date', today()->toDateString());

        $schoolId = $request->get('school_id');
        if (!$user->isSuperAdmin() && !$user->isKetuaYayasan()) {
            $schoolId = $user->school_id;
        }

        if (!$schoolId) {
            return redirect()->route('admin.employees.attendance.index')
                ->with('error', 'Pilih sekolah terlebih dahulu.');
        }

        $school = School::findOrFail($schoolId);
        $employees = Employee::where('is_active', true)
            ->where('school_id', $schoolId)
            ->orderBy('full_name')
            ->get();

        $existing = EmployeeAttendance::where('date', $date)
            ->where('school_id', $schoolId)
            ->get()
            ->keyBy('employee_id');

        return view('admin.employees.attendance.bulk-input', compact(
            'employees', 'existing', 'school', 'date'
        ));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'school_id' => 'required|exists:schools,id',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:hadir,sakit,izin,alpha,dinas_luar,cuti',
        ]);

        $date = $request->date;
        $schoolId = $request->school_id;
        $userId = auth()->id();
        $count = 0;

        foreach ($request->attendance as $employeeId => $data) {
            EmployeeAttendance::updateOrCreate(
                ['employee_id' => $employeeId, 'date' => $date],
                [
                    'school_id' => $schoolId,
                    'status' => $data['status'],
                    'time_in' => $data['time_in'] ?? null,
                    'time_out' => $data['time_out'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'recorded_via' => 'manual',
                    'recorded_by' => $userId,
                ]
            );
            $count++;
        }

        return redirect()->route('admin.employees.attendance.index', [
            'date' => $date, 'school_id' => $schoolId
        ])->with('success', "Absensi {$count} pegawai berhasil disimpan.");
    }

    public function rekap(Request $request)
    {
        $user = auth()->user();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $schoolId = $request->get('school_id');
        if (!$user->isSuperAdmin() && !$user->isKetuaYayasan()) {
            $schoolId = $user->school_id;
        }

        $employees = collect();
        $attendanceData = [];
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        if ($schoolId) {
            $employees = Employee::where('is_active', true)
                ->where('school_id', $schoolId)
                ->with(['teacher.schedules'])
                ->orderBy('full_name')
                ->get();

            $attendances = EmployeeAttendance::where('school_id', $schoolId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            // Build matrix: employee_id => [day => EmployeeAttendance model]
            foreach ($attendances as $att) {
                $day = $att->date->day;
                $attendanceData[$att->employee_id][$day] = $att;
            }
        }

        $schools = $user->isSuperAdmin() || $user->isKetuaYayasan()
            ? School::orderBy('name')->get()
            : School::where('id', $user->school_id)->get();

        return view('admin.employees.attendance.rekap', compact(
            'employees', 'attendanceData', 'schools', 'schoolId', 'month', 'year', 'daysInMonth'
        ));
    }
}
