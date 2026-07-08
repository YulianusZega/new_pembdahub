<?php
/**
 * Emergency Controller Patcher - Teaching Assignment Controller Fix
 * Upload ke public_html/, akses sekali, lalu HAPUS.
 * Akses: https://perguruanpembda.com/patch_controller.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

echo "<html><head><title>Patch Controller Fix</title>";
echo "<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}.warn{color:#ffc107}h1{color:#bb86fc}h2{color:#03dac6}pre{background:#16213e;padding:10px;border-radius:8px;font-size:12px}</style></head><body>";
echo "<h1>🔧 Emergency Patch: TeachingAssignmentController</h1>";

$base = __DIR__ . '/../';
$controllerPath = $base . 'app/Http/Controllers/Admin/TeachingAssignmentController.php';

if (!file_exists($controllerPath)) {
    echo "<p class='err'>❌ Controller tidak ditemukan di: $controllerPath</p>";
    die("</body></html>");
}

$content = file_get_contents($controllerPath);

echo "<p class='info'>ℹ️ Ukuran file controller: " . number_format(strlen($content)) . " bytes</p>";

// Cek versi yang ter-deploy
if (strpos($content, 'Prioritas: (1) teacher') !== false) {
    echo "<p class='ok'>✅ Controller sudah versi terbaru! Tidak perlu patch.</p>";
    echo "<p class='info'>Jalankan clear-cache.php jika halaman masih bermasalah.</p>";
    die("</body></html>");
}

// Cek apakah versi lama yang bisa dipatch
if (strpos($content, 'public function create(Request $request)') === false) {
    echo "<p class='err'>❌ Method create() tidak ditemukan. File mungkin berbeda.</p>";
    die("</body></html>");
}

echo "<p class='info'>ℹ️ Controller terdeteksi versi lama. Menerapkan patch create() method...</p>";

// =============================================
// TARGET: Ganti seluruh method create()
// =============================================
$oldMethod = 'public function create(Request $request)
    {
        $user = auth()->user();

        $academicYears = AcademicYear::orderBy(\'start_date\', \'desc\')->get();
        $currentYear = AcademicYear::where(\'is_active\', 1)->first();
        $activeSemester = Semester::where(\'is_active\', true)->first();

        // Filter semesters by selected or current academic year
        $selectedAcademicYearId = $request->filled(\'academic_year_id\')
            ? $request->academic_year_id
            : ($currentYear ? $currentYear->id : null);
        $semesters = $selectedAcademicYearId
            ? Semester::where(\'academic_year_id\', $selectedAcademicYearId)->orderBy(\'semester_number\')->get()
            : Semester::orderBy(\'id\')->get();

        $teacherId = $request->teacher_id;
        $selectedSchoolId = $request->school_id;

        // Get schools for filter
        $schools = $user->isSuperAdmin()
            ? School::where(\'is_active\', 1)->schoolsOnly()->orderBy(\'name\')->get()
            : School::where(\'id\', $user->school_id)->get();

        // Get teachers (filtered by school if selected)
        $teacherQuery = Teacher::where(\'is_active\', 1)
            ->with([\'school\', \'employee\'])
            ->orderBy(\'full_name\');

        if (!$user->isSuperAdmin()) {
            $teacherQuery->where(\'school_id\', $user->school_id);
        } elseif ($selectedSchoolId) {
            $teacherQuery->where(\'school_id\', $selectedSchoolId);
        }

        $teachers = $teacherQuery->get();

        $selectedTeacher = null;
        $classrooms = collect([]);
        $subjects = collect([]);
        $currentAssignments = collect([]);

        if ($teacherId) {
            $selectedTeacher = Teacher::with(\'school\')->find($teacherId);
            if ($selectedTeacher) {
                $classrooms = Classroom::where(\'school_id\', $selectedTeacher->school_id)
                    ->where(\'academic_year_id\', $selectedAcademicYearId)
                    ->where(\'is_active\', 1)
                    ->orderBy(\'grade_level\')
                    ->orderBy(\'class_name\')
                    ->get();

                // Use competent subjects if available, fallback to all school subjects
                $competentSubjectIds = $selectedTeacher->competentSubjects()->pluck(\'subjects.id\');
                if ($competentSubjectIds->isNotEmpty()) {
                    $subjects = Subject::whereIn(\'id\', $competentSubjectIds)
                        ->where(\'is_active\', 1)
                        ->orderBy(\'subject_name\')
                        ->get();
                } else {
                    $subjects = Subject::where(\'school_id\', $selectedTeacher->school_id)
                        ->where(\'is_active\', 1)
                        ->orderBy(\'subject_name\')
                        ->get();
                }

                // Existing teaching assignments
                if ($currentYear) {
                    $currentAssignments = TeachingAssignment::where(\'teacher_id\', $teacherId)
                        ->where(\'academic_year_id\', $currentYear->id)
                        ->when($activeSemester, fn ($q) => $q->where(\'semester_id\', $activeSemester->id))
                        ->with([\'classroom\', \'subject\', \'semester\'])
                        ->get();
                }
            }
        }

        return view(\'admin.assignments.teaching.create\', compact(
            \'teachers\',
            \'schools\',
            \'classrooms\',
            \'subjects\',
            \'academicYears\',
            \'semesters\',
            \'currentYear\',
            \'activeSemester\',
            \'selectedTeacher\',
            \'selectedSchoolId\',
            \'currentAssignments\'
        ));
    }';

$newMethod = 'public function create(Request $request)
    {
        $user = auth()->user();

        $academicYears = AcademicYear::orderBy(\'start_date\', \'desc\')->get();
        $currentYear = AcademicYear::where(\'is_active\', 1)->first();
        $activeSemester = Semester::where(\'is_active\', true)->first();

        // Filter semesters by selected or current academic year
        $selectedAcademicYearId = $request->filled(\'academic_year_id\')
            ? $request->academic_year_id
            : ($currentYear ? $currentYear->id : null);
        $semesters = $selectedAcademicYearId
            ? Semester::where(\'academic_year_id\', $selectedAcademicYearId)->orderBy(\'semester_number\')->get()
            : Semester::orderBy(\'id\')->get();

        $teacherId = $request->teacher_id;
        $selectedTeacher = $teacherId ? Teacher::with(\'school\')->find($teacherId) : null;

        // Prioritas: (1) teacher\'s school_id, (2) explicit school_id from request
        // Gunakan filled() bukan ?? agar empty string "" dari URL tidak menimpa fallback
        $selectedSchoolId = $selectedTeacher
            ? $selectedTeacher->school_id
            : ($request->filled(\'school_id\') ? $request->school_id : null);

        $selectedSemesterId = $request->filled(\'semester_id\')
            ? $request->semester_id
            : ($activeSemester && $activeSemester->academic_year_id == $selectedAcademicYearId ? $activeSemester->id : $semesters->first()?->id);

        // Get schools for filter
        $schools = $user->isSuperAdmin()
            ? School::where(\'is_active\', 1)->schoolsOnly()->orderBy(\'name\')->get()
            : School::where(\'id\', $user->school_id)->get();

        // Get teachers (filtered by school if selected)
        $teacherQuery = Teacher::where(\'is_active\', 1)
            ->with([\'school\', \'employee\'])
            ->orderBy(\'full_name\');

        if (!$user->isSuperAdmin()) {
            $teacherQuery->where(\'school_id\', $user->school_id);
        } elseif ($selectedSchoolId) {
            $teacherQuery->where(\'school_id\', $selectedSchoolId);
        }

        $teachers = $teacherQuery->get();

        $classrooms = collect([]);
        $subjects = collect([]);
        $currentAssignments = collect([]);

        if ($selectedTeacher) {
            $classrooms = Classroom::where(\'school_id\', $selectedTeacher->school_id)
                ->where(\'academic_year_id\', $selectedAcademicYearId)
                ->where(\'is_active\', 1)
                ->orderBy(\'grade_level\')
                ->orderBy(\'class_name\')
                ->get();

            // Use competent subjects if available, fallback to all school subjects
            $competentSubjectIds = $selectedTeacher->competentSubjects()->pluck(\'subjects.id\');
            if ($competentSubjectIds->isNotEmpty()) {
                $subjects = Subject::whereIn(\'id\', $competentSubjectIds)
                    ->where(\'is_active\', 1)
                    ->orderBy(\'subject_name\')
                    ->get();
            } else {
                $subjects = Subject::where(\'school_id\', $selectedTeacher->school_id)
                    ->where(\'is_active\', 1)
                    ->orderBy(\'subject_name\')
                    ->get();
            }

            // Existing teaching assignments
            if ($selectedAcademicYearId) {
                $currentAssignments = TeachingAssignment::where(\'teacher_id\', $teacherId)
                    ->where(\'academic_year_id\', $selectedAcademicYearId)
                    ->when($selectedSemesterId, fn ($q) => $q->where(\'semester_id\', $selectedSemesterId))
                    ->with([\'classroom\', \'subject\', \'semester\'])
                    ->get();
            }
        }

        return view(\'admin.assignments.teaching.create\', compact(
            \'teachers\',
            \'schools\',
            \'classrooms\',
            \'subjects\',
            \'academicYears\',
            \'semesters\',
            \'currentYear\',
            \'activeSemester\',
            \'selectedTeacher\',
            \'selectedSchoolId\',
            \'selectedAcademicYearId\',
            \'selectedSemesterId\',
            \'currentAssignments\'
        ));
    }';

// Normalize whitespace untuk matching (tabs vs spaces)
$contentNormalized = preg_replace('/\t/', '    ', $content);

if (strpos($contentNormalized, trim($oldMethod)) !== false) {
    $patched = str_replace(trim($oldMethod), trim($newMethod), $contentNormalized);
    file_put_contents($controllerPath, $patched);
    echo "<p class='ok'>✅ Controller berhasil dipatch (exact match)!</p>";
} else {
    echo "<p class='warn'>⚠️ Exact match gagal. Mencoba regex patch...</p>";
    
    // Regex approach: ganti blok create() method dengan pendekatan fungsi
    $pattern = '/(public function create\(Request \$request\)\s*\{)(.*?)(\n    \})/s';
    
    if (preg_match($pattern, $contentNormalized, $matches)) {
        $patched = preg_replace($pattern, $newMethod, $contentNormalized, 1);
        if ($patched && $patched !== $contentNormalized) {
            file_put_contents($controllerPath, $patched);
            echo "<p class='ok'>✅ Controller berhasil dipatch via regex!</p>";
        } else {
            echo "<p class='err'>❌ Regex replacement gagal mengubah content.</p>";
            echo "<p class='info'>Method create() ditemukan di posisi: " . strpos($contentNormalized, 'public function create') . "</p>";
        }
    } else {
        echo "<p class='err'>❌ Regex tidak menemukan method create(). Struktur file mungkin berbeda.</p>";
        
        // Tampilkan snippet sekitar method create untuk diagnosa
        $pos = strpos($contentNormalized, 'public function create');
        if ($pos !== false) {
            echo "<pre>" . htmlspecialchars(substr($contentNormalized, $pos, 500)) . "</pre>";
        }
    }
}

// Clear view cache
echo "<h2>Membersihkan Cache...</h2>";
$viewCacheDir = $base . 'storage/framework/views/';
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '*.php');
    $cleared = 0;
    foreach ($files as $f) { if (@unlink($f)) $cleared++; }
    echo "<p class='ok'>✅ $cleared view cache dihapus.</p>";
}

// Hapus route cache juga
$routeCache = $base . 'bootstrap/cache/routes-v7.php';
if (file_exists($routeCache) && @unlink($routeCache)) {
    echo "<p class='ok'>✅ Route cache dihapus.</p>";
}
$configCache = $base . 'bootstrap/cache/config.php';
if (file_exists($configCache) && @unlink($configCache)) {
    echo "<p class='ok'>✅ Config cache dihapus.</p>";
}

echo "<hr><p style='color:#ff5252;font-weight:bold;'>⚠️ HAPUS file patch_controller.php setelah selesai!</p>";
echo "<p><a href='https://perguruanpembda.com/admin/assignments/teaching/create?teacher_id=295' target='_blank' style='color:#03dac6'>→ Test halaman penugasan mengajar sekarang</a></p>";
echo "</body></html>";
