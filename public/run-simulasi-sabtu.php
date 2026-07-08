<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Illuminate\Foundation\Application;
use App\Models\School;
use App\Models\TimeSlot;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\AcademicYear;
use App\Models\Semester;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if (($_GET['key'] ?? '') !== 'pembda2026') {
    die('Akses ditolak.');
}

try {
    echo "Memulai inisialisasi data simulasi Sabtu untuk SMA Pembda 1...<br><br>";

    $school = School::where('type', 'SMA')->first() ?? School::where('name', 'like', '%SMA%')->first();
    if (!$school) {
        $allSchools = School::all()->pluck('name')->toArray();
        die("Sekolah SMA tidak ditemukan. Daftar sekolah terdaftar: " . (empty($allSchools) ? 'Tidak ada' : implode(', ', $allSchools)));
    }

    // Find and activate TP. 2026/2027
    $targetYear = AcademicYear::where('year', 'like', '%2026/2027%')->first();
    if ($targetYear) {
        echo "Mengaktifkan Tahun Ajaran " . $targetYear->year . " (ID: " . $targetYear->id . ") di database...<br>";
        AcademicYear::query()->update(['is_active' => false]);
        $targetYear->is_active = true;
        $targetYear->save();
        $activeYear = $targetYear;
    } else {
        $activeYear = AcademicYear::where('is_active', true)->first();
    }

    $activeSemester = Semester::where('is_active', true)->first();

    if (!$activeYear || !$activeSemester) {
        die("Tahun ajaran atau semester aktif tidak ditemukan.");
    }

    echo "<b>Detail Sekolah & Semester yang diproses script:</b><br>";
    echo "- Sekolah Target: " . $school->name . " (ID: " . $school->id . ")<br>";
    echo "- Semester Aktif: " . ($activeSemester->semester_name ?? 'Tanpa nama') . " (ID: " . $activeSemester->id . ")<br>";
    echo "<b>Daftar Semua Sekolah di Database:</b><br>";
    foreach (School::all() as $s) {
        echo "  * ID: " . $s->id . " | Nama: " . $s->name . " | Tipe: " . $s->type . "<br>";
    }
    echo "<br>";

    // 1. Buat Time Slot hari Sabtu jika belum ada
    $saturdaySlots = TimeSlot::where('school_id', $school->id)
        ->where('day_of_week', 'saturday')
        ->get();

    if ($saturdaySlots->isEmpty()) {
        echo "Membuat slot waktu hari Sabtu...<br>";
        $slotsData = [
            ['name' => 'Upacara/Apel', 'type' => 'ceremony', 'start' => '07:15', 'end' => '08:00', 'is_teaching' => false, 'order' => 1],
            ['name' => 'Jam ke-1', 'type' => 'lesson', 'start' => '08:00', 'end' => '08:40', 'is_teaching' => true, 'order' => 2],
            ['name' => 'Jam ke-2', 'type' => 'lesson', 'start' => '08:40', 'end' => '09:20', 'is_teaching' => true, 'order' => 3],
            ['name' => 'Istirahat', 'type' => 'break', 'start' => '09:20', 'end' => '09:40', 'is_teaching' => false, 'order' => 4],
            ['name' => 'Jam ke-3', 'type' => 'lesson', 'start' => '09:40', 'end' => '10:20', 'is_teaching' => true, 'order' => 5],
            ['name' => 'Jam ke-4', 'type' => 'lesson', 'start' => '10:20', 'end' => '11:00', 'is_teaching' => true, 'order' => 6],
        ];

        foreach ($slotsData as $data) {
            TimeSlot::create([
                'school_id' => $school->id,
                'day_of_week' => 'saturday',
                'slot_name' => $data['name'],
                'slot_type' => $data['type'],
                'slot_order' => $data['order'],
                'start_time' => $data['start'],
                'end_time' => $data['end'],
                'duration_minutes' => 40,
                'is_teaching_slot' => $data['is_teaching'],
                'is_active' => true,
            ]);
        }
        $saturdaySlots = TimeSlot::where('school_id', $school->id)
            ->where('day_of_week', 'saturday')
            ->get();
        echo "Berhasil membuat " . $saturdaySlots->count() . " slot waktu hari Sabtu.<br>";
    } else {
        echo "Slot waktu hari Sabtu sudah ada.<br>";
    }

    // 2. Buat Jadwal Simulasi Hari Sabtu jika belum ada
    $classrooms = Classroom::where('school_id', $school->id)
        ->where('academic_year_id', $activeYear->id)
        ->get();
    $classroomGroups = $classrooms->groupBy('academic_year_id');
    
    $teachers = Teacher::where('school_id', $school->id)->get();
    $subjects = Subject::where('school_id', $school->id)->get();
    
    echo "<b>Data yang ditemukan untuk SMA:</b><br>";
    echo "- Jumlah Kelas Total: " . $classrooms->count() . "<br>";
    echo "- Jumlah Guru: " . $teachers->count() . "<br>";
    echo "- Jumlah Mapel: " . $subjects->count() . "<br><br>";

    $requestedYearId = $_GET['year_id'] ?? null;
    $teachingSlots = $saturdaySlots->where('is_teaching_slot', true)->sortBy('slot_order')->values();

    if ($teachingSlots->isEmpty()) {
        die("Error: Tidak ada slot waktu mengajar hari Sabtu.");
    }

    foreach ($classroomGroups as $yearId => $yearClassrooms) {
        if ($requestedYearId && $yearId != $requestedYearId) {
            continue;
        }

        $yearModel = AcademicYear::find($yearId);
        $yearName = $yearModel->year ?? "ID: $yearId";
        
        $semesterModel = Semester::where('is_active', true)->first();
        $semesterId = $semesterModel->id ?? 1;
        $semName = strtolower($semesterModel->semester_name ?? 'ganjil');
        $semesterValue = str_contains($semName, 'ganjil') ? 'ganjil' : (str_contains($semName, 'genap') ? 'genap' : 'ganjil');

        // Force reset for this academic year if requested
        $forceReset = ($_GET['force'] ?? '') === '1';
        if ($forceReset) {
            echo "Menghapus jadwal hari Sabtu yang lama untuk tahun ajaran $yearName (force reset)...<br>";
            Schedule::where('school_id', $school->id)
                ->where('academic_year_id', $yearId)
                ->where('day_of_week', 'saturday')
                ->delete();
        }

        $saturdaySchedulesCount = Schedule::where('school_id', $school->id)
            ->where('academic_year_id', $yearId)
            ->where('day_of_week', 'saturday')
            ->count();

        echo "<b>Tahun Ajaran: $yearName (ID: $yearId)</b><br>";
        echo "- Jumlah Kelas: " . $yearClassrooms->count() . "<br>";
        echo "- Jumlah Jadwal Sabtu saat ini: " . $saturdaySchedulesCount . "<br>";

        if ($saturdaySchedulesCount === 0) {
            echo "Membuat jadwal pelajaran hari Sabtu untuk tahun ajaran $yearName...<br>";

            if ($yearClassrooms->isEmpty() || $teachers->isEmpty() || $subjects->isEmpty()) {
                echo "Peringatan: Kelas, Guru, atau Mata Pelajaran tidak lengkap untuk tahun ajaran ini.<br><br>";
                continue;
            }

            $createdSchedules = 0;
            foreach ($yearClassrooms as $classIndex => $classroom) {
                foreach ($teachingSlots as $slotIndex => $timeSlot) {
                    $teacher = $teachers[($classIndex + $slotIndex) % $teachers->count()];
                    $subject = $subjects[($classIndex * 2 + $slotIndex) % $subjects->count()];

                    Schedule::create([
                        'school_id' => $school->id,
                        'classroom_id' => $classroom->id,
                        'teacher_id' => $teacher->id,
                        'subject_id' => $subject->id,
                        'time_slot_id' => $timeSlot->id,
                        'day_of_week' => 'saturday',
                        'start_time' => $timeSlot->start_time,
                        'end_time' => $timeSlot->end_time,
                        'duration_slots' => 1,
                        'academic_year_id' => $yearId,
                        'semester_id' => $semesterId,
                        'semester' => $semesterValue,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $createdSchedules++;
                }
            }
            echo "Berhasil membuat " . $createdSchedules . " jadwal pelajaran hari Sabtu.<br><br>";
        } else {
            echo "Jadwal pelajaran hari Sabtu sudah terisi.<br><br>";
            
            // Tampilkan 5 sampel jadwal
            $sampleSchedules = Schedule::where('school_id', $school->id)
                ->where('academic_year_id', $yearId)
                ->where('day_of_week', 'saturday')
                ->with(['classroom', 'teacher', 'subject'])
                ->limit(5)
                ->get();
                
            echo "<b>Daftar 5 Sampel Jadwal Sabtu di Database:</b><br>";
            foreach ($sampleSchedules as $s) {
                echo "- Kelas " . ($s->classroom->class_name ?? '-') . " | Guru: " . ($s->teacher->full_name ?? '-') . " | Mapel: " . ($s->subject->name ?? '-') . " (" . $s->start_time . "-" . $s->end_time . ")<br>";
            }
            echo "<br>";
        }
    }

    echo "<br><b>Selesai!</b> Semua persiapan simulasi Sabtu berhasil diinisialisasi.<br>";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
