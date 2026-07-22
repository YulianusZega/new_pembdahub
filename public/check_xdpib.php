<?php
$env = parse_ini_file(__DIR__ . '/../.env');
$pdo = new PDO("mysql:host=" . $env['DB_HOST'] . ";dbname=" . $env['DB_DATABASE'], $env['DB_USERNAME'], $env['DB_PASSWORD']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query("
    SELECT s.id, s.day_of_week, ts.slot_order, ts.slot_name, sub.name as mapel_name, t.full_name as guru_name
    FROM schedules s
    JOIN time_slots ts ON s.time_slot_id = ts.id
    LEFT JOIN teaching_assignments ta ON s.teaching_assignment_id = ta.id
    LEFT JOIN subjects sub ON ta.subject_id = sub.id
    LEFT JOIN teachers t ON ta.teacher_id = t.id
    WHERE s.classroom_id IN (SELECT id FROM classrooms WHERE class_name LIKE '%X DPIB%')
    ORDER BY s.day_of_week, ts.slot_order
");

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>Jadwal X DPIB di DB:\n";
foreach ($results as $r) {
    echo "ID: {$r['id']} | Hari: {$r['day_of_week']} | Slot: {$r['slot_order']} ({$r['slot_name']}) | Mapel: {$r['mapel_name']} | Guru: {$r['guru_name']}\n";
}
echo "</pre>";


