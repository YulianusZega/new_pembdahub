<?php
require_once 'config.php';
require_once 'auth.php';

// Check authentication and permission
$auth = new Auth($pdo);
$auth->requireLogin();
$auth->requirePermission('jabatan');

// Helper function to clean formatted numbers
function cleanFormattedNumber($value) {
    if (empty($value)) return 0;
    return (int) preg_replace('/[^\d]/', '', $value);
}

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
}

// Handle form submission
if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $stmt = $pdo->prepare("INSERT INTO jabatan (nama, tunjangan_jabatan) VALUES (?, ?)");
            $stmt->execute([$_POST['nama'], cleanFormattedNumber($_POST['tunjangan_jabatan'])]);
            $success = "Jabatan berhasil ditambahkan!";
        } elseif ($_POST['action'] == 'edit') {
            $stmt = $pdo->prepare("UPDATE jabatan SET nama = ?, tunjangan_jabatan = ? WHERE id = ?");
            $stmt->execute([$_POST['nama'], cleanFormattedNumber($_POST['tunjangan_jabatan']), $_POST['id']]);
            $success = "Jabatan berhasil diperbarui!";
        } elseif ($_POST['action'] == 'delete') {
            $stmt = $pdo->prepare("DELETE FROM jabatan WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Jabatan berhasil dihapus!";
        }
    }
}

// Get all jabatan
$stmt = $pdo->query("SELECT * FROM jabatan ORDER BY tunjangan_jabatan DESC");
$jabatans = $stmt->fetchAll();

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
    <title>Jabatan - SISFOPEMBDA</title>
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
                            <a class="nav-link <?php echo $key === 'jabatan' ? 'active' : ''; ?>" href="<?php echo $menu['file']; ?>">
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
                        <h5 class="mb-0">Jabatan</h5>
                        <div class="navbar-nav ms-auto">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#jabatanModal">
                                <i class="fas fa-plus me-2"></i>Tambah Jabatan
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
                                    <h3 class="text-white"><?php echo count($jabatans); ?></h3>
                                    <p class="text-light mb-0">Total Jabatan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-success">
                                <div class="card-body text-center">
                                    <?php 
                                    $maxTunjangan = !empty($jabatans) ? max(array_column($jabatans, 'tunjangan_jabatan')) : 0;
                                    ?>
                                    <h6 class="text-white"><?php echo formatRupiah($maxTunjangan); ?></h6>
                                    <p class="text-light mb-0">Tunjangan Tertinggi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-warning">
                                <div class="card-body text-center">
                                    <?php 
                                    $minTunjangan = !empty($jabatans) ? min(array_column($jabatans, 'tunjangan_jabatan')) : 0;
                                    ?>
                                    <h6 class="text-white"><?php echo formatRupiah($minTunjangan); ?></h6>
                                    <p class="text-light mb-0">Tunjangan Terendah</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-info">
                                <div class="card-body text-center">
                                    <?php 
                                    $avgTunjangan = !empty($jabatans) ? array_sum(array_column($jabatans, 'tunjangan_jabatan')) / count($jabatans) : 0;
                                    ?>
                                    <h6 class="text-white"><?php echo formatRupiah($avgTunjangan); ?></h6>
                                    <p class="text-light mb-0">Rata-rata Tunjangan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jabatan Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Daftar Jabatan</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover sisfo-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Jabatan</th>
                                            <th>Tunjangan Jabatan</th>
                                            <th>Jumlah Pemegang</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jabatans as $index => $jabatan): ?>
                                            <?php
                                            // Get count of people holding this position
                                            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM penugasan_jabatan WHERE jabatan_id = ?");
                                            $stmt->execute([$jabatan['id']]);
                                            $pemegangCount = $stmt->fetch()['count'];
                                            
                                            // Get list of people holding this position for tooltip
                                            $pemegangList = [];
                                            if ($pemegangCount > 0) {
                                                $stmt = $pdo->prepare("
                                                    SELECT p.nama as pegawai_nama, u.nama as unit_nama
                                                    FROM penugasan_jabatan pj
                                                    JOIN penugasan pen ON pj.penugasan_id = pen.id
                                                    JOIN pegawai p ON pen.pegawai_id = p.id
                                                    LEFT JOIN unit u ON pen.unit_id = u.id
                                                    WHERE pj.jabatan_id = ?
                                                    ORDER BY p.nama
                                                ");
                                                $stmt->execute([$jabatan['id']]);
                                                while ($row = $stmt->fetch()) {
                                                    $pemegangList[] = $row['pegawai_nama'] . " (" . $row['unit_nama'] . ")";
                                                }
                                            }
                                            $pemegangTooltip = empty($pemegangList) ? 'Tidak ada pemegang jabatan' : implode('&#10;', $pemegangList);
                                            ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user-tie text-muted me-2"></i>
                                                        <?php echo htmlspecialchars($jabatan['nama']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-success">
                                                        <?php echo formatRupiah($jabatan['tunjangan_jabatan']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($pemegangCount > 0): ?>
                                                        <span class="badge bg-info position-relative" 
                                                              style="cursor: pointer;" 
                                                              title="<?php echo htmlspecialchars($pemegangTooltip); ?>"
                                                              data-bs-toggle="tooltip" 
                                                              data-bs-placement="top"
                                                              data-bs-html="false">
                                                            <i class="fas fa-users me-1"></i>
                                                            <?php echo $pemegangCount; ?> orang
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-user-slash me-1"></i>
                                                            Kosong
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn-sisfo btn-sisfo-warning" 
                                                            onclick="editJabatan(<?php echo htmlspecialchars(json_encode($jabatan)); ?>)" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn-sisfo btn-sisfo-danger" 
                                                            onclick="deleteJabatan(<?php echo $jabatan['id']; ?>, '<?php echo htmlspecialchars($jabatan['nama']); ?>')" title="Hapus">
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

    <!-- Jabatan Modal -->
    <div class="modal fade" id="jabatanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="jabatanForm" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Jabatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="add">
                        <input type="hidden" name="id" id="jabatanId">
                        
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tunjangan_jabatan" class="form-label">Tunjangan Jabatan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="tunjangan_jabatan" name="tunjangan_jabatan" placeholder="1,500,000" required>
                            </div>
                            <div class="form-text">Gunakan koma untuk pemisah ribuan (contoh: 1,500,000)</div>
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
                        <p>Apakah Anda yakin ingin menghapus jabatan <strong id="deleteName"></strong>?</p>
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
        // Format number with comma separators
        function formatNumberWithComma(num) {
            if (!num) return '';
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Parse number from formatted string
        function parseFormattedNumber(str) {
            if (!str) return '';
            return str.replace(/[^\d]/g, '');
        }

        function editJabatan(jabatan) {
            document.getElementById('modalTitle').textContent = 'Edit Jabatan';
            document.getElementById('action').value = 'edit';
            document.getElementById('jabatanId').value = jabatan.id;
            document.getElementById('nama').value = jabatan.nama;
            document.getElementById('tunjangan_jabatan').value = formatNumberWithComma(jabatan.tunjangan_jabatan);
            
            new bootstrap.Modal(document.getElementById('jabatanModal')).show();
        }

        function deleteJabatan(id, nama) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteName').textContent = nama;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Reset form when modal is hidden
        document.getElementById('jabatanModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('modalTitle').textContent = 'Tambah Jabatan';
            document.getElementById('action').value = 'add';
            document.getElementById('jabatanForm').reset();
        });

        // Auto-format tunjangan jabatan input
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: 'hover focus'
                });
            });
            
            const tunjanganInput = document.getElementById('tunjangan_jabatan');
            
            if (tunjanganInput) {
                // Format on input
                tunjanganInput.addEventListener('input', function(e) {
                    let value = parseFormattedNumber(e.target.value);
                    if (value) {
                        e.target.value = formatNumberWithComma(value);
                    }
                });

                // Format on blur
                tunjanganInput.addEventListener('blur', function(e) {
                    let value = parseFormattedNumber(e.target.value);
                    if (value) {
                        e.target.value = formatNumberWithComma(value);
                    }
                });

                // Handle form submission - convert back to plain number
                const form = document.getElementById('jabatanForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const tunjanganValue = parseFormattedNumber(tunjanganInput.value);
                        tunjanganInput.value = tunjanganValue;
                    });
                }
            }
        });
    </script>
</body>
</html>
