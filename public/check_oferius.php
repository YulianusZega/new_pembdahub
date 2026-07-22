<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;

$ta = TeachingAssignment::find(6555); // Martperan's TA I just created
if ($ta) {
    $ta->classroom_id = 346; // Fix classroom to match Herman Putra's
    $ta->save();
    echo "SUCCESS: Updated Martperan TA. Set Classroom to 346.\n";
} else {
    echo "TA 6555 not found.\n";
}
