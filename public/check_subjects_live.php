<?php
/**
 * Live Subjects Diagnostic Script
 */
header('Content-Type: text/plain; charset=utf-8');

$SECRET_TOKEN = 'pembda2026check';

if (!isset($_GET['token']) || $_GET['token'] !== $SECRET_TOKEN) {
    http_response_code(403);
    die('⛔ Akses ditolak.');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\TeachingAssignment;
use App\Models\Teacher;
use App\Models\Subject;

$activeYear = AcademicYear::where('is_active', true)->first();
echo "ACTIVE ACADEMIC YEAR: " . ($activeYear ? $activeYear->year . " (ID: " . $activeYear->id . ")" : "NONE") . "\n\n";

if (!$activeYear) {
    die("No active academic year found.");
}

$teacherId = $_GET['teacher_id'] ?? null;

if ($teacherId) {
    $teacher = Teacher::find($teacherId);
    if (!$teacher) {
        die("Teacher ID $teacherId not found.");
    }
    echo "DIAGNOSING TEACHER: " . $teacher->name . " (ID: " . $teacher->id . ", User ID: " . $teacher->user_id . ")\n";
    
    // Classrooms where homeroom teacher
    $homeroomClassrooms = Classroom::where('homeroom_teacher_id', $teacher->id)->where('academic_year_id', $activeYear->id)->get();
    echo "- Homeroom classrooms count: " . $homeroomClassrooms->count() . "\n";
    foreach ($homeroomClassrooms as $hc) {
        echo "  * Class: " . $hc->class_name . " (ID: " . $hc->id . ")\n";
        
        // Let's see schedules in this classroom
        $scheds = Schedule::where('classroom_id', $hc->id)->get();
        echo "    Schedules in this class: " . $scheds->count() . "\n";
        foreach ($scheds as $sc) {
            echo "      - Schedule ID: {$sc->id}, Subject ID: {$sc->subject_id} (" . ($sc->subject?->subject_name ?? $sc->subject?->name) . "), Teacher ID: {$sc->teacher_id} (" . ($sc->teacher?->name ?? 'unknown') . ")\n";
        }
        
        // Let's see teaching assignments in this classroom
        $assigns = TeachingAssignment::where('classroom_id', $hc->id)->where('academic_year_id', $activeYear->id)->get();
        echo "    Teaching Assignments in this class: " . $assigns->count() . "\n";
        foreach ($assigns as $as) {
            echo "      - Assignment ID: {$as->id}, Subject ID: {$as->subject_id} (" . ($as->subject?->subject_name ?? $as->subject?->name) . "), Teacher ID: {$as->teacher_id} (" . ($as->teacher?->name ?? 'unknown') . "), active: " . ($as->is_active ? 'yes' : 'no') . "\n";
        }
    }
    
    // Classroom schedules as regular teacher
    $teacherSchedules = Schedule::where('teacher_id', $teacher->id)->get();
    echo "- Personal schedule blocks count (all years/classes): " . $teacherSchedules->count() . "\n";
    foreach ($teacherSchedules->groupBy('classroom_id') as $cId => $gp) {
        $c = Classroom::find($cId);
        echo "  * Class: " . ($c ? $c->class_name . " (ID: $cId, Year ID: {$c->academic_year_id})" : "Unknown Class ID $cId") . "\n";
        foreach ($gp as $sc) {
            echo "      - Schedule ID: {$sc->id}, Subject ID: {$sc->subject_id} (" . ($sc->subject?->subject_name ?? $sc->subject?->name) . ")\n";
        }
    }
    
    // Classroom teaching assignments as regular teacher
    $teacherAssigns = TeachingAssignment::where('teacher_id', $teacher->id)->get();
    echo "- Personal teaching assignments count (all years): " . $teacherAssigns->count() . "\n";
    foreach ($teacherAssigns->groupBy('classroom_id') as $cId => $gp) {
        $c = Classroom::find($cId);
        echo "  * Class: " . ($c ? $c->class_name . " (ID: $cId, Year ID: {$c->academic_year_id})" : "Unknown Class ID $cId") . "\n";
        foreach ($gp as $as) {
            echo "      - Assignment ID: {$as->id}, Subject ID: {$as->subject_id} (" . ($as->subject?->subject_name ?? $as->subject?->name) . "), active: " . ($as->is_active ? 'yes' : 'no') . "\n";
        }
    }
} else {
    // List all teachers
    echo "TEACHERS LIST:\n";
    foreach (Teacher::orderBy('full_name')->get() as $t) {
        echo "- ID: {$t->id}, Name: {$t->full_name}, User ID: {$t->user_id}\n";
    }
}
