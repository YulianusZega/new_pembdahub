<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$modules = \App\Models\TrainingModule::all();
foreach ($modules as $m) {
    echo "ID: {$m->id} | Title: {$m->title} | Published: {$m->is_published} | PDF: " . ($m->pdf_file ?: 'NULL') . "\n";
}
