<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;

$ta1 = TeachingAssignment::with(['teacher', 'subject'])
    ->whereHas('teacher', function($q) {
        $q->where('full_name', 'LIKE', '%Ester%');
    })
    ->whereHas('subject', function($q) {
        $q->where('subject_name', 'LIKE', '%B.IND%');
    })
    ->where('classroom_id', 346)
    ->first();
    
if ($ta1) {
    echo "B.IND TA:\n";
    echo "ID: " . $ta1->id . " | Block Type: " . $ta1->block_type . "\n";
} else {
    echo "Ester B.IND TA not found in classroom 346\n";
}

$ta2 = TeachingAssignment::find(6555); // Martperan
if ($ta2) {
    echo "\nMARTPERAN TA:\n";
    echo "ID: " . $ta2->id . " | Block Type: " . $ta2->block_type . "\n";
}
