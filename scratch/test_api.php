<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\EmployeeAttendance;
use Illuminate\Http\Request;

echo "=== PRE-TEST CLEANUP ===\n";
$today = date('Y-m-d');
$student = Student::whereNotNull('nis')->where('status', 'aktif')->first();
$teacher = Teacher::whereNotNull('teacher_code')->first();

if ($student) {
    echo "Found student NIS: {$student->nis}\n";
    // Delete today's attendance for clean test
    Attendance::where('student_id', $student->id)->where('date', $today)->delete();
} else {
    echo "No active student with NIS found!\n";
}

if ($teacher) {
    echo "Found teacher code: {$teacher->teacher_code}\n";
    if ($teacher->employee) {
        EmployeeAttendance::where('employee_id', $teacher->employee_id)->where('date', $today)->delete();
    }
} else {
    echo "No teacher with code found!\n";
}

echo "\n=== TEST 1: Student QR Code Scan ===\n";
if ($student) {
    $request = Request::create('/api/attendance/rfid-scan', 'POST', [
        'uid' => $student->nis,
        'type' => 'qr',
        'device_id' => 'TEST-KIOSK-QR'
    ]);
    $request->headers->set('X-Kiosk-API-Key', 'RAHASIA-PEMBDAHUB-12345');
    $request->headers->set('Accept', 'application/json');

    $response = app()->handle($request);
    echo "Response: " . $response->getContent() . "\n";
    
    // Verify DB
    $att = Attendance::where('student_id', $student->id)->where('date', $today)->first();
    if ($att) {
        echo "SUCCESS: Attendance record found in DB!\n";
        echo "Recorded via: {$att->recorded_via} (Expected: qr_gps)\n";
    } else {
        echo "FAILED: Attendance record not found in DB!\n";
    }
}

echo "\n=== TEST 2: Teacher QR Code Scan ===\n";
if ($teacher && $teacher->employee) {
    $request = Request::create('/api/attendance/rfid-scan', 'POST', [
        'uid' => $teacher->employee->employee_code ?? $teacher->teacher_code,
        'type' => 'qr',
        'device_id' => 'TEST-KIOSK-QR'
    ]);
    $request->headers->set('X-Kiosk-API-Key', 'RAHASIA-PEMBDAHUB-12345');
    $request->headers->set('Accept', 'application/json');

    $response = app()->handle($request);
    echo "Response: " . $response->getContent() . "\n";
    
    // Verify DB
    $att = EmployeeAttendance::where('employee_id', $teacher->employee_id)->where('date', $today)->first();
    if ($att) {
        echo "SUCCESS: Employee Attendance record found in DB!\n";
        echo "Recorded via: {$att->recorded_via} (Expected: rfid)\n";
    } else {
        echo "FAILED: Employee Attendance record not found in DB!\n";
    }
}
