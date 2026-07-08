<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database
                            {--keep=7 : Number of backup files to keep}
                            {--compress : Compress the backup file with gzip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the MySQL database to storage/backups directory';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Starting database backup...');

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $backupDir = storage_path('backups');

        // Create backup directory if it doesn't exist
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_His');
        $filename = "backup_{$database}_{$timestamp}.sql";
        $filepath = "{$backupDir}/{$filename}";

        // Build mysqldump command
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        $returnVar = 0;
        $output = [];
        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('❌ Backup failed!');
            $this->error(implode("\n", $output));
            return self::FAILURE;
        }

        // Compress if requested
        if ($this->option('compress') && file_exists($filepath)) {
            $gzFilepath = $filepath . '.gz';
            $fp = fopen($filepath, 'rb');
            $gz = gzopen($gzFilepath, 'wb9');

            if ($fp && $gz) {
                while (!feof($fp)) {
                    gzwrite($gz, fread($fp, 1024 * 512)); // 512KB chunks
                }
                gzclose($gz);
                fclose($fp);
                unlink($filepath);
                $filepath = $gzFilepath;
                $filename .= '.gz';
            }
        }

        $fileSize = filesize($filepath);
        $this->info("✅ Backup berhasil: {$filename}");
        $this->info("📁 Lokasi: {$filepath}");
        $this->info("📊 Ukuran: " . $this->formatBytes($fileSize));

        // Cleanup old backups
        $this->cleanupOldBackups($backupDir, (int) $this->option('keep'));

        return self::SUCCESS;
    }

    /**
     * Remove old backup files, keeping only the specified number.
     */
    private function cleanupOldBackups(string $directory, int $keep): void
    {
        $files = glob("{$directory}/backup_*.sql*");

        if (count($files) > $keep) {
            // Sort by modification time, oldest first
            usort($files, fn($a, $b) => filemtime($a) - filemtime($b));

            $toDelete = array_slice($files, 0, count($files) - $keep);
            foreach ($toDelete as $file) {
                unlink($file);
                $this->info("🗑️  Backup lama dihapus: " . basename($file));
            }
        }
    }

    /**
     * Format bytes to human readable size.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
