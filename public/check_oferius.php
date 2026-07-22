<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Subject;

$subjects = Subject::where('school_id', 3)->where('subject_name', 'LIKE', '%Kristen%')->get();
foreach($subjects as $s) {
    echo "ID: " . $s->id . " | Name: " . $s->subject_name . "\n";
}
