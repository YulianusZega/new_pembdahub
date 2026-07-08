<?php
require_once 'config.php';

try {
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        role ENUM('admin', 'operator_sekolah', 'kepala_sekolah') NOT NULL,
        unit_id INT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (unit_id) REFERENCES unit(id) ON DELETE SET NULL
    )";
    
    $pdo->exec($sql);
    echo "Tabel users berhasil dibuat!\n";
    
    // Check if users already exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    
    if ($userCount == 0) {
        // Insert default users
        $defaultPassword = password_hash('password', PASSWORD_DEFAULT);
        
        $users = [
            ['admin', $defaultPassword, 'Administrator', 'admin', null],
            ['operator1', $defaultPassword, 'Operator Sekolah 1', 'operator_sekolah', 1],
            ['kepala1', $defaultPassword, 'Kepala Sekolah 1', 'kepala_sekolah', 1]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role, unit_id) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($users as $user) {
            $stmt->execute($user);
        }
        
        echo "User default berhasil ditambahkan!\n";
        echo "Login credentials:\n";
        echo "- admin / password (Administrator)\n";
        echo "- operator1 / password (Operator Sekolah)\n";
        echo "- kepala1 / password (Kepala Sekolah)\n";
    } else {
        echo "User sudah ada dalam database.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
