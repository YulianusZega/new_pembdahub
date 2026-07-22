<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

try {
    $guruTKJ = \App\Models\Teacher::firstOrCreate([
        'school_id' => 3,
        'full_name' => 'Guru TKJ (42)',
    ], [
        'is_active' => 1,
        'teacher_code' => '42',
    ]);
    echo "Created/Found Teacher: " . $guruTKJ->full_name . " (ID: " . $guruTKJ->id . ")\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
