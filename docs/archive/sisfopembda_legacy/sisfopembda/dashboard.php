<?php
session_start(); // Tambahkan ini untuk memulai sesi
require_once 'config.php';
require_once 'auth.php';

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
}

// NOTE: Baris berikut telah di-komentar untuk melewati pemeriksaan login dan izin.
// $auth->requireLogin();
// $auth->requirePermission('dashboard');

// Get user info (as a dummy user for testing)
$userRole = 'admin'; // Atur peran dummy untuk melihat semua menu
$fullName = 'Pengguna Dummy';
$unitId = 1;

// SIMPAN INFORMASI DUMMY KE SESI AGAR FUNGSI AUTH BERJALAN DENGAN BENAR
$_SESSION['role'] = $userRole;
$_SESSION['full_name'] = $fullName;
$_SESSION['is_logged_in'] = true;

// Check for access denied error
$accessDenied = isset($_GET['error']) && $_GET['error'] === 'access_denied';

// Get menu items based on user role
// Fungsi ini akan menggunakan peran dummy yang sudah kita atur di atas
$menuItems = getMenuItems();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SISFOPEMBDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="d-flex flex-column">
                    <div class="p-3 text-center border-bottom border-light">
                        <h4 class="text-white mb-0">SISFOPEMBDA</h4>
                        <small class="text-light">Sistem Informasi Administrasi</small>
                    </div>
                    
                    <!-- User Info -->
                    <div class="p-3 border-bottom border-light">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-white small fw-bold"><?php echo htmlspecialchars($fullName); ?></div>
                                <div class="text-light small">
                                    <?php 
                                    $roleLabels = [
                                        'admin' => 'Administrator',
                                        'operator_sekolah' => 'Operator Sekolah', 
                                        'kepala_sekolah' => 'Kepala Sekolah'
                                    ];
                                    echo $roleLabels[$userRole] ?? $userRole;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($accessDenied): ?>
                    <div class="alert alert-danger m-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <small>Anda tidak memiliki akses ke halaman tersebut.</small>
                    </div>
                    <?php endif; ?>
                    
                    <nav class="nav flex-column mt-3">
                        <?php foreach ($menuItems as $key => $menu): ?>
                            <a class="nav-link <?php echo $key === 'dashboard' ? 'active' : ''; ?>" href="<?php echo $menu['file']; ?>">
                                <i class="<?php echo $menu['icon']; ?> me-2"></i> <?php echo $menu['title']; ?>
                            </a>
                        <?php endforeach; ?>
                        
                        <hr class="border-light mx-3">
                        <a class="nav-link text-danger" href="?logout=1">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="container-fluid">
                        <h5 class="mb-0">Dashboard</h5>
                        <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
                            <button class="btn btn-sm btn-outline-primary me-3" id="refreshData" title="Refresh Data">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <span class="navbar-text">
                                <i class="fas fa-calendar me-2"></i>
                                <?php echo date('d F Y'); ?>
                            </span>
                        </div>
                    </div>
                </nav>

                <!-- Dashboard Content -->
                <div class="container-fluid p-4">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="text-uppercase text-light small">Total Pegawai</div>
                                            <div class="h2 mb-0 text-white" id="totalPegawai">-</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-light"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card-success">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="text-uppercase text-light small">Total Unit</div>
                                            <div class="h2 mb-0 text-white" id="totalUnit">-</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-building fa-2x text-light"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card-warning">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="text-uppercase text-light small">Total Penugasan</div>
                                            <div class="h2 mb-0 text-white" id="totalPenugasan">-</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tasks fa-2x text-light"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card-info">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="text-uppercase text-light small">Total Honor</div>
                                            <div class="h6 mb-0 text-white" id="totalHonor">-</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-wave fa-2x text-light"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Tables Row -->
                    <div class="row">
                        <!-- Distribusi Pegawai by Unit -->
                        <div class="col-lg-6 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Distribusi Pegawai per Unit</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="pegawaiChart" class="chart-container"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Status Kepegawaian -->
                        <div class="col-lg-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Status Kepegawaian</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusChart" class="chart-container"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities and Quick Access -->
                    <div class="row">
                        <!-- Recent Penugasan -->
                        <div class="col-lg-8 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Penugasan Terbaru</h5>
                                    <a href="input_penugasan.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Pegawai</th>
                                                    <th>Unit</th>
                                                    <th>Tahun Pelajaran</th>
                                                    <th>Jam Mengajar</th>
                                                    <th>Total Honor</th>
                                                </tr>
                                            </thead>
                                            <tbody id="recentPenugasan">
                                                <!-- Data akan diisi via PHP -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Access -->
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Akses Cepat</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="input_pegawai.php" class="btn btn-outline-primary">
                                            <i class="fas fa-user-plus me-2"></i>Tambah Pegawai
                                        </a>
                                        <a href="input_penugasan.php" class="btn btn-outline-success">
                                            <i class="fas fa-tasks me-2"></i>Input Penugasan
                                        </a>
                                        <a href="laporan_gaji.php" class="btn btn-outline-danger">
                                            <i class="fas fa-file-invoice-dollar me-2"></i>Laporan Gaji
                                        </a>
                                        <a href="laporan.php" class="btn btn-outline-info">
                                            <i class="fas fa-file-export me-2"></i>Laporan Penugasan
                                        </a>
                                        <a href="tunjangan_formula.php" class="btn btn-outline-warning">
                                            <i class="fas fa-cog me-2"></i>Pengaturan Formula
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- System Info -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Informasi Sistem</h6>
                                </div>
                                <div class="card-body">
                                    <small class="text-muted">
                                        <div class="mb-2">
                                            <i class="fas fa-server me-2"></i>
                                            Database: MySQL
                                        </div>
                                        <div class="mb-2">
                                            <i class="fas fa-calendar me-2"></i>
                                            Last Update: <?php echo date('d/m/Y H:i'); ?>
                                        </div>
                                        <div>
                                            <i class="fas fa-user me-2"></i>
                                            Version: 1.0.0
                                        </div>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/dashboard.js?v=<?php echo time(); ?>"></script>
</body>
</html>

<?php
// Include configuration
require_once 'config.php';
?>
