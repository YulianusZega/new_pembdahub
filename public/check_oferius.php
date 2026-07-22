<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;
use App\Models\Schedule;

$tas = TeachingAssignment::with('subject')->where('classroom_id', 353)->get();
$out = [];
foreach($tas as $ta) {
    $plotted = Schedule::where('teaching_assignment_id', $ta->id)->sum('duration_slots');
    $out[] = [
        'id' => $ta->id,
        'subject' => $ta->subject->code ?? $ta->subject->name ?? '',
        'active' => $ta->is_active,
        'jp' => $ta->hours_per_week,
        'plotted' => $plotted
    ];
}
echo json_encode($out);
