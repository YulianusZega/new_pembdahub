<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use Illuminate\Support\Facades\DB;

$slots = DB::table('time_slots')
    ->whereIn('id', [515, 517, 519])
    ->get();

foreach($slots as $s) {
    echo $s->id . " => " . $s->name . "\n";
}
