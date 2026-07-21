<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');

// List all classrooms for school 3, TP 5
$classes = App\Models\Classroom::where('school_id', 3)->where('academic_year_id', 5)->get();
echo "Total classrooms (school=3, TP=5): " . $classes->count() . "\n\n";
foreach ($classes as $c) {
    echo "ID: {$c->id}, Name: {$c->class_name}\n";
}

echo "\n--- XII TAV search ---\n";
$tav = App\Models\Classroom::where('school_id', 3)->where('class_name', 'like', '%TAV%')->get();
foreach ($tav as $c) {
    echo "ID: {$c->id}, Name: {$c->class_name}, TP: {$c->academic_year_id}\n";
}

echo "\n--- XII TE search ---\n";
$te = App\Models\Classroom::where('school_id', 3)->where('class_name', 'like', '%TE%')->get();
foreach ($te as $c) {
    echo "ID: {$c->id}, Name: {$c->class_name}, TP: {$c->academic_year_id}\n";
}
