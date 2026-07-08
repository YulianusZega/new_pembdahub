<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Schedule;
use App\Models\Classroom;

$schoolId = 3;
$classIds = Classroom::where('school_id', $schoolId)->pluck('id');
Schedule::whereIn('classroom_id', $classIds)->delete();

echo "Cleaned school 3 schedules.\n";
