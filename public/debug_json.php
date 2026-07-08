<?php
if (($_GET['secret'] ?? '') !== 'pembda99') {
    die('Forbidden');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: application/json');

$response = [
    'status' => 'success',
    'diagnostics' => []
];

try {
    // 1. Teacher & User info
    $teacher = \App\Models\Teacher::where('full_name', 'like', '%Darmawati%')->first();
    if ($teacher) {
        $user = \App\Models\User::find($teacher->user_id);
        $response['diagnostics']['teacher'] = [
            'id' => $teacher->id,
            'name' => $teacher->full_name,
            'school_id' => $teacher->school_id,
            'user_id' => $teacher->user_id,
            'user_exists' => $user ? true : false,
            'user_role' => $user ? $user->role : null,
            'user_username' => $user ? $user->username : null,
        ];
    } else {
        $response['diagnostics']['teacher'] = 'not_found';
    }

    // 2. Final Project info
    $project = \App\Models\FinalProject::find(1);
    if ($project) {
        $response['diagnostics']['project'] = [
            'id' => $project->id,
            'title' => $project->title,
            'status' => $project->status,
            'student_id' => $project->student_id,
            'advisor_id' => $project->advisor_id,
        ];
    } else {
        $response['diagnostics']['project'] = 'not_found';
    }

    // 3. Query check
    if ($teacher) {
        $projects = \App\Models\FinalProject::where('advisor_id', $teacher->id)->get();
        $response['diagnostics']['query_results'] = [];
        foreach ($projects as $p) {
            $response['diagnostics']['query_results'][] = [
                'id' => $p->id,
                'title' => $p->title,
                'status' => $p->status,
                'student_name' => $p->student ? $p->student->full_name : null,
            ];
        }
    }
} catch (\Throwable $e) {
    $response['status'] = 'error';
    $response['error'] = $e->getMessage();
}

file_put_contents(__DIR__ . '/debug_result.json', json_encode($response, JSON_PRETTY_PRINT));
echo json_encode($response);
