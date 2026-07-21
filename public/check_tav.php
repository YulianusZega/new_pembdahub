<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');

// Simulate the full_auto_plot classroom matching for XII TE
$schoolId = 3;
$academicYearId = 5;
$allClassrooms = App\Models\Classroom::where('school_id', $schoolId)->where('academic_year_id', $academicYearId)->get();

function normalizeClassName($name) {
    $name = strtoupper($name);
    $name = str_replace(['.','-','_'], ' ', $name);
    $name = preg_replace('/\s+/', ' ', $name);
    $name = str_replace(' TJKT', ' TKJ', $name);
    $name = str_replace(' TE', ' TAV', $name);
    return trim($name);
}

$normPdf = normalizeClassName('XII TE');
echo "Searching for: $normPdf\n";

$classroom = $allClassrooms->first(function($c) use ($normPdf) {
    $dbName = normalizeClassName($c->class_name);
    if ($dbName === $normPdf) return true;
    if (strpos($dbName, $normPdf) !== false || strpos($normPdf, $dbName) !== false) return true;
    return false;
});

if ($classroom) {
    echo "FOUND: ID={$classroom->id} Name={$classroom->class_name}\n";
} else {
    echo "NOT FOUND!\n";
}

// Check teacher Fil Hulu
$t = App\Models\Teacher::where('school_id', 3)->where('full_name', 'like', '%Filiaro Hulu%')->first();
echo "Teacher 'Fil Hulu' => " . ($t ? "ID={$t->id}" : "NOT FOUND") . "\n";

// Wait - masterData uses 'Fil Hulu' not 'Fil. Hulu'
$mapping = ['Fil Hulu' => 'Filiaro Hulu, ST', 'Fil. Hulu' => 'Filiaro Hulu, ST'];
echo "\nTeacher mapping check:\n";
foreach ($mapping as $short => $full) {
    $t = App\Models\Teacher::where('school_id', 3)->where('full_name', 'like', "%{$full}%")->first();
    echo "  '$short' => '$full' => " . ($t ? "ID={$t->id}" : "NOT FOUND") . "\n";
}

// Check schedules by classroom
$sched = DB::table('schedules')->where('classroom_id', $classroom->id ?? 0)
    ->where('academic_year_id', 5)->where('semester_id', 7)
    ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
    ->select('time_slots.day_of_week', 'time_slots.slot_name')
    ->orderBy('time_slots.day_of_week')
    ->get();
echo "\nSchedules for matched classroom:\n";
foreach ($sched as $s) echo "  {$s->day_of_week}: {$s->slot_name}\n";
