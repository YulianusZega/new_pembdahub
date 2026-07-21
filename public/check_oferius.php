<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Subject;

echo "<pre>";
echo "\nSubjects matching 'AGM':\n";
$subjects = Subject::where('name', 'like', '%AGM%')
    ->orWhere('code', 'like', '%AGM%')
    ->orWhere('subject_name', 'like', '%AGM%')
    ->get();

foreach ($subjects as $s) {
    echo "- ID: {$s->id}, Code: " . ($s->code ?? $s->subject_code ?? 'N/A') . ", Name: " . ($s->name ?? $s->subject_name ?? 'N/A') . "\n";
}
echo "</pre>";
