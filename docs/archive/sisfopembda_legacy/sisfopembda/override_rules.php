<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'override_functions.php';

// Check authentication and permission
$auth = new Auth($pdo);
$auth->requireLogin();
$auth->requirePermission('pegawai'); // Menggunakan permission pegawai untuk sementara

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
}

// Handle form submission
if ($_POST) {
    try {
        if (isset($_POST['action'])) {
            if ($_POST['action'] == 'add_rule') {
                $stmt = $pdo->prepare("
                    INSERT INTO pegawai_override_rules (pegawai_id, rule_type, rule_value, reason, created_by) 
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    rule_value = VALUES(rule_value), 
                    reason = VALUES(reason), 
                    updated_at = CURRENT_TIMESTAMP,
                    is_active = TRUE
                ");
                $stmt->execute([
                    $_POST['pegawai_id'], 
                    $_POST['rule_type'], 
                    isset($_POST['rule_value']) && $_POST['rule_value'] !== '' ? $_POST['rule_value'] : NULL, 
                    $_POST['reason'],
                    $_SESSION['user_id']
                ]);
                
                // Auto-update penugasan yang terkena impact
                updatePenugasanForOverrideChanges($pdo, $_POST['pegawai_id']);
                
                $success = "Aturan khusus berhasil ditambahkan!";
            } elseif ($_POST['action'] == 'toggle_rule') {
                // Get pegawai_id sebelum toggle
                $ruleStmt = $pdo->prepare("SELECT pegawai_id FROM pegawai_override_rules WHERE id = ?");
                $ruleStmt->execute([$_POST['rule_id']]);
                $pegawaiId = $ruleStmt->fetchColumn();
                
                $stmt = $pdo->prepare("UPDATE pegawai_override_rules SET is_active = NOT is_active WHERE id = ?");
                $stmt->execute([$_POST['rule_id']]);
                
                // Auto-update penugasan yang terkena impact
                if ($pegawaiId) {
                    updatePenugasanForOverrideChanges($pdo, $pegawaiId);
                }
                
                $success = "Status aturan berhasil diubah!";
            } elseif ($_POST['action'] == 'delete_rule') {
                // Get pegawai_id sebelum delete
                $ruleStmt = $pdo->prepare("SELECT pegawai_id FROM pegawai_override_rules WHERE id = ?");
                $ruleStmt->execute([$_POST['rule_id']]);
                $pegawaiId = $ruleStmt->fetchColumn();
                
                $stmt = $pdo->prepare("DELETE FROM pegawai_override_rules WHERE id = ?");
                $stmt->execute([$_POST['rule_id']]);
                
                // Auto-update penugasan yang terkena impact
                if ($pegawaiId) {
                    updatePenugasanForOverrideChanges($pdo, $pegawaiId);
                }
                
                $success = "Aturan berhasil dihapus!";
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get pegawai list for dropdown
$pegawaiStmt = $pdo->query("
    SELECT p.id, p.nama, p.nomor_induk, p.status_kepegawaian, p.kelompok_pekerjaan, u.nama as unit_nama
    FROM pegawai p 
    LEFT JOIN unit u ON p.unit_id = u.id 
    ORDER BY p.nama
");
$pegawais = $pegawaiStmt->fetchAll(PDO::FETCH_ASSOC);

// Get override rules with pegawai info
$rulesStmt = $pdo->query("
    SELECT r.*, p.nama as pegawai_nama, p.nomor_induk, p.status_kepegawaian, 
           u.nama as unit_nama, creator.username as created_by_name
    FROM pegawai_override_rules r
    JOIN pegawai p ON r.pegawai_id = p.id
    LEFT JOIN unit u ON p.unit_id = u.id
    LEFT JOIN users creator ON r.created_by = creator.id
    ORDER BY p.nama, r.created_at DESC
");
$overrideRules = $rulesStmt->fetchAll(PDO::FETCH_ASSOC);

// Group rules by pegawai
$rulesByPegawai = [];
foreach ($overrideRules as $rule) {
    $rulesByPegawai[$rule['pegawai_id']][] = $rule;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aturan Khusus Pegawai - SISFOPEMBDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .rule-card {
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
        }
        .rule-type-badge {
            font-size: 0.8rem;
        }
        .pegawai-section {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }
        .pegawai-header {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            padding: 1rem;
            border-radius: 0.375rem 0.375rem 0 0;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-graduation-cap me-2"></i>SISFOPEMBDA
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="?logout=1">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-cogs me-2"></i>Aturan Khusus Pegawai</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRuleModal">
                        <i class="fas fa-plus me-1"></i>Tambah Aturan Khusus
                    </button>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Rules by Pegawai -->
                <?php if (empty($rulesByPegawai)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada aturan khusus</h5>
                        <p class="text-muted">Klik "Tambah Aturan Khusus" untuk membuat aturan baru</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($rulesByPegawai as $pegawaiId => $rules): ?>
                        <?php $pegawai = $rules[0]; // Data pegawai sama untuk semua rules ?>
                        <div class="pegawai-section">
                            <div class="pegawai-header">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-1">
                                            <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($pegawai['pegawai_nama']); ?>
                                        </h5>
                                        <small>
                                            <?php echo htmlspecialchars($pegawai['nomor_induk']); ?> | 
                                            <?php echo htmlspecialchars($pegawai['status_kepegawaian']); ?> | 
                                            <?php echo htmlspecialchars($pegawai['unit_nama']); ?>
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge bg-light text-dark">
                                            <?php echo count($rules); ?> aturan khusus
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <?php foreach ($rules as $rule): ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card rule-card h-100">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <span class="badge bg-<?php echo $rule['is_active'] ? 'success' : 'secondary'; ?> rule-type-badge">
                                                            <?php echo $rule['is_active'] ? 'AKTIF' : 'NONAKTIF'; ?>
                                                        </span>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <form method="POST" class="d-inline">
                                                                        <input type="hidden" name="action" value="toggle_rule">
                                                                        <input type="hidden" name="rule_id" value="<?php echo $rule['id']; ?>">
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fas fa-<?php echo $rule['is_active'] ? 'pause' : 'play'; ?> me-2"></i>
                                                                            <?php echo $rule['is_active'] ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Hapus aturan ini?')">
                                                                        <input type="hidden" name="action" value="delete_rule">
                                                                        <input type="hidden" name="rule_id" value="<?php echo $rule['id']; ?>">
                                                                        <button type="submit" class="dropdown-item text-danger">
                                                                            <i class="fas fa-trash me-2"></i>Hapus
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <h6 class="card-title">
                                                        <?php
                                                        $ruleNames = [
                                                            'no_tunjangan_anak' => 'Tidak Ada Tunjangan Anak',
                                                            'no_tunjangan_beras' => 'Tidak Ada Tunjangan Beras',
                                                            'no_tunjangan_keluarga' => 'Tidak Ada Tunjangan Keluarga',
                                                            'custom_tunjangan_anak_persen' => 'Custom % Tunjangan Anak',
                                                            'custom_tunjangan_keluarga_persen' => 'Custom % Tunjangan Keluarga',
                                                            'custom_tunjangan_beras_amount' => 'Custom Amount Tunjangan Beras',
                                                            'no_honor_calculation' => 'Tidak Ada Perhitungan Honor',
                                                            'custom_jam_wajib' => 'Custom Jam Wajib'
                                                        ];
                                                        echo $ruleNames[$rule['rule_type']] ?? $rule['rule_type'];
                                                        ?>
                                                    </h6>
                                                    <?php if ($rule['rule_value']): ?>
                                                        <p class="card-text">
                                                            <strong>Nilai:</strong> 
                                                            <?php 
                                                            if (strpos($rule['rule_type'], 'persen') !== false) {
                                                                echo $rule['rule_value'] . '%';
                                                            } elseif (strpos($rule['rule_type'], 'amount') !== false || strpos($rule['rule_type'], 'beras') !== false) {
                                                                echo 'Rp ' . number_format($rule['rule_value'], 0, ',', '.');
                                                            } else {
                                                                echo $rule['rule_value'];
                                                            }
                                                            ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    <?php if ($rule['reason']): ?>
                                                        <p class="card-text">
                                                            <small class="text-muted">
                                                                <strong>Alasan:</strong><br>
                                                                <?php echo htmlspecialchars($rule['reason']); ?>
                                                            </small>
                                                        </p>
                                                    <?php endif; ?>
                                                    <small class="text-muted">
                                                        Dibuat: <?php echo date('d/m/Y H:i', strtotime($rule['created_at'])); ?>
                                                        <?php if ($rule['created_by_name']): ?>
                                                            oleh <?php echo htmlspecialchars($rule['created_by_name']); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Rule Modal -->
    <div class="modal fade" id="addRuleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-plus me-2"></i>Tambah Aturan Khusus
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_rule">
                        
                        <div class="mb-3">
                            <label class="form-label">Pegawai <span class="text-danger">*</span></label>
                            <select name="pegawai_id" class="form-select" required>
                                <option value="">Pilih Pegawai</option>
                                <?php foreach ($pegawais as $pegawai): ?>
                                    <option value="<?php echo $pegawai['id']; ?>">
                                        <?php echo htmlspecialchars($pegawai['nama']); ?> 
                                        (<?php echo htmlspecialchars($pegawai['nomor_induk']); ?>) - 
                                        <?php echo htmlspecialchars($pegawai['status_kepegawaian']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Aturan <span class="text-danger">*</span></label>
                            <select name="rule_type" id="ruleType" class="form-select" required>
                                <option value="">Pilih Jenis Aturan</option>
                                <option value="no_tunjangan_anak">Tidak Ada Tunjangan Anak</option>
                                <option value="no_tunjangan_beras">Tidak Ada Tunjangan Beras</option>
                                <option value="no_tunjangan_keluarga">Tidak Ada Tunjangan Keluarga</option>
                                <option value="custom_tunjangan_anak_persen">Custom Persentase Tunjangan Anak</option>
                                <option value="custom_tunjangan_keluarga_persen">Custom Persentase Tunjangan Keluarga</option>
                                <option value="custom_tunjangan_beras_amount">Custom Jumlah Tunjangan Beras</option>
                                <option value="no_honor_calculation">Tidak Ada Perhitungan Honor</option>
                                <option value="custom_jam_wajib">Custom Jam Wajib</option>
                            </select>
                        </div>

                        <div class="mb-3" id="ruleValueDiv" style="display: none;">
                            <label class="form-label" id="ruleValueLabel">Nilai</label>
                            <input type="text" name="rule_value" id="ruleValue" class="form-control" placeholder="Masukkan nilai" min="0">
                            <div class="form-text" id="ruleValueHelp"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alasan Aturan Khusus</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Jelaskan alasan mengapa perlu aturan khusus ini..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Aturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('ruleType').addEventListener('change', function() {
            const ruleType = this.value;
            const ruleValueDiv = document.getElementById('ruleValueDiv');
            const ruleValueLabel = document.getElementById('ruleValueLabel');
            const ruleValueHelp = document.getElementById('ruleValueHelp');
            const ruleValueInput = document.getElementById('ruleValue');

            if (ruleType.includes('custom_')) {
                ruleValueDiv.style.display = 'block';
                ruleValueInput.required = true;

                if (ruleType.includes('persen')) {
                    ruleValueLabel.textContent = 'Persentase (%)';
                    ruleValueInput.type = 'text';
                    ruleValueInput.placeholder = 'Contoh: 2.5';
                    ruleValueInput.removeAttribute('min');
                    ruleValueInput.removeAttribute('max');
                    ruleValueInput.removeAttribute('step');
                    ruleValueHelp.textContent = 'Masukkan persentase tanpa simbol % (contoh: 2.5 untuk 2.5%)';
                } else if (ruleType.includes('amount') || ruleType.includes('beras')) {
                    ruleValueLabel.textContent = 'Jumlah (Rupiah)';
                    ruleValueInput.type = 'text';
                    ruleValueInput.placeholder = 'Contoh: 50000';
                    ruleValueInput.removeAttribute('min');
                    ruleValueInput.removeAttribute('max');
                    ruleValueInput.removeAttribute('step');
                    ruleValueHelp.textContent = 'Masukkan jumlah dalam rupiah tanpa titik atau koma';
                } else if (ruleType.includes('jam_wajib')) {
                    ruleValueLabel.textContent = 'Jam Wajib';
                    ruleValueInput.placeholder = 'Contoh: 0, 10, 24';
                    ruleValueInput.type = 'number';
                    ruleValueInput.min = '0';
                    ruleValueInput.max = '168'; // 24 jam × 7 hari = maksimal 168 jam per minggu
                    ruleValueInput.step = '1';
                    ruleValueHelp.textContent = 'Masukkan jumlah jam wajib per minggu (0-168 jam). Nilai 0 berarti tidak ada jam wajib.';
                }
            } else {
                ruleValueDiv.style.display = 'none';
                ruleValueInput.required = false;
                ruleValueInput.type = 'text';
                ruleValueInput.removeAttribute('min');
                ruleValueInput.removeAttribute('max');
                ruleValueInput.removeAttribute('step');
            }
        });
    </script>
</body>
</html>
