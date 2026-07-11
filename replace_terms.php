<?php
$files = [
    'app/Http/Controllers/Admin/PerformanceContractController.php',
    'app/Http/Controllers/Admin/PositionAssignmentController.php',
    'app/Http/Controllers/Admin/TeachingAssignmentController.php',
    'app/Http/Controllers/Guru/PerformanceContractController.php',
    'resources/views/admin/assignments/positions/create.blade.php',
    'resources/views/admin/assignments/teaching/create.blade.php',
    'resources/views/admin/performance_contracts/index.blade.php',
    'resources/views/admin/performance_contracts/show.blade.php',
    'resources/views/admin/surveys/create.blade.php',
    'resources/views/admin/surveys/edit.blade.php',
    'resources/views/guru/performance_contracts/create.blade.php',
    'resources/views/guru/performance_contracts/edit.blade.php',
    'resources/views/guru/performance_contracts/index.blade.php',
    'resources/views/guru/performance_contracts/print.blade.php',
    'resources/views/guru/performance_contracts/show.blade.php',
    'resources/views/layouts/admin.blade.php',
    'resources/views/layouts/guru.blade.php',
    'resources/views/layouts/yayasan.blade.php',
    'routes/admin.php',
    'routes/guru.php',
    'routes/yayasan.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $newContent = str_replace(
            ['Kontrak Kinerja', 'KONTRAK KINERJA', 'Kontrak kinerja', 'kontrak kinerja', 'Penilaian Kinerja', 'PENILAIAN KINERJA', 'penilaian kinerja'],
            ['Perjanjian Kinerja', 'PERJANJIAN KINERJA', 'Perjanjian kinerja', 'perjanjian kinerja', 'Perjanjian Kinerja', 'PERJANJIAN KINERJA', 'perjanjian kinerja'],
            $content
        );
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Updated: $file\n";
        }
    }
}
