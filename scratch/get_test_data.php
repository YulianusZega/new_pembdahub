<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ACTIVE STUDENTS WITH RFID ===\n";
$students = \App\Models\Student::whereNotNull('rfid_uid')->where('rfid_uid', '!=', '')->take(5)->get();
foreach ($students as $s) {
    echo "ID: {$s->id} | Name: {$s->full_name} | UID: {$s->rfid_uid} | Status: {$s->status}\n";
}

if ($students->isEmpty()) {
    echo "No students with RFID found. Finding any active student:\n";
    $any = \App\Models\Student::whereIn('status', \App\Models\StudentStatusHistory::ACTIVE_STATUSES ?? ['aktif'])->take(3)->get();
    foreach ($any as $s) {
        echo "ID: {$s->id} | Name: {$s->full_name} | Status: {$s->status} (RFID is empty, you can assign one for testing)\n";
    }
}

echo "\n=== ACTIVE EMPLOYEES WITH RFID ===\n";
$employees = \App\Models\Employee::whereNotNull('rfid_uid')->where('rfid_uid', '!=', '')->take(5)->get();
foreach ($employees as $e) {
    echo "ID: {$e->id} | Name: {$e->full_name} | UID: {$e->rfid_uid} | Active: {$e->is_active}\n";
}
if ($employees->isEmpty()) {
    echo "No employees with RFID found. Finding any active employee:\n";
    $any = \App\Models\Employee::where('is_active', true)->take(3)->get();
    foreach ($any as $e) {
        echo "ID: {$e->id} | Name: {$e->full_name} (RFID is empty)\n";
    }
}
