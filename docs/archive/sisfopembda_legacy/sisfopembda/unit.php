<?php
require_once 'config.php';
require_once 'auth.php';

// Check authentication and permission
$auth = new Auth($pdo);
$auth->requireLogin();
$auth->requirePermission('unit');

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
}

// Handle form submission
if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $stmt = $pdo->prepare("INSERT INTO unit (nama, level) VALUES (?, ?)");
            $stmt->execute([$_POST['nama'], $_POST['level']]);
            $success = "Unit berhasil ditambahkan!";
        } elseif ($_POST['action'] == 'edit') {
            $stmt = $pdo->prepare("UPDATE unit SET nama = ?, level = ? WHERE id = ?");
            $stmt->execute([$_POST['nama'], $_POST['level'], $_POST['id']]);
            $success = "Unit berhasil diperbarui!";
        } elseif ($_POST['action'] == 'delete') {
            $stmt = $pdo->prepare("DELETE FROM unit WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Unit berhasil dihapus!";
        }
    }
}

// Get all units
$stmt = $pdo->query("SELECT * FROM unit ORDER BY level, nama");
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
    <title>Unit Kerja - SISFOPEMBDA</title>
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
                            <a class="nav-link <?php echo $key === 'unit' ? 'active' : ''; ?>" href="<?php echo $menu['file']; ?>">
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
                        <h5 class="mb-0">Unit Kerja</h5>
                        <div class="navbar-nav ms-auto">
                            <button class="btn-sisfo btn-sisfo-primary" data-bs-toggle="modal" data-bs-target="#unitModal" title="Tambah Unit">
                                <i class="fas fa-plus"></i><span class="ms-1">Tambah</span>
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

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <h3 class="text-white"><?php echo count($units); ?></h3>
                                    <p class="text-light mb-0">Total Unit</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-success">
                                <div class="card-body text-center">
                                    <h3 class="text-white"><?php echo count(array_filter($units, function($u) { return $u['level'] == 'SMP'; })); ?></h3>
                                    <p class="text-light mb-0">SMP</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-warning">
                                <div class="card-body text-center">
                                    <h3 class="text-white"><?php echo count(array_filter($units, function($u) { return $u['level'] == 'SMA'; })); ?></h3>
                                    <p class="text-light mb-0">SMA</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-info">
                                <div class="card-body text-center">
                                    <h3 class="text-white"><?php echo count(array_filter($units, function($u) { return $u['level'] == 'SMK'; })); ?></h3>
                                    <p class="text-light mb-0">SMK</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Units Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Daftar Unit Kerja</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover sisfo-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Unit</th>
                                            <th>Level</th>
                                            <th>Jumlah Pegawai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($units as $index => $unit): ?>
                                            <?php
                                            // Get employee count for this unit
                                            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pegawai WHERE unit_id = ?");
                                            $stmt->execute([$unit['id']]);
                                            $pegawaiCount = $stmt->fetch()['count'];
                                            ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-building text-muted me-2"></i>
                                                        <?php echo htmlspecialchars($unit['nama']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $unit['level'] == 'SMP' ? 'primary' : 
                                                             ($unit['level'] == 'SMA' ? 'success' : 
                                                              ($unit['level'] == 'SMK' ? 'warning' : 'info')); 
                                                    ?>"><?php echo $unit['level']; ?></span>
                                                </td>
                                                <td><?php echo $pegawaiCount; ?> orang</td>
                                                <td>
                                                    <button class="btn-sisfo btn-sisfo-warning" 
                                                            onclick="editUnit(<?php echo htmlspecialchars(json_encode($unit)); ?>)" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn-sisfo btn-sisfo-danger" 
                                                            onclick="deleteUnit(<?php echo $unit['id']; ?>, '<?php echo htmlspecialchars($unit['nama']); ?>')" title="Hapus">
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

    <!-- Unit Modal -->
    <div class="modal fade" id="unitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="unitForm" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Unit Kerja</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="add">
                        <input type="hidden" name="id" id="unitId">
                        
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Unit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="level" name="level" required>
                                <option value="">Pilih Level</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="SMK">SMK</option>
                                <option value="Yayasan">Yayasan</option>
                            </select>
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
                        <p>Apakah Anda yakin ingin menghapus unit <strong id="deleteName"></strong>?</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
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
        function editUnit(unit) {
            document.getElementById('modalTitle').textContent = 'Edit Unit Kerja';
            document.getElementById('action').value = 'edit';
            document.getElementById('unitId').value = unit.id;
            document.getElementById('nama').value = unit.nama;
            document.getElementById('level').value = unit.level;
            
            new bootstrap.Modal(document.getElementById('unitModal')).show();
        }

        function deleteUnit(id, nama) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteName').textContent = nama;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Reset form when modal is hidden
        document.getElementById('unitModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('modalTitle').textContent = 'Tambah Unit Kerja';
            document.getElementById('action').value = 'add';
            document.getElementById('unitForm').reset();
        });
    </script>
</body>
</html>
