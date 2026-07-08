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

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h2>DIAGNOSIS JADWAL SABTU (LIVE DATABASE)</h2>";

try {
    // 1. Schools
    echo "<h3>1. Daftar Semua Sekolah di Database:</h3>";
    $schools = School::all();
    foreach ($schools as $s) {
        echo "- ID: <b>" . $s->id . "</b> | Nama: " . $s->name . " | Tipe: " . $s->type . "<br>";
    }

    // 2. Active Year & Semester
    echo "<h3>2. Tahun Ajaran & Semester Aktif:</h3>";
    $activeYear = AcademicYear::where('is_active', true)->first();
    $activeSemester = Semester::where('is_active', true)->first();
    echo "- Tahun Ajaran Aktif: " . ($activeYear ? $activeYear->year . " (ID: " . $activeYear->id . ")" : 'TIDAK ADA') . "<br>";
    echo "- Semester Aktif: " . ($activeSemester ? $activeSemester->semester_name . " (ID: " . $activeSemester->id . ")" : 'TIDAK ADA') . "<br>";

    // 3. Time Slots Saturday
    echo "<h3>3. Slot Waktu Hari Sabtu (SABTU):</h3>";
    $slots = TimeSlot::where('day_of_week', 'saturday')->get();
    if ($slots->isEmpty()) {
        echo "TIDAK ADA SLOT SABTU.<br>";
    } else {
        foreach ($slots as $slot) {
            $sch = School::find($slot->school_id);
            echo "- School ID: <b>" . $slot->school_id . "</b> (" . ($sch->name ?? 'Tidak dikenal') . ") | Slot: " . $slot->slot_name . " | Order: " . $slot->slot_order . " | Is Teaching: " . ($slot->is_teaching_slot ? 'Ya' : 'Tidak') . "<br>";
        }
    }

    // 4. Saturday Schedules
    echo "<h3>4. Jadwal Pelajaran Hari Sabtu:</h3>";
    $schedules = Schedule::where('day_of_week', 'saturday')
        ->with(['school', 'classroom', 'teacher', 'subject'])
        ->get();

    if ($schedules->isEmpty()) {
        echo "TIDAK ADA JADWAL PELAJARAN SABTU.<br>";
    } else {
        echo "Total Jadwal Sabtu Ditemukan: <b>" . $schedules->count() . "</b><br><br>";
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
        echo "<tr style='background:#eee;'><th>ID Jadwal</th><th>School ID & Nama</th><th>Classroom (ID & Name)</th><th>Academic Year ID</th><th>Semester (String)</th><th>Teacher</th><th>Subject</th></tr>";
        foreach ($schedules->take(20) as $sch) {
            echo "<tr>";
            echo "<td>" . $sch->id . "</td>";
            echo "<td>ID: <b>" . $sch->school_id . "</b> (" . ($sch->school->name ?? '-') . ")</td>";
            echo "<td>ID: " . $sch->classroom_id . " (" . ($sch->classroom->class_name ?? '-') . ")</td>";
            echo "<td>" . $sch->academic_year_id . "</td>";
            echo "<td>" . $sch->semester . "</td>";
            echo "<td>" . ($sch->teacher->full_name ?? '-') . "</td>";
            echo "<td>" . ($sch->subject->name ?? '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        if ($schedules->count() > 20) {
            echo "... dan " . ($schedules->count() - 20) . " jadwal lainnya.<br>";
        }
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
