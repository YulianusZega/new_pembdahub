<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SCHOOLS ===\n";
foreach (App\Models\School::all() as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Code: {$s->code}\n";
}

echo "\n=== CLASSROOMS FOR SMAS PEMBDA 1 (ID: 2) ===\n";
$classrooms = App\Models\Classroom::where('school_id', 2)->get();
foreach ($classrooms as $c) {
    echo "ID: {$c->id} | Name: {$c->class_name} | Grade: {$c->grade_level} | Active: {$c->is_active}\n";
}
