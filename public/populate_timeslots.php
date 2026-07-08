<?php
/**
 * Populate Time Slots - Isi jam pelajaran untuk semua sekolah
 * Upload ke public_html/pembdahub/public/
 * Akses: https://perguruanpembda.com/populate_timeslots.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

echo "<html><head><title>Populate Time Slots</title>";
echo "<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}.warn{color:#ffc107}h1{color:#bb86fc}h2{color:#03dac6}table{border-collapse:collapse;width:100%;margin:10px 0}td,th{padding:6px 12px;border:1px solid #333;text-align:left}th{background:#16213e}</style></head><body>";
echo "<h1>⏰ Populate Time Slots</h1>";

$laravelRoot = '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub';
require $laravelRoot . '/vendor/autoload.php';
$app = require_once $laravelRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Cek kondisi awal
$totalExisting = DB::table('time_slots')->count();
echo "<p class='info'>ℹ️ Time slots saat ini di database: <b>{$totalExisting}</b></p>";

// Cek apakah sudah ada dan tidak perlu isi ulang
if ($totalExisting > 0 && !isset($_GET['force'])) {
    echo "<p class='warn'>⚠️ Time slots sudah ada. Tambahkan <b>?force=1</b> ke URL untuk mengisi ulang.</p>";
    
    $bySchool = DB::table('time_slots')
        ->join('schools', 'time_slots.school_id', '=', 'schools.id')
        ->select('schools.name', 'schools.type', DB::raw('count(*) as total'))
        ->groupBy('schools.id', 'schools.name', 'schools.type')
        ->get();
    
    echo "<h2>Data yang Sudah Ada</h2><table>";
    echo "<tr><th>Sekolah</th><th>Tipe</th><th>Jumlah Slot</th></tr>";
    foreach ($bySchool as $row) {
        echo "<tr><td>{$row->name}</td><td>{$row->type}</td><td>{$row->total}</td></tr>";
    }
    echo "</table>";
    echo "<p><a href='?secret=pembda99&force=1' style='background:#ff9100;color:#000;padding:8px 16px;border-radius:6px;text-decoration:none;font-weight:bold'>⚠️ Hapus & Isi Ulang Semua Time Slots</a></p>";
    die("</body></html>");
}

// Ambil TP aktif
$activeYear = DB::table('academic_years')->where('is_active', 1)->first();
if (!$activeYear) {
    $activeYear = DB::table('academic_years')->orderBy('id', 'desc')->first();
}
echo "<p class='ok'>✅ Tahun Pelajaran: <b>{$activeYear->year}</b> (ID: {$activeYear->id})</p>";

// Ambil semua sekolah aktif (bukan yayasan)
$schools = DB::table('schools')
    ->where('is_active', 1)
    ->where('type', '!=', 'yayasan')
    ->get();

echo "<p class='info'>ℹ️ Jumlah sekolah aktif: <b>" . count($schools) . "</b></p>";

$days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
$totalCreated = 0;

foreach ($schools as $school) {
    $schoolType = strtoupper($school->type ?? '');
    
    // Hapus SEMUA slot lama untuk sekolah ini (termasuk yang academic_year_id = NULL)
    $deleted = DB::table('time_slots')
        ->where('school_id', $school->id)
        ->delete();
    
    echo "<h2>🏫 {$school->name} ({$schoolType})</h2>";
    if ($deleted > 0) echo "<p class='warn'>🗑️ $deleted slot lama dihapus.</p>";
    
    $slots = getSlots($schoolType, $school->id, $activeYear->id);
    
    $created = 0;
    foreach ($days as $day) {
        foreach ($slots as $slot) {
            $slot['day_of_week'] = $day;
            $slot['created_at'] = now();
            $slot['updated_at'] = now();
            DB::table('time_slots')->insert($slot);
            $created++;
        }
    }
    $totalCreated += $created;
    echo "<p class='ok'>✅ $created slot dibuat (5 hari × " . count($slots) . " slot/hari)</p>";
    
    // Tampilkan ringkasan slot
    echo "<table><tr><th>Slot</th><th>Tipe</th><th>Mulai</th><th>Selesai</th><th>Durasi</th></tr>";
    foreach ($slots as $s) {
        $typeColor = $s['slot_type'] === 'lesson' ? '#00e676' : ($s['slot_type'] === 'break' ? '#ffc107' : '#40c4ff');
        echo "<tr><td>{$s['slot_name']}</td><td style='color:{$typeColor}'>{$s['slot_type']}</td><td>{$s['start_time']}</td><td>{$s['end_time']}</td><td>{$s['duration_minutes']} mnt</td></tr>";
    }
    echo "</table>";
}

echo "<h2>📊 Ringkasan</h2>";
$grandTotal = DB::table('time_slots')->count();
echo "<p class='ok'>✅ Total slot dibuat: <b>{$totalCreated}</b></p>";
echo "<p class='ok'>✅ Total slot di database: <b>{$grandTotal}</b></p>";

echo "<hr><p class='warn'>⚠️ Hapus populate_timeslots.php setelah selesai!</p>";
echo "<p><a href='https://perguruanpembda.com/admin/schedules' target='_blank' style='color:#03dac6'>→ Cek halaman Jadwal Pelajaran</a></p>";
echo "</body></html>";

// =============================================
function getSlots(string $type, int $schoolId, int $yearId): array {
    $base = ['school_id' => $schoolId, 'academic_year_id' => $yearId, 'is_active' => true];
    
    if ($type === 'SMK') {
        return array_map(fn($s) => array_merge($base, $s), [
            ['slot_name'=>'Apel',        'slot_type'=>'ceremony','slot_order'=>1, 'start_time'=>'06:40','end_time'=>'07:00','duration_minutes'=>20, 'is_teaching_slot'=>false],
            ['slot_name'=>'5S',          'slot_type'=>'ceremony','slot_order'=>2, 'start_time'=>'07:00','end_time'=>'07:15','duration_minutes'=>15, 'is_teaching_slot'=>false],
            ['slot_name'=>'Les 1',       'slot_type'=>'lesson',  'slot_order'=>3, 'start_time'=>'07:15','end_time'=>'07:58','duration_minutes'=>43, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 2',       'slot_type'=>'lesson',  'slot_order'=>4, 'start_time'=>'07:58','end_time'=>'08:41','duration_minutes'=>43, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 3',       'slot_type'=>'lesson',  'slot_order'=>5, 'start_time'=>'08:41','end_time'=>'09:24','duration_minutes'=>43, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 4',       'slot_type'=>'lesson',  'slot_order'=>6, 'start_time'=>'09:24','end_time'=>'10:07','duration_minutes'=>43, 'is_teaching_slot'=>true],
            ['slot_name'=>'Istirahat 1', 'slot_type'=>'break',   'slot_order'=>7, 'start_time'=>'10:07','end_time'=>'10:27','duration_minutes'=>20, 'is_teaching_slot'=>false],
            ['slot_name'=>'Les 5',       'slot_type'=>'lesson',  'slot_order'=>8, 'start_time'=>'10:27','end_time'=>'11:10','duration_minutes'=>43, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 6',       'slot_type'=>'lesson',  'slot_order'=>9, 'start_time'=>'11:10','end_time'=>'11:53','duration_minutes'=>43, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 7',       'slot_type'=>'lesson',  'slot_order'=>10,'start_time'=>'11:53','end_time'=>'12:36','duration_minutes'=>43, 'is_teaching_slot'=>true],
            ['slot_name'=>'Istirahat 2', 'slot_type'=>'break',   'slot_order'=>11,'start_time'=>'12:36','end_time'=>'13:06','duration_minutes'=>30, 'is_teaching_slot'=>false],
            ['slot_name'=>'Les 8',       'slot_type'=>'lesson',  'slot_order'=>12,'start_time'=>'13:06','end_time'=>'13:48','duration_minutes'=>42, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 9',       'slot_type'=>'lesson',  'slot_order'=>13,'start_time'=>'13:48','end_time'=>'14:30','duration_minutes'=>42, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 10',      'slot_type'=>'lesson',  'slot_order'=>14,'start_time'=>'14:30','end_time'=>'15:10','duration_minutes'=>40, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 11',      'slot_type'=>'lesson',  'slot_order'=>15,'start_time'=>'15:10','end_time'=>'15:50','duration_minutes'=>40, 'is_teaching_slot'=>true],
        ]);
    }

    if ($type === 'SMA') {
        return array_map(fn($s) => array_merge($base, $s), [
            ['slot_name'=>'Tadarus/Upacara','slot_type'=>'ceremony','slot_order'=>1, 'start_time'=>'07:00','end_time'=>'07:15','duration_minutes'=>15, 'is_teaching_slot'=>false],
            ['slot_name'=>'Les 1',          'slot_type'=>'lesson',  'slot_order'=>2, 'start_time'=>'07:15','end_time'=>'08:00','duration_minutes'=>45, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 2',          'slot_type'=>'lesson',  'slot_order'=>3, 'start_time'=>'08:00','end_time'=>'08:45','duration_minutes'=>45, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 3',          'slot_type'=>'lesson',  'slot_order'=>4, 'start_time'=>'08:45','end_time'=>'09:30','duration_minutes'=>45, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 4',          'slot_type'=>'lesson',  'slot_order'=>5, 'start_time'=>'09:30','end_time'=>'10:15','duration_minutes'=>45, 'is_teaching_slot'=>true],
            ['slot_name'=>'Istirahat 1',    'slot_type'=>'break',   'slot_order'=>6, 'start_time'=>'10:15','end_time'=>'10:30','duration_minutes'=>15, 'is_teaching_slot'=>false],
            ['slot_name'=>'Les 5',          'slot_type'=>'lesson',  'slot_order'=>7, 'start_time'=>'10:30','end_time'=>'11:15','duration_minutes'=>45, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 6',          'slot_type'=>'lesson',  'slot_order'=>8, 'start_time'=>'11:15','end_time'=>'12:00','duration_minutes'=>45, 'is_teaching_slot'=>true],
            ['slot_name'=>'Istirahat 2',    'slot_type'=>'break',   'slot_order'=>9, 'start_time'=>'12:00','end_time'=>'12:30','duration_minutes'=>30, 'is_teaching_slot'=>false],
            ['slot_name'=>'Les 7',          'slot_type'=>'lesson',  'slot_order'=>10,'start_time'=>'12:30','end_time'=>'13:15','duration_minutes'=>45, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 8',          'slot_type'=>'lesson',  'slot_order'=>11,'start_time'=>'13:15','end_time'=>'14:00','duration_minutes'=>45, 'is_teaching_slot'=>true],
            ['slot_name'=>'Les 9',          'slot_type'=>'lesson',  'slot_order'=>12,'start_time'=>'14:00','end_time'=>'14:45','duration_minutes'=>45, 'is_teaching_slot'=>true],
        ]);
    }

    // SMP (default)
    return array_map(fn($s) => array_merge($base, $s), [
        ['slot_name'=>'Tadarus/Upacara','slot_type'=>'ceremony','slot_order'=>1, 'start_time'=>'07:00','end_time'=>'07:15','duration_minutes'=>15, 'is_teaching_slot'=>false],
        ['slot_name'=>'Les 1',         'slot_type'=>'lesson',  'slot_order'=>2, 'start_time'=>'07:15','end_time'=>'07:55','duration_minutes'=>40, 'is_teaching_slot'=>true],
        ['slot_name'=>'Les 2',         'slot_type'=>'lesson',  'slot_order'=>3, 'start_time'=>'07:55','end_time'=>'08:35','duration_minutes'=>40, 'is_teaching_slot'=>true],
        ['slot_name'=>'Les 3',         'slot_type'=>'lesson',  'slot_order'=>4, 'start_time'=>'08:35','end_time'=>'09:15','duration_minutes'=>40, 'is_teaching_slot'=>true],
        ['slot_name'=>'Les 4',         'slot_type'=>'lesson',  'slot_order'=>5, 'start_time'=>'09:15','end_time'=>'09:55','duration_minutes'=>40, 'is_teaching_slot'=>true],
        ['slot_name'=>'Istirahat 1',   'slot_type'=>'break',   'slot_order'=>6, 'start_time'=>'09:55','end_time'=>'10:15','duration_minutes'=>20, 'is_teaching_slot'=>false],
        ['slot_name'=>'Les 5',         'slot_type'=>'lesson',  'slot_order'=>7, 'start_time'=>'10:15','end_time'=>'10:55','duration_minutes'=>40, 'is_teaching_slot'=>true],
        ['slot_name'=>'Les 6',         'slot_type'=>'lesson',  'slot_order'=>8, 'start_time'=>'10:55','end_time'=>'11:35','duration_minutes'=>40, 'is_teaching_slot'=>true],
        ['slot_name'=>'Istirahat 2',   'slot_type'=>'break',   'slot_order'=>9, 'start_time'=>'11:35','end_time'=>'12:00','duration_minutes'=>25, 'is_teaching_slot'=>false],
        ['slot_name'=>'Les 7',         'slot_type'=>'lesson',  'slot_order'=>10,'start_time'=>'12:00','end_time'=>'12:40','duration_minutes'=>40, 'is_teaching_slot'=>true],
        ['slot_name'=>'Les 8',         'slot_type'=>'lesson',  'slot_order'=>11,'start_time'=>'12:40','end_time'=>'13:20','duration_minutes'=>40, 'is_teaching_slot'=>true],
    ]);
}

function now(): string { return date('Y-m-d H:i:s'); }
