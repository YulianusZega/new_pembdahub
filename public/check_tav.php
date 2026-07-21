<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');

if (request('secret') !== 'pembda99') die('Unauthorized');

// Cek dulu apakah XII TSM 1 sudah ada di TP 5
$existing = App\Models\Classroom::where('school_id', 3)
    ->where('academic_year_id', 5)
    ->where('class_name', 'XII TSM 1')
    ->first();

if ($existing) {
    echo "XII TSM 1 sudah ada! ID: {$existing->id}\n";
} else {
    // Ambil contoh dari XII TSM 2 untuk referensi field lain
    $ref = App\Models\Classroom::where('school_id', 3)
        ->where('academic_year_id', 5)
        ->where('class_name', 'XII TSM 2')
        ->first();
    
    if (!$ref) {
        echo "ERROR: XII TSM 2 juga tidak ditemukan sebagai referensi!\n";
        exit;
    }
    
    echo "Referensi dari XII TSM 2 (ID: {$ref->id}):\n";
    echo json_encode($ref->toArray(), JSON_PRETTY_PRINT) . "\n\n";
    
    $new = App\Models\Classroom::create([
        'school_id' => 3,
        'academic_year_id' => 5,
        'class_name' => 'XII TSM 1',
        'grade_level' => $ref->grade_level,
        'major' => $ref->major,
        'capacity' => $ref->capacity ?? 36,
        'is_active' => true,
    ]);
    
    echo "BERHASIL! XII TSM 1 dibuat dengan ID: {$new->id}\n";
}
