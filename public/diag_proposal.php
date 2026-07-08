<?php
// Script diagnostik KHUSUS untuk masalah dropdown guru di modal verifikasi proposal
// Akses: https://perguruanpembda.com/diag_proposal.php?secret=pembda99

if (($_GET['secret'] ?? '') !== 'pembda99') {
    http_response_code(403);
    die('Forbidden');
}

// Path SAMA PERSIS dengan clear-cache.php yang sudah terbukti bekerja
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

echo "==============================================================\n";
echo "  DIAGNOSTIK: Dropdown Guru di Modal Proposals\n";
echo "  Waktu: " . now() . "\n";
echo "  base_path(): " . base_path() . "\n";
echo "  public_path(): " . public_path() . "\n";
echo "  __DIR__: " . __DIR__ . "\n";
echo "==============================================================\n\n";

// ─── 1. DATA SCHOOLS ──────────────────────────────────────────
echo "=== [1] DATA SCHOOLS ===\n";
$schools = App\Models\School::orderBy('id')->get();
foreach ($schools as $s) {
    echo "  ID={$s->id} | type=[{$s->type}] | name={$s->name}\n";
}
echo "\n";

// ─── 2. DATA TEACHERS (SMA/SMK saja) ─────────────────────────
echo "=== [2] DATA TEACHERS YANG MASUK SEBAGAI allTeachers (SMA+SMK) ===\n";
$smaSmkSchoolIds = App\Models\School::whereIn('type', ['SMA', 'SMK'])->pluck('id');
echo "school_id SMA/SMK yang ada: " . $smaSmkSchoolIds->implode(', ') . "\n";

$teachers = App\Models\Teacher::with('school')
    ->whereIn('school_id', $smaSmkSchoolIds)
    ->orderBy('school_id')
    ->get();
echo "Total guru SMA+SMK: " . $teachers->count() . "\n";
foreach ($teachers as $t) {
    $rawSchoolId = $t->getRawOriginal('school_id');
    $schoolType  = $t->school ? $t->school->type : 'NULL';
    echo "  Teacher.id={$t->id}"
        . " | school_id RAW=[{$rawSchoolId}] PHP type=" . gettype($rawSchoolId)
        . " | (int)school_id=[" . (int)$rawSchoolId . "]"
        . " | school->type=[{$schoolType}]"
        . " | name={$t->full_name}\n";
}
echo "\n";

// ─── 3. DATA FINAL PROJECTS (semua yang 'pending') ────────────
echo "=== [3] PROPOSAL BERSTATUS 'pending' ===\n";
$pendingProjects = App\Models\FinalProject::with(['student', 'student.school'])
    ->where('status', 'pending')
    ->get();
echo "Total pending: " . $pendingProjects->count() . "\n\n";

if ($pendingProjects->isEmpty()) {
    echo "  Tidak ada proposal pending.\n\n";
}

foreach ($pendingProjects as $p) {
    echo "--- Project ID={$p->id} ---\n";
    $rawStudentId = $p->getRawOriginal('student_id');
    echo "  Kolom student_id di DB (raw): [{$rawStudentId}] PHP type=" . gettype($rawStudentId) . "\n";
    echo "  Relasi \$p->student: " . ($p->student ? "LOADED (id={$p->student->id})" : "*** NULL ***") . "\n";

    if ($p->student) {
        $rawSchoolId    = $p->student->getRawOriginal('school_id');
        $castedSchoolId = $p->student->school_id;
        echo "  student->full_name: {$p->student->full_name}\n";
        echo "  student->school_id RAW di DB: [{$rawSchoolId}] PHP type=" . gettype($rawSchoolId) . "\n";
        echo "  student->school_id setelah Eloquent cast: [{$castedSchoolId}] PHP type=" . gettype($castedSchoolId) . "\n";
        echo "  Relasi student->school: " . ($p->student->school
            ? "LOADED (id={$p->student->school->id}, type={$p->student->school->type})"
            : "*** NULL ***") . "\n";

        // Nilai yang dikirim ke openAssignModal()
        $schoolIdForJS = $p->student->school_id ?? 0;
        echo "  >> Nilai schoolId dikirim ke openAssignModal(): [{$schoolIdForJS}]\n";

        // Filter guru yang akan muncul di dropdown
        $matchedTeachers = $teachers->filter(fn($t) => (int)$t->school_id === (int)$schoolIdForJS);
        echo "  >> Guru yang COCOK (school_id === {$schoolIdForJS}): " . $matchedTeachers->count() . " guru\n";

        if ($matchedTeachers->isEmpty()) {
            echo "  >> *** MASALAH DITEMUKAN: DROPDOWN AKAN KOSONG! ***\n";
            echo "  >> Daftar school_id dari allTeachers:\n";
            foreach ($teachers as $t) {
                echo "     Teacher.id={$t->id}, school_id={$t->school_id}"
                    . " === {$schoolIdForJS}? "
                    . ((int)$t->school_id === (int)$schoolIdForJS ? "YA" : "tidak") . "\n";
            }
        } else {
            echo "  >> Guru yang akan tampil:\n";
            foreach ($matchedTeachers as $mt) {
                echo "     - {$mt->full_name} (Teacher.id={$mt->id}, school_id={$mt->school_id})\n";
            }
        }
    }
    echo "\n";
}

// ─── 4. SIMULASI JSON allTeachers seperti yang di-render blade ─
echo "=== [4] JSON allTeachers (persis seperti di dalam JavaScript halaman) ===\n";
$jsArray = $teachers->map(fn($t) => [
    'id'          => $t->id,
    'full_name'   => $t->full_name,
    'school_id'   => (int)$t->school_id,
    'school_type' => $t->school?->type,
]);
echo json_encode($jsArray->values(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// ─── 5. CEK SEMUA FINAL PROJECTS (tidak hanya pending) ────────
echo "=== [5] SEMUA FINAL PROJECTS (semua status) ===\n";
$allProjects = App\Models\FinalProject::with(['student.school'])->get();
echo "Total semua final_projects: " . $allProjects->count() . "\n";
foreach ($allProjects as $p) {
    $rawSid = $p->getRawOriginal('student_id');
    $sName  = $p->student ? $p->student->full_name : 'NULL';
    $sSid   = $p->student ? $p->student->school_id : 'NULL';
    $sType  = ($p->student && $p->student->school) ? $p->student->school->type : 'NULL';
    echo "  ID={$p->id} | status={$p->status} | student_id_raw={$rawSid} | student={$sName} | student.school_id={$sSid} | school_type={$sType}\n";
}
echo "\n";

echo "==============================================================\n";
echo "  SELESAI\n";
echo "==============================================================\n";
