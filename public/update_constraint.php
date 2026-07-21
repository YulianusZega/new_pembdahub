<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
if (php_sapi_name() !== 'cli' && request('secret') !== 'pembda99') die('Unauthorized');

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('teaching_assignments', function (Blueprint $table) {
    // Drop the old unique constraint
    $table->dropUnique('unique_teaching_assignment_semester');
    
    // Add the new unique constraint including block_type
    $table->unique(['academic_year_id', 'semester_id', 'classroom_id', 'subject_id', 'teacher_id', 'block_type'], 'unique_teaching_assignment_block');
});

echo "Unique constraint updated successfully!";
