<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Schedule;
use App\Models\Classroom;

$classrooms = Classroom::where('school_id', 3)->get();
foreach($classrooms as $c) {
    $count = Schedule::where('classroom_id', $c->id)->count();
    $mondays = Schedule::where('classroom_id', $c->id)->where('day_of_week', 'monday')->count();
    $fridays = Schedule::where('classroom_id', $c->id)->where('day_of_week', 'friday')->count();
    echo "Class {$c->class_name}: Total=$count, Mon=$mondays, Fri=$fridays\n";
}
