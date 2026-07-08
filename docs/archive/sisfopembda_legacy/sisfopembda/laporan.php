<?php
require_once 'config.php';
require_once 'auth.php';

// Require login and permission
$auth->requireLogin();
$auth->requirePermission('laporan');

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
}

// Handle export to Excel
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    $unit_id = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;
    $tahun_pelajaran = isset($_GET['tahun_pelajaran']) ? $_GET['tahun_pelajaran'] : '2025/2026';
    
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Laporan_Penugasan_' . str_replace('/', '_', $tahun_pelajaran) . '.xls"');
    header('Cache-Control: max-age=0');
    
    echo '<table border="1">';
    echo '<tr><th colspan="15">LAPORAN PENUGASAN PEGAWAI YAYASAN PERGURUAN PEMBDA NIAS</th></tr>';
    echo '<tr><th colspan="15">TAHUN PELAJARAN: ' . $tahun_pelajaran . '</th></tr>';
    if ($unit_id) {
        $stmt = $pdo->prepare("SELECT nama FROM unit WHERE id = ?");
        $stmt->execute([$unit_id]);
        $unit = $stmt->fetch();
        echo '<tr><th colspan="15">UNIT: ' . $unit['nama'] . '</th></tr>';
    }
    echo '<tr><th>No</th><th>Nama Pegawai</th><th>Unit</th><th>Status</th><th>Gaji Pokok</th><th>Jam Mengajar</th><th>Jam Honor</th><th>Honor</th><th>Tunjangan Keluarga</th><th>Tunjangan Anak</th><th>Tunjangan Beras</th><th>Tunjangan Jabatan</th><th>Total</th></tr>';
    
    $where = ["p.tahun_pelajaran = ?"];
    $params = [$tahun_pelajaran];
    if ($unit_id) {
        $where[] = "p.unit_id = ?";
        $params[] = $unit_id;
    }
    
    $sql = "SELECT p.*, pg.nama as pegawai_nama, pg.status_kepegawaian, pg.gaji_pokok,
                   u.nama as unit_nama,
                   COALESCE(SUM(j.tunjangan_jabatan), 0) as total_tunjangan_jabatan
            FROM penugasan p
            LEFT JOIN pegawai pg ON p.pegawai_id = pg.id
            LEFT JOIN unit u ON p.unit_id = u.id
            LEFT JOIN penugasan_jabatan pj ON p.id = pj.penugasan_id
            LEFT JOIN jabatan j ON pj.jabatan_id = j.id
            WHERE " . implode(' AND ', $where) . "
            GROUP BY p.id
            ORDER BY u.nama, pg.nama";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $no = 1;
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td>' . $no++ . '</td>';
        echo '<td>' . $row['pegawai_nama'] . '</td>';
        echo '<td>' . $row['unit_nama'] . '</td>';
        echo '<td>' . $row['status_kepegawaian'] . '</td>';
        echo '<td>' . formatRupiah($row['gaji_pokok']) . '</td>';
        echo '<td>' . $row['jam_mengajar'] . '</td>';
        echo '<td>' . $row['jam_honor'] . '</td>';
        echo '<td>' . formatRupiah($row['honor']) . '</td>';
        echo '<td>' . formatRupiah($row['tunjangan_keluarga']) . '</td>';
        echo '<td>' . formatRupiah($row['tunjangan_anak']) . '</td>';
        echo '<td>' . formatRupiah($row['tunjangan_beras']) . '</td>';
        echo '<td>' . formatRupiah($row['total_tunjangan_jabatan']) . '</td>';
        echo '<td>' . formatRupiah($row['total']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    exit;
}

// Get filter values
$unit_id = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;
$tahun_pelajaran = isset($_GET['tahun_pelajaran']) ? $_GET['tahun_pelajaran'] : '2025/2026';

// Build query
$where = ["p.tahun_pelajaran = ?"];
$params = [$tahun_pelajaran];
if ($unit_id) {
    $where[] = "p.unit_id = ?";
    $params[] = $unit_id;
}

$sql = "SELECT p.*, pg.nama as pegawai_nama, pg.status_kepegawaian, pg.gaji_pokok,
               u.nama as unit_nama,
               COALESCE(SUM(j.tunjangan_jabatan), 0) as total_tunjangan_jabatan
        FROM penugasan p
        LEFT JOIN pegawai pg ON p.pegawai_id = pg.id
        LEFT JOIN unit u ON p.unit_id = u.id
        LEFT JOIN penugasan_jabatan pj ON p.id = pj.penugasan_id
        LEFT JOIN jabatan j ON pj.jabatan_id = j.id
        WHERE " . implode(' AND ', $where) . "
        GROUP BY p.id
        ORDER BY u.nama, pg.nama";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$penugasans = $stmt->fetchAll();

// Get summary data
$summaryStmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_pegawai,
        SUM(p.jam_mengajar) as total_jam_mengajar,
        SUM(p.jam_honor) as total_jam_honor,
        SUM(p.honor) as total_honor,
        SUM(p.tunjangan_keluarga) as total_tunjangan_keluarga,
        SUM(p.tunjangan_anak) as total_tunjangan_anak,
        SUM(p.tunjangan_beras) as total_tunjangan_beras,
        SUM(p.total) as grand_total
    FROM penugasan p
    WHERE " . implode(' AND ', $where)
);
$summaryStmt->execute($params);
$summary = $summaryStmt->fetch();

// Get units for dropdown
$unitsStmt = $pdo->query("SELECT * FROM unit ORDER BY nama");
$units = $unitsStmt->fetchAll();

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
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #667eea;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            text-align: center;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
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
        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .table th {
            font-size: 0.9rem;
            padding: 12px 8px;
        }
        .table td {
            padding: 10px 8px;
            font-size: 0.85rem;
        }
        .export-buttons {
            margin-bottom: 20px;
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
                            <div class="col-md-6">
                                <h2><i class="fas fa-file-invoice-dollar me-3"></i>Laporan Gaji</h2>
                                <p class="mb-0">Laporan daftar gaji pegawai</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="laporan_gaji.php" class="btn btn-light">
                                        <i class="fas fa-file-invoice-dollar me-2"></i>Buka Laporan Gaji
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-users text-primary stats-icon"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Total Pegawai</h6>
                                        <h3 class="mb-0"><?php echo number_format($summary['total_pegawai']); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-clock text-warning stats-icon"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Total Jam Honor</h6>
                                        <h3 class="mb-0"><?php echo number_format($summary['total_jam_honor']); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-money-bill text-success stats-icon"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Total Honor</h6>
                                        <h3 class="mb-0"><?php echo formatRupiah($summary['total_honor']); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-chart-line text-info stats-icon"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Grand Total</h6>
                                        <h3 class="mb-0"><?php echo formatRupiah($summary['grand_total']); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <div class="filter-card">
                        <h5><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="unit_id" class="form-label">Unit Sekolah</label>
                                <select name="unit_id" id="unit_id" class="form-select">
                                    <option value="">Semua Unit</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?php echo $unit['id']; ?>" 
                                                <?php echo $unit_id == $unit['id'] ? 'selected' : ''; ?>>
                                            <?php echo $unit['nama']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tahun_pelajaran" class="form-label">Tahun Pelajaran</label>
                                <select name="tahun_pelajaran" id="tahun_pelajaran" class="form-select">
                                    <option value="2025/2026" <?php echo $tahun_pelajaran == '2025/2026' ? 'selected' : ''; ?>>2025/2026</option>
                                    <option value="2026/2027" <?php echo $tahun_pelajaran == '2026/2027' ? 'selected' : ''; ?>>2026/2027</option>
                                    <option value="2027/2028" <?php echo $tahun_pelajaran == '2027/2028' ? 'selected' : ''; ?>>2027/2028</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="?export=excel&unit_id=<?php echo $unit_id; ?>&tahun_pelajaran=<?php echo urlencode($tahun_pelajaran); ?>" 
                                   class="btn btn-success">
                                    <i class="fas fa-file-excel me-1"></i>Export Excel
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Data Table -->
                    <div class="table-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5><i class="fas fa-table me-2"></i>Data Penugasan</h5>
                            <span class="badge bg-primary"><?php echo count($penugasans); ?> data ditemukan</span>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Pegawai</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Jam Mengajar</th>
                                        <th>Jam Honor</th>
                                        <th>Honor</th>
                                        <th>Tunjangan Keluarga</th>
                                        <th>Tunjangan Anak</th>
                                        <th>Tunjangan Beras</th>
                                        <th>Tunjangan Jabatan</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($penugasans)): ?>
                                        <tr>
                                            <td colspan="12" class="text-center py-4">
                                                <i class="fas fa-info-circle text-muted me-2"></i>
                                                Tidak ada data penugasan untuk filter yang dipilih.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($penugasans as $p): ?>
                                            <tr>
                                                <td class="text-center"><?php echo $no++; ?></td>
                                                <td><strong><?php echo $p['pegawai_nama']; ?></strong></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $p['unit_nama']; ?></span>
                                                </td>
                                                <td>
                                                    <?php echo getStatusBadge($p['status_kepegawaian']); ?>
                                                </td>
                                                <td class="text-center"><?php echo $p['jam_mengajar']; ?> jam</td>
                                                <td class="text-center"><?php echo $p['jam_honor']; ?> jam</td>
                                                <td class="text-end"><?php echo formatRupiah($p['honor']); ?></td>
                                                <td class="text-end"><?php echo formatRupiah($p['tunjangan_keluarga']); ?></td>
                                                <td class="text-end"><?php echo formatRupiah($p['tunjangan_anak']); ?></td>
                                                <td class="text-end"><?php echo formatRupiah($p['tunjangan_beras']); ?></td>
                                                <td class="text-end"><?php echo formatRupiah($p['total_tunjangan_jabatan']); ?></td>
                                                <td class="text-end"><strong><?php echo formatRupiah($p['total']); ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <?php if (!empty($penugasans)): ?>
                                    <tfoot>
                                        <tr class="table-dark">
                                            <th colspan="4" class="text-center">TOTAL</th>
                                            <th class="text-center"><?php echo number_format($summary['total_jam_mengajar']); ?></th>
                                            <th class="text-center"><?php echo number_format($summary['total_jam_honor']); ?></th>
                                            <th class="text-end"><?php echo formatRupiah($summary['total_honor']); ?></th>
                                            <th class="text-end"><?php echo formatRupiah($summary['total_tunjangan_keluarga']); ?></th>
                                            <th class="text-end"><?php echo formatRupiah($summary['total_tunjangan_anak']); ?></th>
                                            <th class="text-end"><?php echo formatRupiah($summary['total_tunjangan_beras']); ?></th>
                                            <th class="text-end">-</th>
                                            <th class="text-end"><strong><?php echo formatRupiah($summary['grand_total']); ?></strong></th>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto submit form when dropdown changes
        document.getElementById('unit_id').addEventListener('change', function() {
            this.form.submit();
        });
        
        document.getElementById('tahun_pelajaran').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>
