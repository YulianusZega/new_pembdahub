<?php
require_once 'config.php';
require_once 'auth.php';

// Check authentication and permission
$auth = new Auth($pdo);
$auth->requireLogin();
$auth->requirePermission('pegawai');

// cleanFormattedNumber function removed for simplification.

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
}

// Handle form submission
if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            // Check if nomor_induk already exists
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM pegawai WHERE nomor_induk = ?");
            $checkStmt->execute([$_POST['nomor_induk']]);
            $exists = $checkStmt->fetchColumn();
            
            if ($exists > 0) {
                $error = "Nomor Induk '{$_POST['nomor_induk']}' sudah ada! Silakan gunakan nomor induk yang berbeda.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO pegawai (nomor_induk, nama, status_kepegawaian, kelompok_pekerjaan, unit_id, pendidikan, jenis_kelamin, status_perkawinan, jumlah_anak, alamat, no_hp, tgl_mulai_bertugas, gaji_pokok, tgl_lahir) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['nomor_induk'], $_POST['nama'], $_POST['status_kepegawaian'], 
                    $_POST['kelompok_pekerjaan'], $_POST['unit_id'], $_POST['pendidikan'],
                    $_POST['jenis_kelamin'], $_POST['status_perkawinan'], $_POST['jumlah_anak'],
                    $_POST['alamat'], $_POST['no_hp'], $_POST['tgl_mulai_bertugas'],
                    $_POST['gaji_pokok'], $_POST['tgl_lahir']
                ]);
                $success = "Pegawai berhasil ditambahkan!";
            }
        } elseif ($_POST['action'] == 'edit') {
            // Check if nomor_induk already exists for different pegawai
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM pegawai WHERE nomor_induk = ? AND id != ?");
            $checkStmt->execute([$_POST['nomor_induk'], $_POST['id']]);
            $exists = $checkStmt->fetchColumn();
            
            if ($exists > 0) {
                $error = "Nomor Induk '{$_POST['nomor_induk']}' sudah digunakan oleh pegawai lain! Silakan gunakan nomor induk yang berbeda.";
            } else {
                $stmt = $pdo->prepare("UPDATE pegawai SET nomor_induk = ?, nama = ?, status_kepegawaian = ?, kelompok_pekerjaan = ?, unit_id = ?, pendidikan = ?, jenis_kelamin = ?, status_perkawinan = ?, jumlah_anak = ?, alamat = ?, no_hp = ?, tgl_mulai_bertugas = ?, gaji_pokok = ?, tgl_lahir = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['nomor_induk'], $_POST['nama'], $_POST['status_kepegawaian'], 
                    $_POST['kelompok_pekerjaan'], $_POST['unit_id'], $_POST['pendidikan'],
                    $_POST['jenis_kelamin'], $_POST['status_perkawinan'], $_POST['jumlah_anak'],
                    $_POST['alamat'], $_POST['no_hp'], $_POST['tgl_mulai_bertugas'],
                    $_POST['gaji_pokok'], $_POST['tgl_lahir'], $_POST['id']
                ]);
                $success = "Data pegawai berhasil diperbarui!";
            }
        } elseif ($_POST['action'] == 'delete') {
            try {
                // Begin transaction
                $pdo->beginTransaction();
                
                // First, delete related penugasan_jabatan records
                $stmt1 = $pdo->prepare("DELETE pj FROM penugasan_jabatan pj 
                                       INNER JOIN penugasan p ON pj.penugasan_id = p.id 
                                       WHERE p.pegawai_id = ?");
                $stmt1->execute([$_POST['id']]);
                
                // Second, delete related pegawai_jabatan records
                $stmt2 = $pdo->prepare("DELETE FROM pegawai_jabatan WHERE pegawai_id = ?");
                $stmt2->execute([$_POST['id']]);
                
                // Third, delete related penugasan records
                $stmt3 = $pdo->prepare("DELETE FROM penugasan WHERE pegawai_id = ?");
                $stmt3->execute([$_POST['id']]);
                
                // Finally, delete the pegawai
                $stmt4 = $pdo->prepare("DELETE FROM pegawai WHERE id = ?");
                $stmt4->execute([$_POST['id']]);
                
                // Commit transaction
                $pdo->commit();
                $success = "Pegawai dan semua data terkait berhasil dihapus!";
                
            } catch (Exception $e) {
                // Rollback transaction on error
                $pdo->rollback();
                $error = "Gagal menghapus pegawai: " . $e->getMessage();
            }
        }
    }
}

// Get all pegawai with unit info
$stmt = $pdo->query("
    SELECT p.*, u.nama as unit_nama 
    FROM pegawai p 
    LEFT JOIN unit u ON p.unit_id = u.id 
    ORDER BY p.nama
");
$pegawais = $stmt->fetchAll();

// Get units for dropdown
$stmt = $pdo->query("SELECT * FROM unit ORDER BY nama");
$units = $stmt->fetchAll();

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
    <title>Data Pegawai - SISFOPEMBDA</title>
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
                    
                    <nav class="nav flex-column mt-3">
                        <?php foreach ($menuItems as $key => $menu): ?>
                            <a class="nav-link <?php echo $key === 'pegawai' ? 'active' : ''; ?>" href="<?php echo $menu['file']; ?>">
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
                        <h5 class="mb-0">Data Pegawai</h5>
                        <div class="navbar-nav ms-auto">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#pegawaiModal">
                                <i class="fas fa-plus me-2"></i>Tambah Pegawai
                            </button>
                        </div>
                    </div>
                </nav>

                <!-- Main Content -->
                <div class="container-fluid p-4">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <h3 class="text-white"><?php echo count($pegawais); ?></h3>
                                    <p class="text-light mb-0">Total Pegawai</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-success">
                                <div class="card-body text-center">
                                    <h3 class="text-white"><?php echo count(array_filter($pegawais, function($p) { return $p['status_kepegawaian'] == 'PNS'; })); ?></h3>
                                    <p class="text-light mb-0">PNS</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-warning">
                                <div class="card-body text-center">
                                    <h3 class="text-white"><?php echo count(array_filter($pegawais, function($p) { return in_array($p['status_kepegawaian'], ['GTY', 'PTY']); })); ?></h3>
                                    <p class="text-light mb-0">GTY/PTY</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-info">
                                <div class="card-body text-center">
                                    <h3 class="text-white"><?php echo count(array_filter($pegawais, function($p) { return $p['kelompok_pekerjaan'] == 'Pendidik'; })); ?></h3>
                                    <p class="text-light mb-0">Pendidik</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter and Search -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-select" id="filterStatus">
                                        <option value="">Semua Status</option>
                                        <option value="PNS">PNS</option>
                                        <option value="GTY">GTY</option>
                                        <option value="PTY">PTY</option>
                                        <option value="Kontrak">Kontrak</option>
                                        <option value="Honorer">Honorer</option>
                                        <option value="Percobaan">Percobaan</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="filterUnit">
                                        <option value="">Semua Unit</option>
                                        <?php foreach ($units as $unit): ?>
                                            <option value="<?php echo $unit['id']; ?>"><?php echo htmlspecialchars($unit['nama']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="searchPegawai" placeholder="Cari nama atau nomor induk...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pegawai Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Daftar Pegawai</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="pegawaiTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nomor Induk</th>
                                            <th>Nama</th>
                                            <th>Status</th>
                                            <th>Unit</th>
                                            <th>Kelompok</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pegawais as $index => $pegawai): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($pegawai['nomor_induk']); ?></span></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user-circle text-muted me-2"></i>
                                                        <div>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($pegawai['nama']); ?></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($pegawai['pendidikan']); ?> | <?php echo $pegawai['jenis_kelamin']; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo getStatusBadge($pegawai['status_kepegawaian']); ?></td>
                                                <td><?php echo htmlspecialchars($pegawai['unit_nama']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $pegawai['kelompok_pekerjaan'] == 'Pendidik' ? 'success' : 'info'; ?>">
                                                        <?php echo $pegawai['kelompok_pekerjaan']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info" 
                                                            onclick="viewPegawai(<?php echo htmlspecialchars(json_encode($pegawai)); ?>)"
                                                            title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="editPegawai(<?php echo htmlspecialchars(json_encode($pegawai)); ?>)"
                                                            title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="deletePegawai(<?php echo $pegawai['id']; ?>, '<?php echo htmlspecialchars($pegawai['nama']); ?>')"
                                                            title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pegawai Modal -->
    <div class="modal fade" id="pegawaiModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="pegawaiForm" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Pegawai</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="add">
                        <input type="hidden" name="id" id="pegawaiId">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nomor_induk" class="form-label">Nomor Induk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nomor_induk" name="nomor_induk" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status_kepegawaian" class="form-label">Status Kepegawaian <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status_kepegawaian" name="status_kepegawaian" required>
                                        <option value="">Pilih Status</option>
                                        <option value="PNS">PNS</option>
                                        <option value="GTY">GTY</option>
                                        <option value="PTY">PTY</option>
                                        <option value="Kontrak">Kontrak</option>
                                        <option value="Honorer">Honorer</option>
                                        <option value="Percobaan">Percobaan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kelompok_pekerjaan" class="form-label">Kelompok Pekerjaan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="kelompok_pekerjaan" name="kelompok_pekerjaan" required>
                                        <option value="">Pilih Kelompok</option>
                                        <option value="Pendidik">Pendidik</option>
                                        <option value="Non Kependidikan">Non Kependidikan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit_id" class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                                    <select class="form-select" id="unit_id" name="unit_id" required>
                                        <option value="">Pilih Unit</option>
                                        <?php foreach ($units as $unit): ?>
                                            <option value="<?php echo $unit['id']; ?>"><?php echo htmlspecialchars($unit['nama']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pendidikan" class="form-label">Pendidikan</label>
                                    <select class="form-select" id="pendidikan" name="pendidikan">
                                        <option value="SMA">SMA</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status_perkawinan" class="form-label">Status Perkawinan</label>
                                    <select class="form-select" id="status_perkawinan" name="status_perkawinan">
                                        <option value="Belum Menikah">Belum Menikah</option>
                                        <option value="Menikah">Menikah</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="jumlah_anak" class="form-label">Jumlah Anak</label>
                                    <input type="number" class="form-control" id="jumlah_anak" name="jumlah_anak" min="0" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tgl_mulai_bertugas" class="form-label">Tanggal Mulai Bertugas</label>
                                    <input type="date" class="form-control" id="tgl_mulai_bertugas" name="tgl_mulai_bertugas">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gaji_pokok" class="form-label">Gaji Pokok</label>
                                    <input type="number" class="form-control" id="gaji_pokok" name="gaji_pokok" value="0" placeholder="Contoh: 3000000" required>
                                    <div class="form-text">Masukkan angka saja, tanpa titik atau koma.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <p>Apakah Anda yakin ingin menghapus pegawai <strong id="deleteName"></strong>?</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Peringatan:</strong> Menghapus pegawai ini akan juga menghapus:
                            <ul class="mb-0 mt-2">
                                <li>Semua data penugasan pegawai</li>
                                <li>Semua data jabatan yang dimiliki pegawai</li>
                                <li>Semua data jabatan dalam penugasan</li>
                                <li>Data honor dan tunjangan</li>
                            </ul>
                            Data yang sudah dihapus tidak dapat dikembalikan!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Status badge function to match PHP getStatusBadge()
        function getStatusBadgeJS(status) {
            const badges = {
                'PNS': 'badge bg-success',
                'GTY': 'badge bg-primary', 
                'PTY': 'badge bg-info',
                'Kontrak': 'badge bg-warning',
                'Honorer': 'badge bg-secondary',
                'Percobaan': 'badge bg-danger'
            };
            
            const badgeClass = badges[status] || 'badge bg-secondary';
            return `<span class="${badgeClass}">${status}</span>`;
        }

        function editPegawai(pegawai) {
            document.getElementById('modalTitle').textContent = 'Edit Pegawai';
            document.getElementById('action').value = 'edit';
            document.getElementById('pegawaiId').value = pegawai.id;
            
            // Fill form fields
            document.getElementById('nomor_induk').value = pegawai.nomor_induk;
            document.getElementById('nama').value = pegawai.nama;
            document.getElementById('status_kepegawaian').value = pegawai.status_kepegawaian;
            document.getElementById('kelompok_pekerjaan').value = pegawai.kelompok_pekerjaan;
            document.getElementById('unit_id').value = pegawai.unit_id;
            document.getElementById('pendidikan').value = pegawai.pendidikan;
            document.getElementById('jenis_kelamin').value = pegawai.jenis_kelamin;
            document.getElementById('status_perkawinan').value = pegawai.status_perkawinan;
            document.getElementById('jumlah_anak').value = pegawai.jumlah_anak || 0;
            document.getElementById('alamat').value = pegawai.alamat || '';
            document.getElementById('no_hp').value = pegawai.no_hp || '';
            document.getElementById('tgl_mulai_bertugas').value = pegawai.tgl_mulai_bertugas || '';
            document.getElementById('gaji_pokok').value = pegawai.gaji_pokok || 0;
            document.getElementById('tgl_lahir').value = pegawai.tgl_lahir || '';
            
            new bootstrap.Modal(document.getElementById('pegawaiModal')).show();
        }

        function viewPegawai(pegawai) {
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><th>Nomor Induk:</th><td>${pegawai.nomor_induk}</td></tr>
                            <tr><th>Nama:</th><td>${pegawai.nama}</td></tr>
                            <tr><th>Status Kepegawaian:</th><td>${getStatusBadgeJS(pegawai.status_kepegawaian)}</td></tr>
                            <tr><th>Kelompok Pekerjaan:</th><td>${pegawai.kelompok_pekerjaan}</td></tr>
                            <tr><th>Unit Kerja:</th><td>${pegawai.unit_nama}</td></tr>
                            <tr><th>Pendidikan:</th><td>${pegawai.pendidikan}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><th>Jenis Kelamin:</th><td>${pegawai.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'}</td></tr>
                            <tr><th>Status Perkawinan:</th><td>${pegawai.status_perkawinan}</td></tr>
                            <tr><th>Jumlah Anak:</th><td>${pegawai.jumlah_anak || 0} orang</td></tr>
                            <tr><th>Tanggal Lahir:</th><td>${pegawai.tgl_lahir || '-'}</td></tr>
                            <tr><th>Mulai Bertugas:</th><td>${pegawai.tgl_mulai_bertugas || '-'}</td></tr>
                            <tr><th>Gaji Pokok:</th><td>Rp ${new Intl.NumberFormat('id-ID').format(pegawai.gaji_pokok || 0)}</td></tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-borderless">
                            <tr><th>No. HP:</th><td>${pegawai.no_hp || '-'}</td></tr>
                            <tr><th>Alamat:</th><td>${pegawai.alamat || '-'}</td></tr>
                        </table>
                    </div>
                </div>
            `;
            
            document.getElementById('viewContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('viewModal')).show();
        }

        function deletePegawai(id, nama) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteName').textContent = nama;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Reset form when modal is hidden
        document.getElementById('pegawaiModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('modalTitle').textContent = 'Tambah Pegawai';
            document.getElementById('action').value = 'add';
            document.getElementById('pegawaiForm').reset();
        });

        // Filter functionality
        document.getElementById('filterStatus').addEventListener('change', filterTable);
        document.getElementById('filterUnit').addEventListener('change', filterTable);
        document.getElementById('searchPegawai').addEventListener('input', filterTable);

        function filterTable() {
            const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
            const unitFilter = document.getElementById('filterUnit').value;
            const searchFilter = document.getElementById('searchPegawai').value.toLowerCase();
            const table = document.getElementById('pegawaiTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                
                if (cells.length > 0) {
                    const nomor = cells[1].textContent.toLowerCase();
                    const nama = cells[2].textContent.toLowerCase();
                    const status = cells[3].textContent.toLowerCase();
                    const unit = cells[4].textContent;
                    
                    const matchStatus = !statusFilter || status.includes(statusFilter);
                    const matchUnit = !unitFilter || row.dataset.unitId === unitFilter;
                    const matchSearch = !searchFilter || nomor.includes(searchFilter) || nama.includes(searchFilter);
                    
                    row.style.display = matchStatus && matchUnit && matchSearch ? '' : 'none';
                }
            }
        }

        // All number formatting JavaScript functions have been removed.
    </script>
</body>
</html>
