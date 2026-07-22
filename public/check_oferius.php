<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Teacher;

$teachers = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Guru TKJ%')->orWhere('full_name', 'LIKE', '%(42)%')->get();
foreach($teachers as $t) {
    echo "ID: " . $t->id . " | Name: " . $t->full_name . "\n";
}
