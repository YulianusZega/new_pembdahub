<?php
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = "TRACING ACTIVITY LOGS FOR DELETED ACCOUNTS\n";
$output .= "==========================================\n\n";

$logs = ActivityLog::where('model_type', User::class)
    ->whereIn('action', ['created', 'deleted'])
    ->orderBy('created_at', 'desc')
    ->get();

$output .= "Total Logs for User Model: " . $logs->count() . "\n";
$output .= "Summary of actions:\n";
$summary = ActivityLog::where('model_type', User::class)
    ->select('action', DB::raw('count(*) as total'))
    ->groupBy('action')
    ->get();

foreach ($summary as $s) {
    $output .= "- {$s->action}: {$s->total}\n";
}

$output .= "\nRecent Deletions details:\n";
$deletions = ActivityLog::where('model_type', User::class)
    ->where('action', 'deleted')
    ->orderBy('created_at', 'desc')
    ->take(50)
    ->get();

foreach ($deletions as $log) {
    $data = $log->data; // This should be the attributes before deletion
    $name = $data['name'] ?? 'Unknown';
    $output .= "[{$log->created_at}] User ID: {$log->model_id} | Name: {$name} | Deleted by: " . ($log->user_id ?? 'System') . "\n";
}

$output .= "\nRecent Creations details:\n";
$creations = ActivityLog::where('model_type', User::class)
    ->where('action', 'created')
    ->orderBy('created_at', 'desc')
    ->take(50)
    ->get();

foreach ($creations as $log) {
    $data = $log->data;
    $name = $data['name'] ?? 'Unknown';
    $role = $data['role'] ?? 'Unknown';
    $output .= "[{$log->created_at}] User ID: {$log->model_id} | Name: {$name} | Role: {$role} | Created by: " . ($log->user_id ?? 'System') . "\n";
}

file_put_contents('activity_trace.txt', $output);
echo "Results written to activity_trace.txt\n";
