<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;

$ta = TeachingAssignment::find(6555); // Martperan's TA I just created
if ($ta) {
    $ta->academic_year_id = 1;
    $ta->save();
    echo "SUCCESS: Updated Martperan TA to AY = 1. Now it matches Herman Putra!\n";
} else {
    echo "TA 6555 not found.\n";
}
