<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');

$classes = App\Models\Classroom::where('school_id', 3)->where('academic_year_id', 5)
    ->orderBy('class_name')
    ->pluck('class_name', 'id');
echo "TP5 (" . $classes->count() . "): " . json_encode($classes, JSON_PRETTY_PRINT) . "\n";

$classesAll = App\Models\Classroom::where('school_id', 3)
    ->where('class_name', 'like', 'X %')
    ->pluck('class_name', 'id');
echo "\nAll X classes: " . json_encode($classesAll, JSON_PRETTY_PRINT);
