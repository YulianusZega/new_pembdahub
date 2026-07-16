<?php
/**
 * Diagnostic and Cleanup Tool for Wali Kelas & Classroom Assignments
 * Access via: https://perguruanpembda.com/fix_classroom_assignment.php?secret=pembda99
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if ($request->query('secret') !== 'pembda99') {
    die('Unauthorized access.');
}

use App\Models\Employee;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\Position;
use Illuminate\Support\Facades\DB;

$currentYear = AcademicYear::where('is_active', 1)->first();
$yearId = $currentYear ? $currentYear->id : 0;
$yearName = $currentYear ? $currentYear->year : 'Unknown';

$action = $request->query('action', 'show');
$targetEmployeeId = $request->query('employee_id');

echo "<!DOCTYPE html><html><head><title>Wali Kelas Diagnostic & Fix Tool</title>";
echo "<style>
body { font-family: sans-serif; margin: 20px; line-height: 1.6; background: #f8fafc; color: #334155; }
.card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
th { background: #f1f5f9; }
.btn { display: inline-block; padding: 10px 18px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; }
.btn-danger { background: #ef4444; }
.btn-success { background: #10b981; }
.badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
.badge-active { background: #d1fae5; color: #065f46; }
.badge-closed { background: #fee2e2; color: #991b1b; }
</style></head><body>";

echo "<h1>🛠️ Diagnostik & Pembersihan Data Wali Kelas</h1>";
echo "<div class='card'><b>Tahun Pelajaran Aktif:</b> {$yearName} (ID: {$yearId})</div>";

if ($action === 'fix_markus' || $action === 'clean_all') {
    DB::beginTransaction();
    try {
        echo "<div class='card' style='background: #ecfdf5; border-left: 4px solid #10b981;'>";
        echo "<h3>Menjalankan Pembersihan...</h3><ul>";

        // Find all Wali Kelas position IDs
        $waliKelasPosIds = Position::whereRaw('LOWER(position_name) LIKE ?', ['%wali kelas%'])->pluck('id')->toArray();
        if (empty($waliKelasPosIds)) {
            throw new Exception("Jabatan Wali Kelas tidak ditemukan di tabel positions.");
        }

        $employeesQuery = Employee::query();
        if ($action === 'fix_markus') {
            $employeesQuery->where('full_name', 'LIKE', '%Markus Zebua%');
        }
        $employees = $employeesQuery->get();

        foreach ($employees as $emp) {
            $activePivotRows = DB::table('employee_positions')
                ->where('employee_id', $emp->id)
                ->whereIn('position_id', $waliKelasPosIds)
                ->where('academic_year_id', $yearId)
                ->whereNull('end_date')
                ->orderBy('updated_at', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            if ($activePivotRows->count() > 1) {
                $keptRow = $activePivotRows->first();
                $closedCount = $activePivotRows->count() - 1;
                echo "<li>Ditemukan {$activePivotRows->count()} record Wali Kelas aktif untuk <b>{$emp->full_name}</b> di TP {$yearName}. Mempertahankan record terbaru (ID: {$keptRow->id}, Kelas ID: {$keptRow->classroom_id}) dan menutup {$closedCount} record lama...</li>";

                DB::table('employee_positions')
                    ->where('employee_id', $emp->id)
                    ->whereIn('position_id', $waliKelasPosIds)
                    ->where('academic_year_id', $yearId)
                    ->whereNull('end_date')
                    ->where('id', '!=', $keptRow->id)
                    ->update(['end_date' => now(), 'updated_at' => now()]);
            }

            // Also check classrooms table homeroom_teacher_id synchronization
            if ($emp->teacher) {
                // Check if classrooms table already has a class assigned to this teacher right now
                $masterClassroom = Classroom::where('homeroom_teacher_id', $emp->teacher->id)
                    ->where('academic_year_id', $yearId)
                    ->orderBy('id', 'desc')
                    ->first();

                $latestPivotRow = DB::table('employee_positions')
                    ->where('employee_id', $emp->id)
                    ->whereIn('position_id', $waliKelasPosIds)
                    ->where('academic_year_id', $yearId)
                    ->whereNull('end_date')
                    ->orderBy('updated_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($masterClassroom && $latestPivotRow && $latestPivotRow->classroom_id != $masterClassroom->id) {
                    // Sync pivot row to match the master classroom that was just assigned
                    DB::table('employee_positions')
                        ->where('id', $latestPivotRow->id)
                        ->update(['classroom_id' => $masterClassroom->id, 'updated_at' => now()]);
                    echo "<li>✔ Memperbarui kelas pada riwayat jabatan (Pivot ID: {$latestPivotRow->id}) menjadi <b>{$masterClassroom->class_name}</b> (ID: {$masterClassroom->id}) sesuai tabel master kelas.</li>";
                } elseif ($latestPivotRow && $latestPivotRow->classroom_id && !$masterClassroom) {
                    // Clear from any other classroom in active year
                    DB::table('classrooms')
                        ->where('homeroom_teacher_id', $emp->teacher->id)
                        ->where('academic_year_id', $yearId)
                        ->where('id', '!=', $latestPivotRow->classroom_id)
                        ->update(['homeroom_teacher_id' => null]);

                    // Assign to latestClassroomId
                    DB::table('classrooms')
                        ->where('id', $latestPivotRow->classroom_id)
                        ->update(['homeroom_teacher_id' => $emp->teacher->id]);

                    $cls = Classroom::find($latestPivotRow->classroom_id);
                    echo "<li>✔ Sinkronisasi kelas di tabel classrooms untuk <b>{$emp->full_name}</b> -> <b>" . ($cls ? $cls->class_name : 'Unknown') . "</b></li>";
                }
            }
        }

        DB::commit();
        echo "</ul><b>Selesai! Data berhasil disinkronkan.</b> <a href='?secret=pembda99' class='btn btn-success' style='margin-left:10px;'>Kembali ke Diagnostik</a></div>";
    } catch (Exception $e) {
        DB::rollBack();
        echo "<div class='card' style='background: #fee2e2; border-left: 4px solid #ef4444;'><b>Error:</b> " . $e->getMessage() . "</div>";
    }
}

// Display Current State for Markus Zebua
$markus = Employee::where('full_name', 'LIKE', '%Markus Zebua%')->first();
if ($markus) {
    echo "<div class='card'>";
    echo "<h2>📋 Diagnostik Khusus: {$markus->full_name} (ID: {$markus->id})</h2>";
    echo "<p><a href='?action=fix_markus&secret=pembda99' class='btn btn-danger'>⚡ Bersihkan & Sinkronkan Data Markus Zebua di TP {$yearName}</a></p>";
    
    echo "<h3>1. Semua Riwayat Penugasan di Tabel employee_positions (Khusus TP {$yearName}):</h3>";
    $pivots = DB::table('employee_positions as ep')
        ->join('positions as p', 'ep.position_id', '=', 'p.id')
        ->leftJoin('classrooms as c', 'ep.classroom_id', '=', 'c.id')
        ->where('ep.employee_id', $markus->id)
        ->where('ep.academic_year_id', $yearId)
        ->select('ep.*', 'p.position_name', 'c.class_name')
        ->orderBy('ep.id', 'desc')
        ->get();

    echo "<table><tr><th>Pivot ID</th><th>Jabatan</th><th>Kelas (classroom_id)</th><th>TMT Mulai</th><th>TMT Selesai</th><th>Status</th><th>Updated At</th></tr>";
    if ($pivots->isEmpty()) {
        echo "<tr><td colspan='7'>Belum ada penugasan di TP ini.</td></tr>";
    } else {
        foreach ($pivots as $p) {
            $status = is_null($p->end_date) ? "<span class='badge badge-active'>AKTIF</span>" : "<span class='badge badge-closed'>DITUTUP ({$p->end_date})</span>";
            $className = $p->class_name ? "<b>{$p->class_name}</b> (ID: {$p->classroom_id})" : ($p->classroom_id ? "ID: {$p->classroom_id}" : "-");
            echo "<tr><td>{$p->id}</td><td>{$p->position_name}</td><td>{$className}</td><td>{$p->start_date}</td><td>" . ($p->end_date ?: '-') . "</td><td>{$status}</td><td>{$p->updated_at}</td></tr>";
        }
    }
    echo "</table>";

    echo "<h3>2. Data Kelas di Tabel classrooms Yang Menunjuk ke Markus Zebua (di TP {$yearName}):</h3>";
    if ($markus->teacher) {
        $classes = Classroom::where('homeroom_teacher_id', $markus->teacher->id)
            ->where('academic_year_id', $yearId)
            ->get();
        echo "<table><tr><th>Class ID</th><th>Kode Kelas</th><th>Nama Kelas</th><th>Tingkat</th><th>Status</th></tr>";
        if ($classes->isEmpty()) {
            echo "<tr><td colspan='5'>Tidak ada kelas di TP ini yang menunjuk ke Markus Zebua sebagai Wali Kelas.</td></tr>";
        } else {
            foreach ($classes as $cl) {
                echo "<tr><td>{$cl->id}</td><td>{$cl->class_code}</td><td><b>{$cl->class_name}</b></td><td>{$cl->grade_level}</td><td>Aktif</td></tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<p style='color:red;'>Markus Zebua belum memiliki record di tabel teachers.</p>";
    }
    echo "</div>";
}

echo "</body></html>";
