<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\LmsSubmission;
use App\Http\Controllers\Guru\LmsAssignmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$sub = LmsSubmission::first();
if (!$sub) {
    echo "No submission found.";
    exit;
}

echo "Found submission ID: " . $sub->id . "\n";
echo "Assignment max score: " . $sub->assignment->max_score . "\n";

// Login as teacher of this course
$teacherId = $sub->assignment->course->teacher_id;
$teacher = App\Models\Teacher::find($teacherId);
Auth::login($teacher->user);
echo "Logged in as teacher user: " . $teacher->user->email . "\n";

$request = Request::create("/guru/lms/submissions/{$sub->id}/grade", "POST", [
    "score" => 85,
    "feedback" => "Good job!"
]);

$controller = app(LmsAssignmentController::class);
try {
    $response = $controller->grade($request, $sub);
    echo "Response status: " . $response->getStatusCode() . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

