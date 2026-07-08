<?php
require_once 'config.php';
require_once 'auth.php';

// Check authentication
$auth = new Auth($pdo);
$auth->requireLogin();

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
    header('Location: login.php');
    exit;
}

// Get menu items and user info
$menuItems = getMenuItems();
$userRole = $auth->getRole();
$fullName = $auth->getFullName();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Gaji - SISFOPEMBDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(5px);
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .header-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .menu-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar-brand h4 {
            color: white;
            margin: 0;
            font-weight: 600;
        }
        .avatar-circle {
            width: 35px;
            height: 35px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="sidebar-brand">
                    <h4><i class="fas fa-graduation-cap"></i> SISFOPEMBDA</h4>
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
                
                <nav class="nav flex-column">
                    <?php foreach ($menuItems as $key => $menu): ?>
                        <a class="nav-link <?php echo $key === 'laporan' ? 'active' : ''; ?>" href="<?php echo $menu['file']; ?>">
                            <i class="<?php echo $menu['icon']; ?>"></i> <?php echo $menu['title']; ?>
                        </a>
                    <?php endforeach; ?>
                    
                    <hr class="border-light mx-3">
                    <a class="nav-link text-danger" href="?logout=1">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="container-fluid py-4">
                    <!-- Header -->
                    <div class="header-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2><i class="fas fa-file-invoice-dollar me-3"></i>Laporan Gaji</h2>
                                <p class="mb-0">Menu utama untuk mengakses laporan daftar gaji pegawai</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <i class="fas fa-money-check-alt" style="font-size: 3rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Laporan Gaji -->
                    <div class="menu-card">
                        <div class="text-center">
                            <i class="fas fa-file-invoice-dollar text-primary mb-4" style="font-size: 4rem; opacity: 0.6;"></i>
                            <h3 class="mb-3">Laporan Daftar Gaji</h3>
                            <p class="text-muted mb-4">
                                Akses laporan komprehensif daftar gaji pegawai berdasarkan data penugasan,<br>
                                dengan berbagai filter dan opsi tampilan yang tersedia.
                            </p>
                            
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body p-4">
                                            <div class="row align-items-center">
                                                <div class="col-md-3 text-center">
                                                    <i class="fas fa-money-check-alt text-success" style="font-size: 3rem;"></i>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5 class="card-title mb-2">Laporan Daftar Gaji Pegawai</h5>
                                                    <p class="card-text text-muted mb-0">
                                                        Generate dan tampilkan laporan gaji lengkap dengan filter berdasarkan tahun pelajaran dan unit kerja
                                                    </p>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <a href="laporan_gaji.php" class="btn btn-primary btn-lg">
                                                        <i class="fas fa-arrow-right me-2"></i>
                                                        Buka Laporan
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <div class="p-3">
                                            <i class="fas fa-filter text-info mb-2" style="font-size: 1.5rem;"></i>
                                            <h6>Filter Data</h6>
                                            <small class="text-muted">Filter berdasarkan tahun pelajaran dan unit</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3">
                                            <i class="fas fa-eye text-warning mb-2" style="font-size: 1.5rem;"></i>
                                            <h6>Preview Mode</h6>
                                            <small class="text-muted">Tampilan preview laporan gaji tersedia</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3">
                                            <i class="fas fa-calculator text-primary mb-2" style="font-size: 1.5rem;"></i>
                                            <h6>Perhitungan Otomatis</h6>
                                            <small class="text-muted">Kalkulasi tunjangan dan total otomatis</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
