<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
if (php_sapi_name() !== 'cli' && request('secret') !== 'pembda99') die('Unauthorized');

use Illuminate\Support\Facades\DB;

try {
    DB::statement('ALTER TABLE teaching_assignments DROP INDEX unique_teaching_assignment_semester');
    echo "Dropped index unique_teaching_assignment_semester<br>";
} catch (\Exception $e) {
    echo "Error drop index: " . $e->getMessage() . "<br>";
}

try {
    DB::statement('ALTER TABLE teaching_assignments DROP INDEX teaching_assignments_academic_year_id_semester_id_classroom_id_subject_id_teacher_id_unique');
    echo "Dropped auto-generated index<br>";
} catch (\Exception $e) {
    echo "Error drop auto index: " . $e->getMessage() . "<br>";
}

try {
    // Add the new constraint using raw SQL to ensure it works
    DB::statement('ALTER TABLE teaching_assignments ADD UNIQUE INDEX unique_teaching_assignment_block (academic_year_id, semester_id, classroom_id, subject_id, teacher_id, block_type)');
    echo "Added new constraint<br>";
} catch (\Exception $e) {
    echo "Error add constraint: " . $e->getMessage() . "<br>";
}

echo "Done!";
