<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\BlockSchedule;
use App\Models\BlockStudentGroup;
use App\Models\Classroom;
use App\Models\Semester;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BlockScheduleController extends Controller
{
    /**
     * Helper: ambil konteks sekolah, tahun ajaran, dan semester aktif
     */
    private function getContext()
    {
        $user = auth()->user();
        
        // Sesuai permintaan: Sistem Blok hanya berlaku untuk unit SMK
        $school = \App\Models\School::where('type', 'SMK')->orWhere('name', 'like', '%SMK%')->first();
        
        // Fallback jika unit SMK tidak ditemukan
        if (!$school) {
            $school = $user->school;
        }
        
        $academicYear = AcademicYear::where('is_active', true)->first();
        $semester = $academicYear
            ? Semester::where('academic_year_id', $academicYear->id)->where('is_active', true)->first()
            : null;

        return compact('user', 'school', 'academicYear', 'semester');
    }

    /**
     * Helper: ambil BlockSchedule aktif untuk konteks saat ini
     */
    private function getActiveBlockSchedule($schoolId, $academicYearId, $semesterId)
    {
        return BlockSchedule::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->first();
    }

    /**
     * Halaman utama — konfigurasi blok + daftar kelas + kalender rotasi
     */
    public function index()
    {
        $ctx = $this->getContext();
        extract($ctx);

        if (!$academicYear || !$semester) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Tahun pelajaran atau semester aktif tidak ditemukan.');
        }

        $blockSchedule = $this->getActiveBlockSchedule($school->id, $academicYear->id, $semester->id);

        // Ambil semua kelas untuk sekolah ini di tahun ajaran aktif
        $classrooms = Classroom::where('school_id', $school->id)
            ->where('academic_year_id', $academicYear->id)
            ->orderBy('grade_level')
            ->orderBy('class_name')
            ->get();

        // Hitung jumlah siswa dan grup per kelas
        foreach ($classrooms as $classroom) {
            $classroom->total_students = StudentClass::where('classroom_id', $classroom->id)
                ->where('academic_year_id', $academicYear->id)
                ->where('status', 'aktif')
                ->count();

            if ($blockSchedule) {
                $classroom->group_a_count = BlockStudentGroup::where('block_schedule_id', $blockSchedule->id)
                    ->where('classroom_id', $classroom->id)
                    ->where('group', 'A')
                    ->count();

                $classroom->group_b_count = BlockStudentGroup::where('block_schedule_id', $blockSchedule->id)
                    ->where('classroom_id', $classroom->id)
                    ->where('group', 'B')
                    ->count();
            } else {
                $classroom->group_a_count = 0;
                $classroom->group_b_count = 0;
            }
        }

        // Kalender rotasi & minggu aktif
        $swapPeriods = [];
        $currentWeek = 0;
        if ($blockSchedule) {
            $swapPeriods = $blockSchedule->getSwapPeriods();
            $currentWeek = $blockSchedule->getWeekNumber(Carbon::now());
        }

        return view('admin.block-schedule.index', compact(
            'blockSchedule', 'classrooms', 'academicYear', 'semester', 'school',
            'swapPeriods', 'currentWeek'
        ));
    }

    /**
     * Simpan konfigurasi blok baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'swap_interval_weeks' => 'required|integer|min:1|max:8',
        ]);

        $ctx = $this->getContext();
        extract($ctx);

        BlockSchedule::firstOrCreate(
            [
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
            ],
            [
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'swap_interval_weeks' => $request->swap_interval_weeks,
                'is_active' => true,
            ]
        );

        return redirect()->back()->with('success', 'Konfigurasi Sistem Blok berhasil disimpan.');
    }

    /**
     * Update konfigurasi blok (termasuk interval rotasi)
     */
    public function update(Request $request, BlockSchedule $blockSchedule)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'swap_interval_weeks' => 'required|integer|min:1|max:8',
        ]);

        $blockSchedule->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'swap_interval_weeks' => $request->swap_interval_weeks,
        ]);

        return redirect()->back()->with('success', 'Konfigurasi berhasil diperbarui. Kalender rotasi sudah disesuaikan.');
    }

    /**
     * Halaman bagi siswa ke Grup A/B per kelas
     */
    public function manageGroups(Classroom $classroom)
    {
        $ctx = $this->getContext();
        extract($ctx);

        $blockSchedule = $this->getActiveBlockSchedule($school->id, $academicYear->id, $semester->id);

        if (!$blockSchedule) {
            return redirect()->route('admin.block-schedule.index')
                ->with('error', 'Silakan konfigurasi Sistem Blok terlebih dahulu.');
        }

        // Ambil siswa aktif di kelas ini
        $studentClasses = StudentClass::with('student')
            ->where('classroom_id', $classroom->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 'aktif')
            ->get()
            ->sortBy(fn($sc) => $sc->student->full_name ?? '');

        // Ambil pembagian grup yang sudah ada
        $existingGroups = BlockStudentGroup::where('block_schedule_id', $blockSchedule->id)
            ->where('classroom_id', $classroom->id)
            ->pluck('group', 'student_id')
            ->toArray();

        return view('admin.block-schedule.manage-groups', compact(
            'classroom', 'blockSchedule', 'studentClasses', 'existingGroups', 'school'
        ));
    }

    /**
     * Bagi otomatis 50:50 (berdasarkan nama/urut absen)
     */
    public function autoAssignGroups(Request $request, Classroom $classroom)
    {
        $ctx = $this->getContext();
        extract($ctx);

        $blockSchedule = $this->getActiveBlockSchedule($school->id, $academicYear->id, $semester->id);

        if (!$blockSchedule) {
            return redirect()->back()->with('error', 'Sistem Blok belum dikonfigurasi.');
        }

        $studentClasses = StudentClass::with('student')
            ->where('classroom_id', $classroom->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 'aktif')
            ->get()
            ->sortBy(fn($sc) => $sc->student->name ?? '');

        $half = (int) ceil($studentClasses->count() / 2);

        DB::transaction(function () use ($studentClasses, $half, $blockSchedule, $classroom) {
            foreach ($studentClasses->values() as $index => $studentClass) {
                $group = ($index < $half) ? 'A' : 'B';

                BlockStudentGroup::updateOrCreate(
                    [
                        'block_schedule_id' => $blockSchedule->id,
                        'student_id' => $studentClass->student_id,
                    ],
                    [
                        'classroom_id' => $classroom->id,
                        'group' => $group,
                    ]
                );
            }
        });

        $groupACount = $half;
        $groupBCount = $studentClasses->count() - $half;

        return redirect()->back()->with('success', "Berhasil membagi siswa: Grup A = {$groupACount} orang, Grup B = {$groupBCount} orang.");
    }

    /**
     * Simpan pembagian grup manual
     */
    public function saveGroups(Request $request, Classroom $classroom)
    {
        $request->validate([
            'groups' => 'required|array',
            'groups.*' => 'in:A,B',
        ]);

        $ctx = $this->getContext();
        extract($ctx);

        $blockSchedule = $this->getActiveBlockSchedule($school->id, $academicYear->id, $semester->id);

        if (!$blockSchedule) {
            return redirect()->back()->with('error', 'Sistem Blok belum dikonfigurasi.');
        }

        DB::transaction(function () use ($request, $blockSchedule, $classroom) {
            foreach ($request->groups as $studentId => $group) {
                BlockStudentGroup::updateOrCreate(
                    [
                        'block_schedule_id' => $blockSchedule->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'classroom_id' => $classroom->id,
                        'group' => $group,
                    ]
                );
            }
        });

        return redirect()->back()->with('success', 'Pembagian grup siswa berhasil disimpan.');
    }

    /**
     * Tampilan jadwal blok (Kelompok A + B side by side)
     */
    public function viewSchedule(Request $request)
    {
        $ctx = $this->getContext();
        extract($ctx);

        $blockSchedule = $this->getActiveBlockSchedule($school->id, $academicYear->id, $semester->id);

        $classrooms = Classroom::where('school_id', $school->id)
            ->where('academic_year_id', $academicYear->id)
            ->orderBy('grade_level')
            ->orderBy('class_name')
            ->get();

        $selectedClassroomId = $request->get('classroom_id');
        $scheduleA = collect();
        $scheduleB = collect();
        $timeSlots = collect();
        $currentRotation = 'normal';
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $dayLabels = [
            'monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu',
            'thursday' => 'Kamis', 'friday' => 'Jumat',
        ];

        if ($selectedClassroomId && $blockSchedule) {
            $timeSlots = \App\Models\TimeSlot::where('school_id', $school->id)
                ->where('is_active', true)
                ->where('slot_type', 'lesson')
                ->orderBy('slot_order')
                ->get();

            $rawSchedules = \App\Models\Schedule::with(['teachingAssignment.subject', 'teachingAssignment.teacher', 'timeSlot'])
                ->where('classroom_id', $selectedClassroomId)
                ->whereHas('teachingAssignment', function ($q) use ($academicYear, $semester) {
                    $q->where('academic_year_id', $academicYear->id)
                      ->where('semester_id', $semester->id);
                })
                ->get();

            // Pisahkan jadwal berdasarkan block_type
            $scheduleA = $rawSchedules->filter(function ($s) {
                $blockType = $s->teachingAssignment->block_type ?? 'none';
                return in_array($blockType, ['none', 'all']);
            });

            $scheduleB = $rawSchedules->filter(function ($s) {
                return ($s->teachingAssignment->block_type ?? 'none') === 'split';
            });

            $currentRotation = $blockSchedule->getActiveRotationForDate(Carbon::now());
        }

        return view('admin.block-schedule.view-schedule', compact(
            'classrooms', 'selectedClassroomId', 'scheduleA', 'scheduleB',
            'timeSlots', 'days', 'dayLabels', 'blockSchedule', 'currentRotation', 'school'
        ));
    }

    /**
     * API: Kalender swap (JSON)
     */
    public function swapCalendar()
    {
        $ctx = $this->getContext();
        extract($ctx);

        $blockSchedule = $this->getActiveBlockSchedule($school->id, $academicYear->id, $semester->id);

        if (!$blockSchedule) {
            return response()->json([]);
        }

        return response()->json($blockSchedule->getSwapPeriods());
    }
}
