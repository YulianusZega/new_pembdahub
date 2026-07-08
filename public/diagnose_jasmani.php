<?php
/**
 * Diagnostic script for subject names
 */
header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Subject;

$subjects = Subject::get();
echo "TOTAL SUBJECTS: " . $subjects->count() . "\n\n";

foreach ($subjects as $sub) {
    echo "ID: {$sub->id}\n";
    echo "  code: " . var_export($sub->code, true) . "\n";
    echo "  name: " . var_export($sub->name, true) . "\n";
    echo "  subject_code: " . var_export($sub->subject_code, true) . "\n";
    echo "  subject_name: " . var_export($sub->subject_name, true) . "\n";
    echo "  kkm: " . var_export($sub->kkm, true) . "\n";
    echo "  is_active: " . var_export($sub->is_active, true) . "\n";
    echo "----------------------------------------\n";
}
