<?php
require_once 'config.php';
require_once 'auth.php';

// Check authentication and permission
$auth = new Auth($pdo);
$auth->requireLogin();
$auth->requirePermission('tunjangan_formula');

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
}

// Handle form submission
if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'edit') {
            $stmt = $pdo->prepare("UPDATE tunjangan_formula SET nilai = ? WHERE id = ?");
            $stmt->execute([$_POST['nilai'], $_POST['id']]);
            $success = "Formula tunjangan berhasil diperbarui!";
        }
    }
}

// Get all tunjangan formula
$stmt = $pdo->query("SELECT * FROM tunjangan_formula ORDER BY id");
$formulas = $stmt->fetchAll();

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
    <title>Formula Tunjangan - SISFOPEMBDA</title>
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
                            <a class="nav-link <?php echo $key === 'tunjangan_formula' ? 'active' : ''; ?>" href="<?php echo $menu['file']; ?>">
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
                        <h5 class="mb-0">Formula Tunjangan</h5>
                        <div class="navbar-nav ms-auto">
                            <span class="navbar-text text-muted">
                                <i class="fas fa-info-circle me-2"></i>
                                Konfigurasi perhitungan tunjangan
                            </span>
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

                    <!-- Info Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-percentage fa-2x text-white mb-2"></i>
                                    <h6 class="text-white">Tunjangan Keluarga</h6>
                                    <p class="text-light mb-0">Berdasarkan % gaji pokok</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stat-card-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-child fa-2x text-white mb-2"></i>
                                    <h6 class="text-white">Tunjangan Anak</h6>
                                    <p class="text-light mb-0">Berdasarkan % gaji pokok</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stat-card-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-seedling fa-2x text-white mb-2"></i>
                                    <h6 class="text-white">Tunjangan Beras</h6>
                                    <p class="text-light mb-0">Nominal tetap</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formula Cards -->
                    <div class="row">
                        <?php foreach ($formulas as $formula): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0 text-capitalize">
                                            <i class="fas fa-<?php echo $formula['nama'] == 'keluarga' ? 'heart' : ($formula['nama'] == 'anak' ? 'baby' : 'wheat'); ?> me-2"></i>
                                            Tunjangan <?php echo ucfirst($formula['nama']); ?>
                                        </h6>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="editFormula(<?php echo htmlspecialchars(json_encode($formula)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Tipe Perhitungan</label>
                                            <div>
                                                <span class="badge bg-<?php echo $formula['tipe'] == 'persen' ? 'warning' : 'info'; ?>">
                                                    <?php echo $formula['tipe'] == 'persen' ? 'Persentase' : 'Nominal Tetap'; ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Nilai</label>
                                            <div class="h4 text-primary">
                                                <?php if ($formula['tipe'] == 'persen'): ?>
                                                    <?php echo $formula['nilai']; ?>%
                                                <?php else: ?>
                                                    <?php echo formatRupiah($formula['nilai']); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="form-label small text-muted">Deskripsi</label>
                                            <p class="small">
                                                <?php if ($formula['nama'] == 'keluarga'): ?>
                                                    Tunjangan diberikan <?php echo $formula['nilai']; ?>% dari gaji pokok untuk pegawai berstatus menikah.
                                                <?php elseif ($formula['nama'] == 'anak'): ?>
                                                    Tunjangan diberikan <?php echo $formula['nilai']; ?>% dari gaji pokok per anak (maksimal 2 anak).
                                                <?php else: ?>
                                                    Tunjangan beras sebesar <?php echo formatRupiah($formula['nilai']); ?> per bulan untuk semua pegawai.
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Calculation Example -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calculator me-2"></i>
                                Contoh Perhitungan Tunjangan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">Asumsi Data Pegawai:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Gaji Pokok:</strong> Rp 3.000.000</li>
                                        <li><strong>Status:</strong> Menikah</li>
                                        <li><strong>Jumlah Anak:</strong> 2 orang</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success">Perhitungan Tunjangan:</h6>
                                    <ul class="list-unstyled" id="calculationExample">
                                        <!-- Will be populated by JavaScript -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Formula Modal -->
    <div class="modal fade" id="formulaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formulaForm" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Edit Formula Tunjangan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="formulaId">
                        
                        <div class="mb-3">
                            <label for="formulaNama" class="form-label">Jenis Tunjangan</label>
                            <input type="text" class="form-control" id="formulaNama" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="formulaTipe" class="form-label">Tipe Perhitungan</label>
                            <input type="text" class="form-control" id="formulaTipe" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nilai" class="form-label">Nilai <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="nilai" name="nilai" step="0.01" min="0" required>
                                <span class="input-group-text" id="nilaiUnit">%</span>
                            </div>
                            <div class="form-text" id="nilaiHelp">
                                Masukkan nilai tanpa simbol % atau Rp
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Catatan:</strong> Perubahan formula akan mempengaruhi perhitungan tunjangan untuk semua penugasan yang akan datang.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const formulas = <?php echo json_encode($formulas); ?>;
        
        function editFormula(formula) {
            document.getElementById('modalTitle').textContent = 'Edit Formula Tunjangan ' + formula.nama.charAt(0).toUpperCase() + formula.nama.slice(1);
            document.getElementById('formulaId').value = formula.id;
            document.getElementById('formulaNama').value = 'Tunjangan ' + formula.nama.charAt(0).toUpperCase() + formula.nama.slice(1);
            document.getElementById('formulaTipe').value = formula.tipe === 'persen' ? 'Persentase dari gaji pokok' : 'Nominal tetap';
            document.getElementById('nilai').value = formula.nilai;
            
            // Update unit and help text based on type
            const unitSpan = document.getElementById('nilaiUnit');
            const helpText = document.getElementById('nilaiHelp');
            
            if (formula.tipe === 'persen') {
                unitSpan.textContent = '%';
                helpText.textContent = 'Masukkan nilai persentase (contoh: 10 untuk 10%)';
                document.getElementById('nilai').step = '0.01';
            } else {
                unitSpan.textContent = 'Rp';
                helpText.textContent = 'Masukkan nominal dalam rupiah (contoh: 50000 untuk Rp 50.000)';
                document.getElementById('nilai').step = '1000';
            }
            
            new bootstrap.Modal(document.getElementById('formulaModal')).show();
        }

        // Calculate example
        function updateCalculationExample() {
            const gajiPokok = 3000000;
            const keluargaFormula = formulas.find(f => f.nama === 'keluarga');
            const anakFormula = formulas.find(f => f.nama === 'anak');
            const berasFormula = formulas.find(f => f.nama === 'beras');
            
            let calculation = '';
            let total = 0;
            
            if (keluargaFormula) {
                const tunjanganKeluarga = (gajiPokok * keluargaFormula.nilai) / 100;
                calculation += `<li><strong>Tunjangan Keluarga:</strong> ${keluargaFormula.nilai}% × Rp 3.000.000 = Rp ${tunjanganKeluarga.toLocaleString('id-ID')}</li>`;
                total += tunjanganKeluarga;
            }
            
            if (anakFormula) {
                const tunjanganAnak = (gajiPokok * anakFormula.nilai / 100) * 2;
                calculation += `<li><strong>Tunjangan Anak:</strong> ${anakFormula.nilai}% × Rp 3.000.000 × 2 = Rp ${tunjanganAnak.toLocaleString('id-ID')}</li>`;
                total += tunjanganAnak;
            }
            
            if (berasFormula) {
                calculation += `<li><strong>Tunjangan Beras:</strong> Rp ${parseInt(berasFormula.nilai).toLocaleString('id-ID')}</li>`;
                total += parseInt(berasFormula.nilai);
            }
            
            calculation += `<li class="border-top pt-2 mt-2"><strong class="text-success">Total Tunjangan:</strong> <span class="h6 text-success">Rp ${total.toLocaleString('id-ID')}</span></li>`;
            
            document.getElementById('calculationExample').innerHTML = calculation;
        }

        // Update calculation on page load
        document.addEventListener('DOMContentLoaded', updateCalculationExample);

        // Format number input
        document.getElementById('nilai').addEventListener('input', function(e) {
            const formula = formulas.find(f => f.id == document.getElementById('formulaId').value);
            if (formula && formula.tipe === 'fixed') {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value;
            }
        });

        // Update calculation after form submission
        document.getElementById('formulaForm').addEventListener('submit', function() {
            setTimeout(updateCalculationExample, 1000);
        });
    </script>
</body>
</html>
