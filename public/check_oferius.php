<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Subject;
use App\Models\TeachingAssignment;

$subjectDdpk = Subject::where('school_id', 3)->where('subject_name', 'LIKE', '%DDPK-DPIB%')->first();

if (!$subjectDdpk) {
    echo "Subject DDPK-DPIB not found!\n";
    die();
}

$ta = TeachingAssignment::find(6555); // Martperan's TA I just created
if ($ta) {
    $ta->academic_year_id = 5; // Revert back to 5
    $ta->subject_id = $subjectDdpk->id; // Fix subject to DDPK-DPIB
    $ta->save();
    echo "SUCCESS: Updated Martperan TA. Set Subj to " . $subjectDdpk->subject_name . " and AY back to 5.\n";
} else {
    echo "TA 6555 not found.\n";
}
