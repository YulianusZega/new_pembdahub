<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$secret = $_GET['secret'] ?? '';
if ($secret !== 'pembda99') {
    die("Unauthorized");
}

$results = Illuminate\Support\Facades\DB::select("
    SELECT s.day_of_week, ts.slot_order, ts.slot_name, sub.name as mapel_name, t.full_name as guru_name
    FROM schedules s
    JOIN time_slots ts ON s.time_slot_id = ts.id
    LEFT JOIN teaching_assignments ta ON s.teaching_assignment_id = ta.id
    LEFT JOIN subjects sub ON ta.subject_id = sub.id
    LEFT JOIN teachers t ON ta.teacher_id = t.id
    WHERE s.classroom_id IN (SELECT id FROM classrooms WHERE class_name LIKE '%X DPIB%')
    ORDER BY s.day_of_week, ts.slot_order
");

echo "<pre>";
echo "Jadwal Kelas X DPIB (SELURUH HARI) - RAW SQL\n";
echo "============================\n";
foreach ($results as $r) {
    echo 'Hari: ' . $r->day_of_week . ' | Slot: ' . $r->slot_order . ' (' . $r->slot_name . ') | Mapel: ' . ($r->mapel_name ?: 'N/A') . ' | Guru: ' . ($r->guru_name ?: 'N/A') . "\n";
}
echo "</pre>";
