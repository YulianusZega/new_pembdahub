<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');

if (($_GET['secret'] ?? '') !== 'pembda99') die('Unauthorized');

$existing = App\Models\Classroom::where('school_id', 3)
    ->where('academic_year_id', 5)
    ->where('class_name', 'XII TSM 1')
    ->first();

if ($existing) {
    echo "XII TSM 1 sudah ada! ID: {$existing->id}\n";
} else {
    $ref = App\Models\Classroom::where('school_id', 3)
        ->where('academic_year_id', 5)
        ->where('class_name', 'XII TSM 2')
        ->first();
    
    if (!$ref) {
        die("ERROR: XII TSM 2 tidak ditemukan sebagai referensi!\n");
    }
    
    $new = $ref->replicate();
    $new->class_name = 'XII TSM 1';
    $new->class_code = 'XII-TSM-1';
    $new->save();
    
    echo "BERHASIL! XII TSM 1 dibuat dengan ID: {$new->id}\n";
    echo "Referensi dari XII TSM 2 (ID: {$ref->id})\n";
}
