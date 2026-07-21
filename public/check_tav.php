<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');
use Illuminate\Support\Facades\DB;

$schoolId = 3;
$academicYearId = 5;
$allClassrooms = App\Models\Classroom::where('school_id', $schoolId)->where('academic_year_id', $academicYearId)->get();

$normFn = function($name) {
    $name = strtoupper($name);
    $name = str_replace(['.','-','_'], ' ', $name);
    $name = preg_replace('/\s+/', ' ', $name);
    $name = str_replace(' TJKT', ' TKJ', $name);
    $name = str_replace(' TE', ' TAV', $name);
    return trim($name);
};

$normPdf = $normFn('XII TE');
echo "Searching for: $normPdf\n";

$classroom = $allClassrooms->first(function($c) use ($normPdf, $normFn) {
    $dbName = $normFn($c->class_name);
    if ($dbName === $normPdf) return true;
    if (strpos($dbName, $normPdf) !== false || strpos($normPdf, $dbName) !== false) return true;
    return false;
});

echo $classroom ? "FOUND: ID={$classroom->id} Name={$classroom->class_name}\n" : "NOT FOUND!\n";

// Teacher check
$teacherMapping = [
    'Fil Hulu' => 'Filiaro Hulu, ST',
    'Fil. Hulu' => 'Filiaro Hulu, ST'
];
$teacherCache = [];
foreach ($teacherMapping as $short => $full) {
    $t = App\Models\Teacher::where('school_id', $schoolId)->where('full_name', 'like', "%{$full}%")->first();
    echo "Teacher '$short' => " . ($t ? "ID={$t->id}" : "NOT FOUND") . "\n";
    if ($t) $teacherCache[$short] = $t->id;
}

// Check what's in schedulePlacements for monday
echo "\nSchedules for classroom {$classroom->id}:\n";
$sched = DB::table('schedules')
    ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
    ->where('schedules.classroom_id', $classroom->id)
    ->where('schedules.academic_year_id', 5)
    ->where('schedules.semester_id', 7)
    ->select('time_slots.day_of_week', 'time_slots.slot_name')
    ->orderBy('time_slots.day_of_week')
    ->get();
foreach ($sched as $s) echo "  {$s->day_of_week}: {$s->slot_name}\n";
echo "Total: " . $sched->count() . "\n";
