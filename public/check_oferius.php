<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;

$tas = TeachingAssignment::where('classroom_id', 353)->get();
foreach($tas as $ta) {
    echo $ta->id . " | " . $ta->subject->name . " | is_active: " . $ta->is_active . " | semester: " . $ta->semester_id . "\n";
}
