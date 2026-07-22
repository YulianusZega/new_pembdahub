<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;

$tas = TeachingAssignment::with(['teacher', 'subject', 'classroom'])
    ->whereHas('classroom', function($q) {
        $q->where('class_name', 'LIKE', '%X DPIB%');
    })
    ->whereHas('teacher', function($q) {
        $q->where('full_name', 'LIKE', '%Herman%');
    })
    ->get();
    
echo "Herman TAs for X DPIB:\n";
foreach($tas as $ta) {
    echo "ID: " . $ta->id . " | Subj: " . ($ta->subject->subject_name ?? '') . " | Sem: " . $ta->semester_id . " | AY: " . $ta->academic_year_id . " | Active: " . $ta->is_active . " | JP: " . $ta->hours_per_week . "\n";
}
