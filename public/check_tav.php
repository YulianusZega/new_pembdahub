<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');

$classes = App\Models\Classroom::where('school_id', 3)->where('academic_year_id', 5)
    ->where('class_name', 'like', 'XII%')
    ->orderBy('class_name')
    ->pluck('class_name', 'id');
echo "XII classes TP5: " . json_encode($classes) . "\n";

// Simulate normalizeClassName for XII TE
function normalizeClassName($name) {
    $name = strtoupper($name);
    $name = str_replace(['.','-','_'], ' ', $name);
    $name = preg_replace('/\s+/', ' ', $name);
    $name = str_replace(' TJKT', ' TKJ', $name);
    $name = str_replace(' TE', ' TAV', $name);
    return trim($name);
}

echo "\nnormalizeClassName tests:\n";
$tests = ['XII TE', 'XII TAV', 'XII TKR INDUSTRI', 'XII DPIB', 'XII ACP', 'XI TE', 'X TE', 'XII TSM 1', 'XII TSM 2', 'XII TKR 2', 'XII TKJ', 'X DPIB'];
foreach ($tests as $t) {
    echo "  '$t' => '" . normalizeClassName($t) . "'\n";
}

// Check schedule insert logic
$ts = DB::table('time_slots')
    ->where('school_id', 3)
    ->where('day_of_week', 'monday')
    ->where('is_teaching_slot', true)
    ->select('id', 'slot_name')
    ->get();
echo "\nMonday teaching slots:\n";
foreach ($ts as $s) {
    preg_match('/(\d+)/', $s->slot_name, $m);
    echo "  ID:{$s->id} Name:{$s->slot_name} JamKe:" . ($m[1] ?? '?') . "\n";
}
