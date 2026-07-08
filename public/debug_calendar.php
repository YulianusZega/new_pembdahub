<?php
/**
 * Debug script kalender CRUD
 * Akses: http://localhost/pembdahub/public/debug_calendar.php?token=pembda2026
 */

$token = $_GET['token'] ?? '';
if ($token !== 'pembda2026') {
    die('Unauthorized');
}

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Manual boot agar bisa pakai Eloquent
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

echo "<pre style='font-family:monospace;background:#1e1e1e;color:#d4d4d4;padding:20px'>";
echo "=== DEBUG KALENDER CRUD ===\n\n";

// 1. Cek APP_URL
echo "1. APP_URL: " . config('app.url') . "\n";
echo "   url('admin/calendar'): " . url('admin/calendar') . "\n\n";

// 2. Cek event id=3 dan id=4
echo "2. Data EducationalCalendar id=3 dan id=4:\n";
try {
    $events = DB::table('educational_calendars')
        ->whereIn('id', [3, 4])
        ->get(['id', 'title', 'level', 'school_id', 'type', 'is_holiday', 'start_date', 'end_date']);
    
    foreach ($events as $ev) {
        echo "   ID={$ev->id} | level={$ev->level} | school_id={$ev->school_id} (type=" . gettype($ev->school_id) . ")\n";
        echo "   title={$ev->title} | type={$ev->type}\n";
        echo "   is_holiday={$ev->is_holiday} | start={$ev->start_date} | end={$ev->end_date}\n\n";
    }
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
}

// 3. Cek semua user admin_sekolah dan school_id mereka
echo "3. User dengan role admin_sekolah / kepala_sekolah:\n";
try {
    $users = DB::table('users')
        ->whereIn('role', ['admin_sekolah', 'kepala_sekolah', 'superadmin'])
        ->where('is_active', true)
        ->get(['id', 'name', 'role', 'school_id']);
    
    foreach ($users as $u) {
        $sid = is_null($u->school_id) ? 'NULL' : $u->school_id . " (type=" . gettype($u->school_id) . ")";
        echo "   UserID={$u->id} | {$u->name} | role={$u->role} | school_id={$sid}\n";
        
        // Simulasi pengecekan controller update untuk event id=3
        foreach ([3,4] as $eventId) {
            $ev = DB::table('educational_calendars')->where('id', $eventId)->first();
            if ($ev) {
                $levelOk = ($ev->level === 'school');
                $schoolOk = ($ev->school_id === $u->school_id);
                $schoolStrictOk = ($ev->school_id !== $u->school_id); // cek kondisi abort
                echo "      → Cek Event#{$eventId}: level='school' ✓={$levelOk} | school_id match ({$ev->school_id}==={$u->school_id}): " . ($schoolOk ? "✓ MATCH" : "✗ TIDAK MATCH → AKAN ABORT 403") . "\n";
                if (!$levelOk || !$schoolOk) {
                    echo "        *** CONTROLLER AKAN MENJALANKAN abort(403)! ***\n";
                }
            }
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
}

// 4. Cek School id=4
echo "4. Data School id=4:\n";
try {
    $school = DB::table('schools')->where('id', 4)->first();
    if ($school) {
        echo "   ID={$school->id} | name={$school->name} | type={$school->type}\n";
    } else {
        echo "   School id=4 tidak ditemukan!\n";
    }
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
}

// 5. Test url() helper untuk form action
echo "\n5. URL yang akan dihasilkan url() helper:\n";
echo "   url('admin/calendar'): " . url('admin/calendar') . "\n";
echo "   route('admin.calendar.update', 3): ";
try {
    echo route('admin.calendar.update', 3) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";
echo "</pre>";
