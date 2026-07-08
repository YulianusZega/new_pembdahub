<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Subject;

$fixes = [
    'Bimbingan Konseling' => 'BK',
    'Seni Budaya' => 'SNB',
    'Prakarya' => 'PKRY',
    'Matematika' => 'MTK',
    'Bahasa Indonesia' => 'BIND',
    'Bahasa Inggris' => 'BING',
    'Informatika' => 'INFO',
    'Mulok' => 'MLK',
];

echo "Refining subject codes...\n";
$subjects = Subject::all();
$count = 0;

foreach ($subjects as $s) {
    foreach ($fixes as $name => $code) {
        if (str_contains(strtolower($s->name), strtolower($name))) {
            if ($s->code !== $code) {
                $old = $s->code;
                $s->code = $code;
                $s->save();
                echo "  Updated ID {$s->id}: '{$s->name}' -> '$old' to '$code'\n";
                $count++;
            }
            break;
        }
    }
}

echo "\nTotal refined: $count\n";
