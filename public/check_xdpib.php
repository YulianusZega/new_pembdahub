<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$secret = $_GET['secret'] ?? '';
if ($secret !== 'pembda99') {
    die("Unauthorized");
}

$classrooms = App\Models\Classroom::where('class_name', 'like', '%X DPIB%')->get();
if ($classrooms->isEmpty()) {
    echo "Classroom not found\n";
    exit;
}

foreach ($classrooms as $classroom) {
    echo "Classroom: " . $classroom->class_name . " (ID: " . $classroom->id . ")\n";
    $schedules = App\Models\Schedule::with(['teachingAssignment.subject', 'teachingAssignment.teacher', 'timeSlot'])
        ->where('classroom_id', $classroom->id)
        ->get();

    echo "<pre>";
    echo "Jadwal Kelas " . $classroom->class_name . " (SELURUH HARI)\n";
    echo "============================\n";
    $sorted = $schedules->sortBy(function($s) {
        return $s->day_of_week . ' - ' . ($s->timeSlot->slot_order ?? 99);
    });

    foreach ($sorted as $s) {
        echo 'Hari: ' . $s->day_of_week . ' | Slot: ' . ($s->timeSlot->slot_order ?? '?') . ' (' . ($s->timeSlot->slot_name ?? '?') . ') | Mapel: ' . ($s->teachingAssignment ? $s->teachingAssignment->subject->name : 'N/A') . ' | Guru: ' . ($s->teachingAssignment ? $s->teachingAssignment->teacher->full_name : 'N/A') . "\n";
    }
    echo "</pre>";
}
