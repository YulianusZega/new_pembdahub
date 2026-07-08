<?php
/**
 * Temporary script to run training_modules migration on hosting.
 * DELETE THIS FILE AFTER USE!
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<pre>";
echo "Running migration...\n\n";

try {
    echo "Running migrations...\n";
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo Illuminate\Support\Facades\Artisan::output();
    echo "\n✅ Migration completed!\n\n";

    echo "Running TrainingModuleSeeder...\n";
    Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'Database\\Seeders\\TrainingModuleSeeder',
        '--force' => true
    ]);
    echo Illuminate\Support\Facades\Artisan::output();
    echo "\n✅ Seeding completed successfully!";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "\n\n⚠️ HAPUS FILE INI SETELAH SELESAI!";
echo "</pre>";
