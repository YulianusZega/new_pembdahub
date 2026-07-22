<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;

$ta2 = TeachingAssignment::find(6555); // Martperan
if ($ta2) {
    $ta2->block_type = 'split'; // KELOMPOK B
    $ta2->save();
    echo "SUCCESS: Updated Martperan TA to block_type = split (Kelompok B).\n";
} else {
    echo "TA 6555 not found.\n";
}
