<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;

$ta = TeachingAssignment::with(['teacher', 'subject', 'classroom'])
    ->whereHas('teacher', function($q) {
        $q->where('full_name', 'LIKE', '%Herman%');
    })
    ->whereHas('subject', function($q) {
        $q->where('subject_name', 'LIKE', '%DDPK-DPIB%');
    })
    ->first();
    
if ($ta) {
    echo "HERMAN TA:\n";
    echo "ID: " . $ta->id . "\n";
    echo "Classroom ID: " . $ta->classroom_id . "\n";
    echo "Subject ID: " . $ta->subject_id . "\n";
    echo "Semester ID: " . $ta->semester_id . "\n";
    echo "AY ID: " . $ta->academic_year_id . "\n";
    echo "Active: " . $ta->is_active . "\n";
} else {
    echo "Herman TA for DDPK-DPIB not found!\n";
}

$ta2 = TeachingAssignment::find(6555); // Martperan's TA
if ($ta2) {
    echo "\nMARTPERAN TA:\n";
    echo "ID: " . $ta2->id . "\n";
    echo "Classroom ID: " . $ta2->classroom_id . "\n";
    echo "Subject ID: " . $ta2->subject_id . "\n";
    echo "Semester ID: " . $ta2->semester_id . "\n";
    echo "AY ID: " . $ta2->academic_year_id . "\n";
    echo "Active: " . $ta2->is_active . "\n";
}
