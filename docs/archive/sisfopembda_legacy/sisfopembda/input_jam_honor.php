<?php
require_once 'config.php';
require_once 'auth.php';

// Check authentication and permission
$auth = new Auth($pdo);
$auth->requireLogin();
$auth->requirePermission('jam_honor');

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
            $stmt = $pdo->prepare("INSERT INTO jam_honor (unit_id, status_kepegawaian, jam_wajib, honor_per_jam) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['unit_id'], $_POST['status_kepegawaian'], $_POST['jam_wajib'], cleanFormattedNumber($_POST['honor_per_jam'])]);
            $success = "Aturan jam honor berhasil ditambahkan!";
        } elseif ($_POST['action'] == 'edit') {
            $stmt = $pdo->prepare("UPDATE jam_honor SET unit_id = ?, status_kepegawaian = ?, jam_wajib = ?, honor_per_jam = ? WHERE id = ?");
            $stmt->execute([$_POST['unit_id'], $_POST['status_kepegawaian'], $_POST['jam_wajib'], cleanFormattedNumber($_POST['honor_per_jam']), $_POST['id']]);
            $success = "Aturan jam honor berhasil diperbarui!";
        } elseif ($_POST['action'] == 'delete') {
            $stmt = $pdo->prepare("DELETE FROM jam_honor WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Aturan jam honor berhasil dihapus!";
        }
    }
}

// Get all jam_honor with unit info
$stmt = $pdo->query("
    SELECT jh.*, u.nama as unit_nama, u.level as unit_level 
    FROM jam_honor jh 
    LEFT JOIN unit u ON jh.unit_id = u.id 
    ORDER BY u.nama, jh.status_kepegawaian
");
$jam_honors = $stmt->fetchAll();

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
    <title>Jam & Honor - SISFOPEMBDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
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
                            <a class="nav-link <?php echo $key === 'jam_honor' ? 'active' : ''; ?>" href="<?php echo $menu['file']; ?>">
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
                        <h5 class="mb-0">Jam & Honor</h5>
                        <div class="navbar-nav ms-auto">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#jamHonorModal">
                                <i class="fas fa-plus me-2"></i>Tambah Aturan
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
                                    <h3 class="text-white"><?php echo count($jam_honors); ?></h3>
                                    <p class="text-light mb-0">Total Aturan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-success">
                                <div class="card-body text-center">
                                    <?php 
                                    $maxHonor = !empty($jam_honors) ? max(array_column($jam_honors, 'honor_per_jam')) : 0;
                                    ?>
                                    <h6 class="text-white"><?php echo formatRupiah($maxHonor); ?></h6>
                                    <p class="text-light mb-0">Honor Tertinggi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-warning">
                                <div class="card-body text-center">
                                    <?php 
                                    $minHonor = !empty($jam_honors) ? min(array_column($jam_honors, 'honor_per_jam')) : 0;
                                    ?>
                                    <h6 class="text-white"><?php echo formatRupiah($minHonor); ?></h6>
                                    <p class="text-light mb-0">Honor Terendah</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card-info">
                                <div class="card-body text-center">
                                    <?php 
                                    $avgHonor = !empty($jam_honors) ? array_sum(array_column($jam_honors, 'honor_per_jam')) / count($jam_honors) : 0;
                                    ?>
                                    <h6 class="text-white"><?php echo formatRupiah($avgHonor); ?></h6>
                                    <p class="text-light mb-0">Rata-rata Honor</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-select" id="filterUnit">
                                        <option value="">Semua Unit</option>
                                        <?php foreach ($units as $unit): ?>
                                            <option value="<?php echo $unit['id']; ?>"><?php echo htmlspecialchars($unit['nama']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="filterStatus">
                                        <option value="">Semua Status</option>
                                        <option value="PNS">PNS</option>
                                        <option value="GTY">GTY</option>
                                        <option value="PTY">PTY</option>
                                        <option value="Honorer">Honorer</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="searchJam" placeholder="Cari unit atau status...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Honor Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Aturan Jam & Honor</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="jamHonorTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Unit Kerja</th>
                                            <th>Status Kepegawaian</th>
                                            <th>Jam Wajib</th>
                                            <th>Honor per Jam</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jam_honors as $index => $jh): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-building text-muted me-2"></i>
                                                        <div>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($jh['unit_nama']); ?></div>
                                                            <small class="text-muted"><?php echo $jh['unit_level']; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo getStatusBadge($jh['status_kepegawaian']); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $jh['jam_wajib']; ?> jam</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-success">
                                                        <?php echo formatRupiah($jh['honor_per_jam']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="editJamHonor(<?php echo htmlspecialchars(json_encode($jh)); ?>)"
                                                            title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteJamHonor(<?php echo $jh['id']; ?>, '<?php echo htmlspecialchars($jh['unit_nama'] . ' - ' . $jh['status_kepegawaian']); ?>')"
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

    <!-- Jam Honor Modal -->
    <div class="modal fade" id="jamHonorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="jamHonorForm" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Aturan Jam & Honor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="add">
                        <input type="hidden" name="id" id="jamHonorId">
                        
                        <div class="mb-3">
                            <label for="unit_id" class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                            <select class="form-select" id="unit_id" name="unit_id" required>
                                <option value="">Pilih Unit</option>
                                <?php foreach ($units as $unit): ?>
                                    <option value="<?php echo $unit['id']; ?>"><?php echo htmlspecialchars($unit['nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status_kepegawaian" class="form-label">Status Kepegawaian <span class="text-danger">*</span></label>
                            <select class="form-select" id="status_kepegawaian" name="status_kepegawaian" required>
                                <option value="">Pilih Status</option>
                                <option value="PNS">PNS</option>
                                <option value="GTY">GTY</option>
                                <option value="PTY">PTY</option>
                                <option value="Honorer">Honorer</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="jam_wajib" class="form-label">Jam Wajib <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="jam_wajib" name="jam_wajib" min="0" required>
                                <span class="input-group-text">jam</span>
                            </div>
                            <div class="form-text">Masukkan 0 untuk status yang tidak memiliki jam wajib (misal: Honorer)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="honor_per_jam" class="form-label">Honor per Jam <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="honor_per_jam" name="honor_per_jam" placeholder="25,000" required>
                            </div>
                            <div class="form-text">Gunakan koma untuk pemisah ribuan (contoh: 25,000)</div>
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
                        <p>Apakah Anda yakin ingin menghapus aturan <strong id="deleteName"></strong>?</p>
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

        function editJamHonor(jamHonor) {
            document.getElementById('modalTitle').textContent = 'Edit Aturan Jam & Honor';
            document.getElementById('action').value = 'edit';
            document.getElementById('jamHonorId').value = jamHonor.id;
            document.getElementById('unit_id').value = jamHonor.unit_id;
            document.getElementById('status_kepegawaian').value = jamHonor.status_kepegawaian;
            document.getElementById('jam_wajib').value = jamHonor.jam_wajib;
            document.getElementById('honor_per_jam').value = formatNumberWithComma(jamHonor.honor_per_jam);
            
            new bootstrap.Modal(document.getElementById('jamHonorModal')).show();
        }

        function deleteJamHonor(id, nama) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteName').textContent = nama;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Reset form when modal is hidden
        document.getElementById('jamHonorModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('modalTitle').textContent = 'Tambah Aturan Jam & Honor';
            document.getElementById('action').value = 'add';
            document.getElementById('jamHonorForm').reset();
        });

        // Filter functionality
        document.getElementById('filterUnit').addEventListener('change', filterTable);
        document.getElementById('filterStatus').addEventListener('change', filterTable);
        document.getElementById('searchJam').addEventListener('input', filterTable);

        function filterTable() {
            const unitFilter = document.getElementById('filterUnit').value;
            const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
            const searchFilter = document.getElementById('searchJam').value.toLowerCase();
            const table = document.getElementById('jamHonorTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                
                if (cells.length > 0) {
                    const unit = cells[1].textContent.toLowerCase();
                    const status = cells[2].textContent.toLowerCase();
                    
                    const matchUnit = !unitFilter || row.dataset.unitId === unitFilter;
                    const matchStatus = !statusFilter || status.includes(statusFilter);
                    const matchSearch = !searchFilter || unit.includes(searchFilter) || status.includes(searchFilter);
                    
                    row.style.display = matchUnit && matchStatus && matchSearch ? '' : 'none';
                }
            }
        }

        // Auto-format honor per jam input
        document.addEventListener('DOMContentLoaded', function() {
            const honorInput = document.getElementById('honor_per_jam');
            
            if (honorInput) {
                // Format on input
                honorInput.addEventListener('input', function(e) {
                    let value = parseFormattedNumber(e.target.value);
                    if (value) {
                        e.target.value = formatNumberWithComma(value);
                    }
                });

                // Format on blur
                honorInput.addEventListener('blur', function(e) {
                    let value = parseFormattedNumber(e.target.value);
                    if (value) {
                        e.target.value = formatNumberWithComma(value);
                    }
                });

                // Handle form submission - convert back to plain number
                const form = document.getElementById('jamHonorForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const honorValue = parseFormattedNumber(honorInput.value);
                        honorInput.value = honorValue;
                    });
                }
            }

            // Add unit_id as data attribute to rows for filtering
            const jamHonors = <?php echo json_encode($jam_honors); ?>;
            const rows = document.querySelectorAll('#jamHonorTable tbody tr');
            
            rows.forEach((row, index) => {
                if (jamHonors[index]) {
                    row.dataset.unitId = jamHonors[index].unit_id;
                }
            });
        });
    </script>
</body>
</html>
