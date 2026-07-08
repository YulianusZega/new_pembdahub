<?php
// auth.php - Authentication functions
// Perbaikan: Tambahkan pemeriksaan untuk memastikan sesi belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['unit_id'] = $user['unit_id'];
            $_SESSION['is_logged_in'] = true;
            return true;
        }
        return false;
    }
    
    public function logout() {
        session_destroy();
        header('Location: login.php');
        exit;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
    }
    
    public function getRole() {
        return $_SESSION['role'] ?? null;
    }
    
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function getUnitId() {
        return $_SESSION['unit_id'] ?? null;
    }
    
    public function getFullName() {
        return $_SESSION['full_name'] ?? null;
    }
    
    public function hasPermission($permission) {
        $role = $this->getRole();
        
        $permissions = [
            'admin' => [
                'dashboard', 'unit', 'jabatan', 'pegawai', 'jam_honor', 
                'tunjangan_formula', 'override_rules', 'penugasan', 'laporan', 'users', 'evaluasi'
            ],
            'operator_sekolah' => [
                'dashboard', 'unit', 'jabatan', 'pegawai', 'jam_honor', 
                'tunjangan_formula', 'override_rules', 'penugasan', 'laporan'
            ],
            'kepala_sekolah' => [
                'dashboard', 'laporan', 'evaluasi'
            ]
        ];
        
        return isset($permissions[$role]) && in_array($permission, $permissions[$role]);
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    public function requirePermission($permission) {
        $this->requireLogin();
        if (!$this->hasPermission($permission)) {
            header('Location: dashboard.php?error=access_denied');
            exit;
        }
    }
}

// Initialize auth
$auth = new Auth($pdo);

// Helper functions
function checkRole($requiredRole) {
    global $auth;
    return $auth->getRole() === $requiredRole;
}

function hasAnyRole($roles) {
    global $auth;
    return in_array($auth->getRole(), $roles);
}

function getMenuItems() {
    global $auth;
    
    $allMenus = [
        'dashboard' => ['icon' => 'fas fa-tachometer-alt', 'title' => 'Dashboard', 'file' => 'dashboard.php'],
        'unit' => ['icon' => 'fas fa-building', 'title' => 'Data Unit', 'file' => 'unit.php'],
        'jabatan' => ['icon' => 'fas fa-user-tie', 'title' => 'Data Jabatan', 'file' => 'jabatan.php'],
        'pegawai' => ['icon' => 'fas fa-users', 'title' => 'Data Pegawai', 'file' => 'input_pegawai.php'],
        'jam_honor' => ['icon' => 'fas fa-clock', 'title' => 'Jam & Honor', 'file' => 'input_jam_honor.php'],
        'tunjangan_formula' => ['icon' => 'fas fa-calculator', 'title' => 'Formula Tunjangan', 'file' => 'tunjangan_formula.php'],
        'override_rules' => ['icon' => 'fas fa-cogs', 'title' => 'Aturan Khusus', 'file' => 'override_rules.php'],
        'penugasan' => ['icon' => 'fas fa-tasks', 'title' => 'Input Penugasan', 'file' => 'input_penugasan.php'],
        'laporan' => ['icon' => 'fas fa-chart-line', 'title' => 'Laporan', 'file' => 'laporan.php'],
        'evaluasi' => ['icon' => 'fas fa-chart-line', 'title' => 'Evaluasi', 'file' => 'evaluasi.php'],
        'users' => ['icon' => 'fas fa-user-cog', 'title' => 'Kelola User', 'file' => 'users.php']
    ];
    
    $userMenus = [];
    foreach ($allMenus as $key => $menu) {
        if ($auth->hasPermission($key)) {
            $userMenus[$key] = $menu;
        }
    }
    
    return $userMenus;
}
?>