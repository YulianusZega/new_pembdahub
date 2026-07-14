<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// ==== ROUTING ABSENSI KIOSK (RFID) ====
// Tidak dijaga auth sanctum, melainkan pakai Manual Secret Key di controllernya.
Route::post('/attendance/rfid-scan', [\App\Http\Controllers\Api\AttendanceController::class, 'handleRfidScan']);

// ==== ROUTING ABSENSI PEGAWAI KIOSK (RFID) ====
Route::post('/attendance/employee-rfid-scan', [\App\Http\Controllers\Api\EmployeeAttendanceController::class, 'handleRfidScan']);

// ==== SCAN BUFFER (untuk registrasi kartu RFID via browser) ====
Route::post('/rfid/scan-buffer', function (Request $request) {
    $apiKey = $request->header('X-Kiosk-API-Key') ?? $request->input('api_key');
    if ($apiKey !== config('services.kiosk.api_key', 'RAHASIA-PEMBDAHUB-12345')) {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
    }
    $uid = strtoupper(trim($request->input('uid', '')));
    if (empty($uid)) {
        return response()->json(['status' => 'error', 'message' => 'UID kosong'], 400);
    }
    $bufferFile = storage_path('app/rfid_scan_buffer.json');
    file_put_contents($bufferFile, json_encode(['uid' => $uid, 'time' => time()]));
    return response()->json(['status' => 'success', 'uid' => $uid]);
});

// ==== CEK KEPEMILIKAN UID (untuk validasi di modal RFID sebelum simpan) ====
Route::get('/rfid/check-uid', function (Request $request) {
    $uid = strtoupper(trim($request->input('uid', '')));
    if (empty($uid)) {
        return response()->json(['owned' => false]);
    }

    // Cek di Students
    $student = \App\Models\Student::where('rfid_uid', $uid)->first();
    if ($student) {
        return response()->json([
            'owned' => true,
            'owner_name' => $student->full_name,
            'owner_type' => 'Siswa',
            'owner_id' => $student->id,
        ]);
    }

    // Cek di Employees (Guru/Pegawai)
    $employee = \App\Models\Employee::where('rfid_uid', $uid)->first();
    if ($employee) {
        $type = $employee->isTeacher() ? 'Guru' : 'Pegawai';
        return response()->json([
            'owned' => true,
            'owner_name' => $employee->full_name,
            'owner_type' => $type,
            'owner_id' => $employee->id,
        ]);
    }

    // Cek di TefaEmployees
    $tefaEmployee = \App\Models\TefaEmployee::where('rfid_uid', $uid)->first();
    if ($tefaEmployee) {
        return response()->json([
            'owned' => true,
            'owner_name' => $tefaEmployee->name,
            'owner_type' => 'Karyawan TEFA',
            'owner_id' => $tefaEmployee->id,
        ]);
    }

    return response()->json(['owned' => false]);
});

Route::get('/rfid/scan-buffer', function (Request $request) {
    $bufferFile = storage_path('app/rfid_scan_buffer.json');
    if (!file_exists($bufferFile)) {
        return response()->json(['uid' => null]);
    }
    $data = json_decode(file_get_contents($bufferFile), true);
    // Hapus file setelah dibaca (one-time read)
    @unlink($bufferFile);
    // Abaikan jika data lebih dari 60 detik
    if (isset($data['time']) && (time() - $data['time']) > 60) {
        return response()->json(['uid' => null]);
    }
    return response()->json(['uid' => $data['uid'] ?? null]);
});
