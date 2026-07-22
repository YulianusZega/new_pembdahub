<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;

$tas = TeachingAssignment::with(['subject', 'classroom'])->where('teacher_id', 195)->get();
foreach($tas as $ta) {
    echo "ID: " . $ta->id . " | Subj: " . ($ta->subject->name ?? '') . " | Class: " . ($ta->classroom->class_name ?? '') . " | JP: " . $ta->hours_per_week . "\n";
}
