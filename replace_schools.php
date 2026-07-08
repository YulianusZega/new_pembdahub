<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$files = [
    'app/Http/Controllers/Admin/ApplicantController.php',
    'app/Http/Controllers/Admin/UserController.php',
    'resources/views/admin/majors/index.blade.php',
    'routes/web.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace('School::all()', 'School::schoolsOnly()->get()', $content);
        file_put_contents($file, $content);
        echo "Replaced School::all() in $file\n";
    }
}

// Now replace School::where('is_active', true)->get() and similar
// Let's use grep to find files and replace them in PHP using regex
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Http/Controllers'));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        $original = $content;
        
        // Match School::where('is_active', true)->get() or School::where('is_active', 1)->get()
        $content = preg_replace("/School::where\('is_active',\s*(true|1)\)->get\(\)/", "School::where('is_active', $1)->schoolsOnly()->get()", $content);
        $content = preg_replace("/School::where\('is_active',\s*(true|1)\)->orderBy\('name'\)->get\(\)/", "School::where('is_active', $1)->schoolsOnly()->orderBy('name')->get()", $content);
        $content = preg_replace("/\\\App\\\Models\\\School::where\('is_active',\s*(true|1)\)->get\(\)/", "\\App\\Models\\School::where('is_active', $1)->schoolsOnly()->get()", $content);
        $content = preg_replace("/\\\App\\\Models\\\School::where\('is_active',\s*(true|1)\)->orderBy\('name'\)->get\(\)/", "\\App\\Models\\School::where('is_active', $1)->schoolsOnly()->orderBy('name')->get()", $content);

        if ($original !== $content) {
            file_put_contents($path, $content);
            echo "Replaced in $path\n";
        }
    }
}

// resources/views
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        $original = $content;
        
        $content = preg_replace("/School::where\('is_active',\s*(true|1)\)->get\(\)/", "School::where('is_active', $1)->schoolsOnly()->get()", $content);
        $content = preg_replace("/\\\App\\\Models\\\School::where\('is_active',\s*(true|1)\)->get\(\)/", "\\App\\Models\\School::where('is_active', $1)->schoolsOnly()->get()", $content);

        if ($original !== $content) {
            file_put_contents($path, $content);
            echo "Replaced in $path\n";
        }
    }
}

echo "Done.\n";
