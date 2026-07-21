<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');

$schedules = DB::table('schedules')
    ->where('academic_year_id', 5)
    ->where('semester_id', 7)
    ->select('classroom_id')
    ->distinct()
    ->get();
    
echo "Classroom IDs in schedules:\n";
foreach ($schedules as $s) {
    echo "{$s->classroom_id}\n";
}
