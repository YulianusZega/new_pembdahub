<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ay = App\Models\AcademicYear::where('is_active', true)->first();
echo "Active AY ID: " . ($ay ? $ay->id : 'none') . "\n";
echo "Active AY Name: " . ($ay ? $ay->year : 'none') . "\n";

$classes = App\Models\Classroom::select('academic_year_id', 'is_active', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
    ->groupBy('academic_year_id', 'is_active')
    ->get();

foreach ($classes as $c) {
    echo "AY: {$c->academic_year_id}, is_active: {$c->is_active}, Count: {$c->total}\n";
}
