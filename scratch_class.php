<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';
$app = app();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$c = App\Models\Classroom::where('school_id', 2) // assuming 2 is SMP
    ->get(['id', 'class_name', 'grade_level', 'school_id', 'academic_year_id', 'is_active']);
echo json_encode($c->toArray(), JSON_PRETTY_PRINT);
