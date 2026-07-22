<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Teacher;
use App\Models\TeachingAssignment;

$teachers = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Ofer%')->get();
foreach($teachers as $t) {
    echo "Oferius ID: " . $t->id . " | Name: " . $t->full_name . "\n";
}

$tas = TeachingAssignment::with('teacher')->where('classroom_id', 353)->where('subject_id', 212)->get();
foreach($tas as $ta) {
    echo "TA 212 -> Teacher: " . ($ta->teacher->full_name ?? '') . " (ID: ".$ta->teacher_id.") | is_active: " . $ta->is_active . " | JP: " . $ta->hours_per_week . "\n";
}

$t_otiani = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Otiani%')->first();
if ($t_otiani) {
    echo "Otiani ID: " . $t_otiani->id . "\n";
}
