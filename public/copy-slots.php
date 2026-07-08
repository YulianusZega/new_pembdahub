<?php
/**
 * Script ini digunakan untuk menyalin Time Slot antar hari secara langsung di server production.
 * Jalankan script ini melalui browser dengan mengakses:
 * https://perguruanpembda.com/copy-slots.php?secret=pembda99&school_id=3&from_day=Kamis&to_day=Sabtu
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

use App\Models\TimeSlot;

// Verifikasi Secret
if ($request->query('secret') !== 'pembda99') {
    http_response_code(403);
    die("<h2 style='color:red;'>Akses Ditolak. Gunakan secret key yang benar.</h2>");
}

$schoolId = $request->query('school_id', 3);
$fromDayRaw = strtolower($request->query('from_day', 'thursday'));
$toDayRaw = strtolower($request->query('to_day', 'saturday'));

// Map indonesian days to english if they used indonesian
$dayMap = [
    'senin' => 'monday', 'selasa' => 'tuesday', 'rabu' => 'wednesday',
    'kamis' => 'thursday', 'jumat' => 'friday', 'sabtu' => 'saturday', 'minggu' => 'sunday'
];

$fromDay = $dayMap[$fromDayRaw] ?? $fromDayRaw;
$toDay = $dayMap[$toDayRaw] ?? $toDayRaw;

try {
    $sourceSlots = TimeSlot::where('school_id', $schoolId)
                           ->where('day_of_week', $fromDay)
                           ->get();

    if ($sourceSlots->isEmpty()) {
        die("<h3>Tidak ditemukan Time Slot pada hari <strong>$fromDay</strong> untuk school_id <strong>$schoolId</strong>.</h3>");
    }

    // Hapus data hari tujuan yang ada sebelumnya agar tidak ganda
    $deleted = TimeSlot::where('school_id', $schoolId)->where('day_of_week', $toDay)->delete();

    $count = 0;
    foreach ($sourceSlots as $slot) {
        $newSlot = $slot->replicate();
        $newSlot->day_of_week = $toDay;
        $newSlot->save();
        $count++;
    }

    // Clear schedule grid cache for this school
    cache()->forget("timeslots_school_{$schoolId}");

    echo "<h2 style='color:green;'>✅ Berhasil!</h2>";
    echo "<h3>Telah menyalin <strong>$count</strong> Time Slot dari hari <strong>$fromDay</strong> ke hari <strong>$toDay</strong> (Menimpa $deleted slot lama) untuk Unit Sekolah ID $schoolId.</h3>";
    echo "<p><a href='/admin/schedules/grid?academic_year_id=5&semester=ganjil&school_id=$schoolId'>Kembali ke Jadwal</a></p>";

} catch (\Exception $e) {
    echo "<h3 style='color:red;'>Terjadi Kesalahan: " . $e->getMessage() . "</h3>";
}
