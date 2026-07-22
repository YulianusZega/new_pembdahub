<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Classroom;

$cls = Classroom::where('class_name', 'LIKE', '%X DPIB%')->get();
foreach($cls as $c) {
    echo "ID: " . $c->id . " | Name: " . $c->class_name . " | School: " . $c->school_id . "\n";
}
