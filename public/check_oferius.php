<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;

$tas = TeachingAssignment::with(['subject', 'teacher'])
    ->where('classroom_id', 377) // X DPIB ID is 377 probably? Let's check class name instead
    ->whereHas('classroom', function($q){
        $q->where('class_name', 'LIKE', '%X DPIB%');
    })
    ->get();
    
echo "Penugasan Mengajar for X DPIB:\n";
foreach($tas as $ta) {
    echo "- Subj: " . ($ta->subject->subject_name ?? 'N/A') . " | Guru: " . ($ta->teacher->full_name ?? 'N/A') . " | JP: " . $ta->hours_per_week . "\n";
}
