<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\TimeSlot;

$schoolId = 3;
$slots = TimeSlot::where('school_id', $schoolId)->get();
$map = [];
foreach ($slots as $s) {
    if (preg_match('/Pelajaran\s+(\d+)/i', $s->slot_name, $m)) {
        $map[strtolower($s->day_of_week)][$m[1]] = $s->id;
    }
}
file_put_contents('slot_mapping.json', json_encode($map));
echo "Mapping Slot for School 3 DONE.\n";
print_r($map['monday'] ?? []);
