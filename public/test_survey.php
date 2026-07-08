<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<pre>";
echo "Testing Survey Model...\n";
try {
    $surveys = \App\Models\Survey::all();
    echo "Count: " . $surveys->count() . "\n";
} catch (\Exception $e) {
    echo "Error on Survey::all(): " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "Testing Route...\n";
try {
    $route = app('router')->getRoutes()->match(request()->create('/admin/surveys', 'GET'));
    echo "Action: " . $route->getActionName() . "\n";
} catch (\Exception $e) {
    echo "Error matching route: " . $e->getMessage() . "\n";
}
echo "</pre>";
