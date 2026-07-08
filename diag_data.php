<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- SUBJECT CODES --- \n";
$subjects = App\Models\Subject::where('school_id', 3)->get();
foreach($subjects as $s) {
    echo $s->name . " | Code: " . ($s->code ?: 'NULL') . "\n";
}

echo "\n--- TEACHER PHOTOS --- \n";
$teachers = App\Models\Teacher::where('school_id', 3)->get();
foreach($teachers as $t) {
    echo $t->full_name . " | Photo: " . ($t->photo ?: 'NULL') . "\n";
}
