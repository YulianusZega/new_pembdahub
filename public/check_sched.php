<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$courses = \App\Models\LmsCourse::all();
foreach($courses as $c) {
    echo "ID: {$c->id}, Code: {$c->code}, Title: {$c->title}, Teacher: {$c->teacher_id}\n";
}
