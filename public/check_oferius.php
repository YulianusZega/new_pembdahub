<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Classroom;
use App\Models\TeachingAssignment;

$cls = Classroom::where('school_id', 3)->where('class_name', 'LIKE', '%X DPIB%')->first();
if ($cls) {
    echo "Class: " . $cls->class_name . " (ID: " . $cls->id . ")\n";
    $tas = TeachingAssignment::with(['subject', 'teacher'])
        ->where('classroom_id', $cls->id)
        ->get();
    echo "Total TA: " . count($tas) . "\n";
    foreach($tas as $ta) {
        $subjType = $ta->subject->subject_group ?? 'N/A';
        echo "- Subj: " . ($ta->subject->subject_name ?? 'N/A') . " ($subjType) | JP: " . $ta->hours_per_week . "\n";
    }
}
