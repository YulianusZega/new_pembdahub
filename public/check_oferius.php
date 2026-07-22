<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Subject;
use Illuminate\Support\Facades\DB;

$subjects = Subject::where('school_id', 3)->get();
foreach($subjects as $s) {
    echo $s->id . " | " . $s->subject_name . " | " . $s->name . " | " . $s->code . "\n";
}
