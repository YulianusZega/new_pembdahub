<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo '=== FINAL PROJECTS DATA ===' . PHP_EOL;
$projects = App\Models\FinalProject::with(['student.school', 'advisor'])->get();

if ($projects->isEmpty()) {
    echo 'TIDAK ADA DATA final_projects di database lokal.' . PHP_EOL;
} else {
    foreach ($projects as $p) {
        echo 'Project ID: ' . $p->id . PHP_EOL;
        echo '  student_id (kolom DB): [' . $p->student_id . '] type=' . gettype($p->student_id) . PHP_EOL;
        echo '  student relasi: ' . ($p->student ? 'LOADED' : '*** NULL ***') . PHP_EOL;
        if ($p->student) {
            echo '  student->id: ' . $p->student->id . PHP_EOL;
            echo '  student->full_name: ' . $p->student->full_name . PHP_EOL;
            echo '  student->school_id (raw): [' . $p->student->school_id . '] type=' . gettype($p->student->school_id) . PHP_EOL;
            echo '  student->school relasi: ' . ($p->student->school ? 'LOADED' : '*** NULL ***') . PHP_EOL;
            if ($p->student->school) {
                echo '  student->school->id: ' . $p->student->school->id . PHP_EOL;
                echo '  student->school->type: ' . $p->student->school->type . PHP_EOL;
                echo '  student->school->name: ' . $p->student->school->name . PHP_EOL;
            }
        }
        echo '  status: ' . $p->status . PHP_EOL;
        echo '---' . PHP_EOL;
    }
}

echo PHP_EOL . '=== TEACHERS DATA ===' . PHP_EOL;
$teachers = App\Models\Teacher::with('school')->get();
echo 'Total guru: ' . $teachers->count() . PHP_EOL;
foreach ($teachers as $t) {
    echo '  Teacher ID: ' . $t->id . ' | school_id: [' . $t->school_id . '] type=' . gettype($t->school_id) . ' | school->type: ' . ($t->school ? $t->school->type : 'NULL') . ' | name: ' . $t->full_name . PHP_EOL;
}

echo PHP_EOL . '=== SCHOOLS DATA ===' . PHP_EOL;
$schools = App\Models\School::all();
foreach ($schools as $s) {
    echo '  School ID: ' . $s->id . ' | type: ' . $s->type . ' | name: ' . $s->name . PHP_EOL;
}
