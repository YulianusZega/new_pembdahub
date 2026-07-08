<?php
/**
 * Database Exporter - Export database dari server untuk di-download
 * HAPUS FILE INI SETELAH SELESAI DIGUNAKAN!
 */

$SECRET_TOKEN = 'pembda2026export';

if (!isset($_GET['token']) || $_GET['token'] !== $SECRET_TOKEN) {
    http_response_code(403);
    die('⛔ Akses ditolak. Gunakan ?token=YOUR_TOKEN');
}

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = config('database.connections.mysql.database');
$user = config('database.connections.mysql.username');
$pass = config('database.connections.mysql.password');
$host = config('database.connections.mysql.host');
$port = config('database.connections.mysql.port', 3306);

// Jika user klik download
if (isset($_GET['download']) && $_GET['download'] === 'yes') {
    
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "";
        $sql .= "-- =========================================\n";
        $sql .= "-- Database Export: $db\n";
        $sql .= "-- Exported at: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Server: $host\n";
        $sql .= "-- =========================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";
        
        // Get all tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            // CREATE TABLE statement
            $createStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
            $sql .= "-- ----------------------------------------\n";
            $sql .= "-- Table: $table\n";
            $sql .= "-- ----------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql .= $createStmt['Create Table'] . ";\n\n";
            
            // INSERT statements
            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($rows) > 0) {
                // Batch insert in chunks of 100
                $chunks = array_chunk($rows, 100);
                foreach ($chunks as $chunk) {
                    $columns = array_keys($chunk[0]);
                    $colStr = '`' . implode('`, `', $columns) . '`';
                    $sql .= "INSERT INTO `$table` ($colStr) VALUES\n";
                    
                    $values = [];
                    foreach ($chunk as $row) {
                        $vals = [];
                        foreach ($row as $val) {
                            if ($val === null) {
                                $vals[] = 'NULL';
                            } else {
                                $vals[] = $pdo->quote($val);
                            }
                        }
                        $values[] = '(' . implode(', ', $vals) . ')';
                    }
                    $sql .= implode(",\n", $values) . ";\n\n";
                }
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        // Download as .sql file
        $filename = $db . '_' . date('Y-m-d_His') . '.sql';
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($sql));
        header('Cache-Control: no-cache, must-revalidate');
        echo $sql;
        exit;
        
    } catch (\Exception $e) {
        die('❌ Error: ' . $e->getMessage());
    }
}

// Tampilkan halaman info
echo '<html><head><title>DB Exporter</title>';
echo '<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}';
echo '.ok{color:#00e676}.err{color:#ff5252}.warn{color:#ffab40}.info{color:#40c4ff}';
echo 'h1{color:#bb86fc}h2{color:#03dac6}pre{background:#16213e;padding:15px;border-radius:8px}</style></head><body>';
echo '<h1>📦 Pembda Hub - Database Exporter</h1>';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    echo '<p class="ok">✅ Koneksi database: <b>' . $db . '</b></p>';
    
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo '<h2>📋 Tabel dalam database (' . count($tables) . ' tabel)</h2>';
    echo '<pre>';
    $totalRows = 0;
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        $totalRows += $count;
        printf("  %-45s %6d rows\n", $table, $count);
    }
    echo "\n  Total: $totalRows rows";
    echo '</pre>';
    
    echo '<br><a href="?token=' . $SECRET_TOKEN . '&download=yes" style="background:#00e676;color:#000;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:16px">⬇️ Download Database (.sql)</a>';
    
} catch (\Exception $e) {
    echo '<p class="err">❌ Error: ' . $e->getMessage() . '</p>';
}

echo '<hr><p class="warn">🗑️ <b>HAPUS FILE INI</b> setelah selesai demi keamanan!</p>';
echo '</body></html>';
