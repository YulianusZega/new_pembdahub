<?php
require_once 'config.php';
require_once 'auth.php';

// Local helper for clean inline Rupiah formatting (avoids HTML span layout used by global formatRupiah)
if (!function_exists('formatRupiahInline')) {
    function formatRupiahInline($angka) {
        if ($angka === null || $angka === '' || !is_numeric($angka)) return 'Rp 0';
        return 'Rp ' . number_format((int)$angka, 0, ',', '.');
    }
}

// Cek autentikasi dan izin
$auth = new Auth($pdo);
$auth->requireLogin();
$auth->requirePermission('evaluasi');

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
}

// Dapatkan item menu dan info pengguna
$menuItems = getMenuItems();
$userRole = $auth->getRole();
$fullName = $auth->getFullName();

$unitId = '';
$tahunPelajaran = '';
// Kolom bulan sudah dihapus (model tahunan)
$pendapatanData = [];
$totalPendapatan = 0;
$totalPengeluaran = 0; // Total belanja (gaji + otorisasi + belanja yayasan manual)
$totalSiswa = 0;
$labaBersih = 0;
$belanjaYayasanManual = 0; // input manual tambahan
$belanjaGaji = 0; // diset saat kalkulasi
$otorisasiTambahan = 0; // diset saat kalkulasi
$isCalculated = false;
$error = '';
$success = '';
$isYayasan = false; // Flag untuk Yayasan
$gajiPerUnit = []; // Rincian gaji per unit (khusus ketika pilih Yayasan)

// Handle delete
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM evaluasi_unit WHERE id = ?");
        $stmt->execute([$deleteId]);
        $success = "Data evaluasi berhasil dihapus.";
    } catch (Exception $e) {
        $error = "Gagal menghapus data: " . $e->getMessage();
    }
}

// Data historis untuk riwayat tabel
$riwayatData = [];
try {
    $riwayatStmt = $pdo->query("SELECT e.*, u.nama as unit_nama, u.level as unit_level FROM evaluasi_unit e JOIN unit u ON e.unit_id = u.id ORDER BY e.tahun_pelajaran DESC");
    $riwayatData = $riwayatStmt->fetchAll();
} catch (Exception $e) {
    $error = "Gagal mengambil data riwayat: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isCalculated = true;
    $unitId = isset($_POST['unit_id']) ? (int)$_POST['unit_id'] : 0;
    $tahunPelajaran = isset($_POST['tahun_pelajaran']) ? trim($_POST['tahun_pelajaran']) : '';
    if (!$unitId || !$tahunPelajaran) {
        $error = 'Unit dan Tahun Pelajaran wajib diisi.';
    } else {
        // Dapatkan level unit
        $stmtUnit = $pdo->prepare("SELECT level,nama FROM unit WHERE id = ?");
        $stmtUnit->execute([$unitId]);
        $resUnit = $stmtUnit->fetch();
        if (!$resUnit) {
            $error = 'Unit tidak ditemukan.';
        } else {
            $selectedUnitLevel = $resUnit['level'];
            $isYayasan = ($selectedUnitLevel === 'Yayasan');

            // Reset hasil
            $pendapatanData = []; $totalPendapatan=0; $totalSiswa=0; $belanjaGaji=0; $otorisasiTambahan=0; $belanjaYayasanManual=0; $totalPengeluaran=0; $labaBersih=0;

            // Hitung pendapatan & siswa
            if ($isYayasan) {
                // Agregasi semua unit non-yayasan
                $q = "SELECT SUM(jumlah_siswa*uang_sekolah) AS total_p, SUM(jumlah_siswa) AS total_s FROM pendapatan_unit_kelas puk JOIN unit u ON u.id=puk.unit_id WHERE puk.tahun_pelajaran=? AND u.level <> 'Yayasan'";
                $st = $pdo->prepare($q); $st->execute([$tahunPelajaran]); $r=$st->fetch();
                $totalPendapatan = (int)($r['total_p']??0);
                $totalSiswa = (int)($r['total_s']??0);
                // Input manual belanja yayasan
                $belanjaYayasanManual = isset($_POST['belanja_yayasan_manual']) ? (int)preg_replace('/[^0-9]/','', $_POST['belanja_yayasan_manual']) : 0;
            } else {
                $q = "SELECT kelas,jumlah_siswa,uang_sekolah,(jumlah_siswa*uang_sekolah) AS pendapatan FROM pendapatan_unit_kelas WHERE unit_id=? AND tahun_pelajaran=? ORDER BY FIELD(kelas,'VII','VIII','IX','X','XI','XII')";
                $st = $pdo->prepare($q); $st->execute([$unitId,$tahunPelajaran]);
                foreach($st->fetchAll(PDO::FETCH_ASSOC) as $row){
                    $pendapatanData[$row['kelas']] = [
                        'jumlah_siswa'=>(int)$row['jumlah_siswa'],
                        'uang_sekolah'=>(int)$row['uang_sekolah'],
                        'pendapatan'=>(int)$row['pendapatan']
                    ];
                    $totalPendapatan += (int)$row['pendapatan'];
                    $totalSiswa += (int)$row['jumlah_siswa'];
                }
            }

            // Hitung belanja gaji (hanya baris sesuai tahun pelajaran)
            if ($isYayasan) {
                // Total seluruh gaji
                $stG = $pdo->prepare("SELECT SUM(total) FROM penugasan WHERE tahun_pelajaran=?");
                $stG->execute([$tahunPelajaran]);
                $belanjaGaji = (int)$stG->fetchColumn();
                // Rincian per unit
                $stGUnit = $pdo->prepare("SELECT u.id, u.nama, SUM(p.total) AS total_gaji FROM penugasan p JOIN unit u ON u.id=p.unit_id WHERE p.tahun_pelajaran=? GROUP BY u.id,u.nama ORDER BY u.nama");
                $stGUnit->execute([$tahunPelajaran]);
                $gajiPerUnit = $stGUnit->fetchAll();
            } else {
                $stG = $pdo->prepare("SELECT SUM(total) FROM penugasan WHERE unit_id=? AND tahun_pelajaran=?");
                $stG->execute([$unitId,$tahunPelajaran]);
                $belanjaGaji = (int)$stG->fetchColumn();
            }

            // Hitung otorisasi
            try {
                if ($isYayasan) {
                    $stO = $pdo->prepare("SELECT SUM(nilai) FROM otorisasi WHERE tahun_pelajaran=?");
                    $stO->execute([$tahunPelajaran]);
                } else {
                    $stO = $pdo->prepare("SELECT SUM(nilai) FROM otorisasi WHERE unit_id=? AND tahun_pelajaran=?");
                    $stO->execute([$unitId,$tahunPelajaran]);
                }
                $otorisasiTambahan = (int)$stO->fetchColumn();
            } catch(Exception $e) {
                $otorisasiTambahan = 0; // tabel mungkin belum ada
            }

            // Total belanja & laba
            $totalPengeluaran = $belanjaGaji + $otorisasiTambahan + ($isYayasan ? $belanjaYayasanManual : 0);
            $labaBersih = $totalPendapatan - $totalPengeluaran;

            // Simpan
            $dataToSave = [
                'unit_id'=>$unitId,
                'tahun_pelajaran'=>$tahunPelajaran,
                'total_pendapatan'=>$totalPendapatan,
                'total_pengeluaran_gaji'=>$totalPengeluaran,
                'laba_bersih'=>$labaBersih,
                'belanja_yayasan_manual'=>$belanjaYayasanManual,
                'otorisasi_total'=>$otorisasiTambahan
            ];
            try {
                $checkStmt = $pdo->prepare("SELECT id FROM evaluasi_unit WHERE unit_id = ? AND tahun_pelajaran = ?");
                $checkStmt->execute([$unitId, $tahunPelajaran]);
                $existingId = $checkStmt->fetchColumn();
                if ($existingId) {
                    $updateFields = []; $updateValues = [];
                    foreach ($dataToSave as $k=>$v){ $updateFields[]="`$k`=?"; $updateValues[]=$v; }
                    $updateValues[] = $existingId;
                    $sql = "UPDATE evaluasi_unit SET ".implode(',', $updateFields)." WHERE id=?";
                    $st = $pdo->prepare($sql); $st->execute($updateValues);
                    $success='Data evaluasi tahunan diperbarui!';
                } else {
                    $cols = "`".implode('`,`', array_keys($dataToSave))."`"; $ph = implode(',', array_fill(0,count($dataToSave),'?'));
                    $sql = "INSERT INTO evaluasi_unit ($cols) VALUES ($ph)";
                    $st = $pdo->prepare($sql); $st->execute(array_values($dataToSave));
                    $success='Data evaluasi tahunan disimpan!';
                }
                $riwayatStmt = $pdo->query("SELECT e.*, u.nama as unit_nama, u.level as unit_level FROM evaluasi_unit e JOIN unit u ON e.unit_id = u.id ORDER BY e.tahun_pelajaran DESC");
                $riwayatData = $riwayatStmt->fetchAll();
            } catch(Exception $e){ $error='Gagal menyimpan data evaluasi: '.$e->getMessage(); }
        }
    }
}

// Dapatkan unit untuk dropdown filter
$stmt = $pdo->query("SELECT * FROM unit ORDER BY nama");
$units = $stmt->fetchAll();

// Dapatkan level unit yang dipilih untuk ditampilkan di form
$selectedUnitLevel = '';
if ($unitId) {
    $stmt = $pdo->prepare("SELECT level FROM unit WHERE id = ?");
    $stmt->execute([$unitId]);
    $result = $stmt->fetch();
    if ($result) {
        $selectedUnitLevel = $result['level'];
    }
}

    // (Removed earlier experimental AJAX block to restore clean page flow)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Keuangan - SISFOPEMBDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet">
    <style>
        .class-inputs {
            display: none;
        }
    /* Styling untuk rincian angka agar lebih besar & jelas */
    .rincian-table th, .rincian-table td { vertical-align: middle; }
    .rincian-table td.amount { text-align: right; font-weight:600; color:#0d6efd; }
    .rincian-table td.label { font-size:1rem; }
    .rincian-highlight { font-size:1.05rem; font-weight:600; }
    /* Simplified revert: only enlarge font & right align numbers with color */
    .total-row { background:#f8f9fa; font-weight:600; }
    .amount-positive { color:#198754; }
    .amount-negative { color:#dc3545; }
    .rincian-simple li { font-size:1.05rem; }
    /* Restored enhanced styling (Option B) */
    .section-block { margin-bottom:1.5rem; }
    .section-title { font-weight:600; letter-spacing:.5px; text-transform:uppercase; font-size:.85rem; opacity:.85; }
    .summary-pill { display:inline-block; padding:.35rem .9rem; border-radius:2rem; font-weight:600; font-size:.8rem; }
    .summary-pill.income { background:#e6f6ed; color:#198754; }
    .summary-pill.expense { background:#fdeceb; color:#c2392b; }
    .summary-pill.net-positive { background:#e0ebff; color:#0d47a1; }
    .summary-pill.net-negative { background:#fff4e0; color:#a15c0d; }
    .rincian-table-income td.amount { color:#198754; }
    .rincian-table-expense td.amount { color:#dc3545; }
    .rincian-table-income thead th { background:linear-gradient(90deg,#e9f9f1,#d1f2e2); }
    .rincian-table-expense thead th { background:linear-gradient(90deg,#fdeceb,#fad3d1); }
    .divider-soft { border-top:1px dashed #ced4da; margin:1rem 0; }
    </style>
</head>
<body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-lg-2 sidebar bg-dark text-white min-vh-100 p-0">
                    <div class="p-3 border-bottom border-secondary">
                        <h4 class="mb-0">SISFOPEMBDA</h4>
                        <small>Sistem Informasi Administrasi</small>
                    </div>
                    <div class="p-3 border-bottom border-secondary small">
                        <div class="fw-bold"><?php echo htmlspecialchars($fullName); ?></div>
                        <div class="text-muted">Role: <?php echo htmlspecialchars($userRole); ?></div>
                    </div>
                    <nav class="nav flex-column">
                        <?php foreach ($menuItems as $key => $menu): ?>
                            <a class="nav-link text-white <?php echo $key==='evaluasi'?'active':''; ?>" href="<?php echo $menu['file']; ?>"> <i class="<?php echo $menu['icon']; ?> me-2"></i><?php echo $menu['title']; ?></a>
                        <?php endforeach; ?>
                        <hr class="border-secondary">
                        <a class="nav-link text-danger" href="?logout=1"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </nav>
                </div>
                <div class="col-md-9 col-lg-10 main-content">
                    <nav class="navbar navbar-light bg-white shadow-sm mb-3">
                        <div class="container-fluid"><h5 class="mb-0">Evaluasi Keuangan</h5></div>
                    </nav>
                    <div class="container-fluid">
                        <?php if ($error): ?><div class="alert alert-danger py-2"><?php echo $error; ?></div><?php endif; ?>
                        <?php if ($success): ?><div class="alert alert-success py-2"><?php echo $success; ?></div><?php endif; ?>
                        <div class="card mb-4">
                            <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-calculator me-2"></i>Hitung Laba/Rugi per Unit</h5></div>
                            <div class="card-body">
                                <form method="POST" class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Unit Sekolah</label>
                                        <select name="unit_id" class="form-select" required>
                                            <option value="">Pilih Unit</option>
                                            <?php foreach($units as $u): ?>
                                                <option value="<?php echo $u['id']; ?>" <?php echo $u['id']==$unitId?'selected':''; ?>><?php echo htmlspecialchars($u['nama']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tahun Pelajaran</label>
                                        <select name="tahun_pelajaran" class="form-select" required>
                                            <?php $tahunOptions=['2025/2026','2026/2027','2027/2028']; foreach($tahunOptions as $tp): ?>
                                                <option value="<?php echo $tp; ?>" <?php echo $tahunPelajaran==$tp?'selected':''; ?>><?php echo $tp; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <a href="pendapatan_tahunan.php" target="_blank" class="btn btn-outline-secondary w-100"><i class="fas fa-database me-2"></i>Kelola Data Siswa & Uang Sekolah</a>
                                    </div>
                                    <?php if ($unitId && $isYayasan): ?>
                                    <div class="col-12">
                                        <label class="form-label">Belanja Yayasan (Manual)</label>
                                        <input type="text" name="belanja_yayasan_manual" class="form-control" value="<?php echo isset($_POST['belanja_yayasan_manual'])?htmlspecialchars($_POST['belanja_yayasan_manual']):''; ?>" placeholder="Rp">
                                        <small class="text-muted">Biaya operasional lain-lain Yayasan (opsional).</small>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100"><i class="fas fa-check me-2"></i>Hitung & Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Reverted simpler Rincian Perhitungan block intentionally left out (already present later in file) -->
                    
                    <?php if ($isCalculated): ?>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center text-white bg-info">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Siswa</h5>
                                        <p class="h3"><?php echo number_format($totalSiswa); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center text-white bg-success">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Pendapatan</h5>
                                        <p class="h3"><?php echo formatRupiahInline($totalPendapatan); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center text-white bg-danger">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Belanja</h5>
                                        <p class="h3"><?php echo formatRupiahInline($totalPengeluaran); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center text-white bg-<?php echo $labaBersih >= 0 ? 'primary' : 'warning'; ?>">
                                    <div class="card-body">
                                        <h5 class="card-title">Laba Bersih</h5>
                                        <p class="h3"><?php echo formatRupiahInline($labaBersih); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-white border-0 pb-0">
                                <h5 class="card-title mb-0"><i class="fas fa-list-alt me-2 text-primary"></i>Rincian Perhitungan</h5>
                            </div>
                            <div class="card-body pt-3">
                                <div class="row mb-3 small text-uppercase fw-bold text-muted">
                                    <div class="col-md-6">Unit: <span class="text-dark"><?php echo htmlspecialchars($units[array_search($unitId, array_column($units, 'id'))]['nama']); ?></span></div>
                                    <div class="col-md-6">Tahun: <span class="text-dark"><?php echo htmlspecialchars($tahunPelajaran); ?></span></div>
                                </div>
                                <div class="section-block">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="section-title text-success me-2"><i class="fas fa-arrow-up"></i> Pendapatan</span>
                                        <span class="summary-pill income ms-auto">Total: <?php echo formatRupiahInline($totalPendapatan); ?></span>
                                    </div>
                                    <?php if ($isYayasan): ?>
                                        <div class="alert alert-success py-2 mb-2">
                                            Total pendapatan adalah agregat seluruh unit sekolah non-Yayasan.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered rincian-table rincian-table-income mb-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width:15%">Kelas</th>
                                                        <th style="width:15%" class="text-center">Siswa</th>
                                                        <th style="width:30%" class="text-end">Uang Sekolah</th>
                                                        <th style="width:40%" class="text-end">Pendapatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($pendapatanData as $kelas => $data): ?>
                                                        <tr>
                                                            <td class="fw-bold"><?php echo $kelas; ?></td>
                                                            <td class="text-center"><?php echo $data['jumlah_siswa']; ?></td>
                                                            <td class="amount"><?php echo formatRupiahInline($data['uang_sekolah']); ?></td>
                                                            <td class="amount"><?php echo formatRupiahInline($data['pendapatan']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    <tr class="total-row">
                                                        <td colspan="3" class="text-end">TOTAL</td>
                                                        <td class="amount"><?php echo formatRupiahInline($totalPendapatan); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="divider-soft"></div>
                                <div class="section-block">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="section-title text-danger me-2"><i class="fas fa-arrow-down"></i> Belanja</span>
                                        <span class="summary-pill expense ms-auto">Total: <?php echo formatRupiahInline($totalPengeluaran); ?></span>
                                    </div>
                                    <div class="table-responsive mb-2">
                                        <table class="table table-sm table-bordered rincian-table rincian-table-expense mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Komponen</th>
                                                    <th class="text-end">Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold">Belanja Gaji</td>
                                                    <td class="amount"><?php echo formatRupiahInline($belanjaGaji); ?></td>
                                                </tr>
                                                <?php if ($isYayasan && !empty($gajiPerUnit)): ?>
                                                    <tr>
                                                        <td colspan="2" class="p-0">
                                                            <table class="table table-sm mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th class="ps-4">Rincian Gaji per Unit</th>
                                                                        <th class="text-end pe-3">Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach($gajiPerUnit as $gu): ?>
                                                                        <tr>
                                                                            <td class="ps-4 small">- <?php echo htmlspecialchars($gu['nama']); ?></td>
                                                                            <td class="amount pe-3"><?php echo formatRupiahInline($gu['total_gaji']); ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td class="fw-bold">Otorisasi (Subsidi Operasional)</td>
                                                    <td class="amount"><?php echo formatRupiahInline($otorisasiTambahan); ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Belanja Manual Lain</td>
                                                    <td class="amount"><?php echo formatRupiahInline($belanjaYayasanManual); ?></td>
                                                </tr>
                                                <tr class="total-row">
                                                    <td class="text-end">TOTAL BELANJA</td>
                                                    <td class="amount"><?php echo formatRupiahInline($totalPengeluaran); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="divider-soft"></div>
                                <div class="text-center mt-3">
                                    <?php $netClass = $labaBersih >=0 ? 'net-positive' : 'net-negative'; ?>
                                    <span class="summary-pill <?php echo $netClass; ?>">
                                        Hasil: <?php echo formatRupiahInline($labaBersih); ?> (<?php echo $labaBersih >= 0 ? 'Laba' : 'Rugi'; ?>)
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Riwayat Evaluasi</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($riwayatData)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-table text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Tidak ada riwayat evaluasi</h5>
                                    <p class="text-muted">Silakan hitung dan simpan data evaluasi pertama Anda.</p>
                                </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Unit Sekolah</th>
                                            <th>Tahun</th>
                                            <th>Pendapatan</th>
                                            <th>Total Belanja</th>
                                            <th>Laba/Rugi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($riwayatData as $item): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($item['unit_nama']); ?></td>
                                            <td><?php echo htmlspecialchars($item['tahun_pelajaran']); ?></td>
                                            <td><?php echo formatRupiahInline($item['total_pendapatan']); ?></td>
                                            <td><?php echo formatRupiahInline($item['total_pengeluaran_gaji']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $item['laba_bersih'] >= 0 ? 'success' : 'danger'; ?>">
                                                    <?php echo formatRupiahInline($item['laba_bersih']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-info text-white" 
                                                            onclick="showDetail(<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="?delete_id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini? Aksi ini tidak dapat dibatalkan.')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div> <!-- /container-fluid inner -->
            </div> <!-- /main-content -->
        </div> <!-- /row -->
    </div> <!-- /container-fluid root -->

    <!-- Modal Detail Evaluasi -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detail Evaluasi Keuangan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailModalContent">
                    <!-- Content will be filled by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function showClassInputs() { /* annual model: no per-class inputs here */ }

        // Fungsi untuk menampilkan modal detail
        function showDetail(item) {
            let detailHtml = `
                <table class="table table-striped table-bordered">
                    <tbody>
                        <tr><th>Unit Sekolah</th><td>${item.unit_nama}</td></tr>
                        <tr><th>Tahun Pelajaran</th><td>${item.tahun_pelajaran}</td></tr>
                        
                    </tbody>
                </table>
                <h6 class="mt-4">Rincian Pendapatan</h6>
            `;
            
            const levels = {
                'smp': ['VII', 'VIII', 'IX'],
                'sma': ['X', 'XI', 'XII'],
                'smk': ['X', 'XI', 'XII']
            };
            
            const currentLevels = levels[item.unit_level.toLowerCase()] || [];

            if (item.unit_level.toLowerCase() === 'yayasan') {
                detailHtml += `
                    <p>Total pendapatan merupakan rekapitulasi dari seluruh unit sekolah.</p>
                `;
            } else {
                detailHtml += `
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Jumlah Siswa</th>
                                <th>Uang Sekolah</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                currentLevels.forEach(kelas => {
                    const siswaKey = 'siswa_kelas_' + kelas.toLowerCase();
                    const uangSekolahKey = 'uang_sekolah_kelas_' + kelas.toLowerCase();
                    
                    const jumlahSiswa = item[siswaKey] || 0;
                    const uangSekolah = item[uangSekolahKey] || 0;
                    const pendapatan = jumlahSiswa * uangSekolah;
    
                    if (jumlahSiswa > 0) {
                        detailHtml += `
                            <tr>
                                <td>Kelas ${kelas}</td>
                                <td>${jumlahSiswa}</td>
                                <td>${formatRupiah(uangSekolah)}</td>
                                <td>${formatRupiah(pendapatan)}</td>
                            </tr>
                        `;
                    }
                });
                detailHtml += `
                        </tbody>
                    </table>
                `;
            }

            detailHtml += `
                <h6 class="mt-4">Ringkasan Keuangan</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-success">
                            <strong>Total Pendapatan:</strong> ${formatRupiah(item.total_pendapatan)}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-danger">
                            <strong>Total Belanja:</strong> ${formatRupiah(item.total_pengeluaran_gaji)}
                        </div>
                    </div>
                </div>
                <div class="alert alert-${item.laba_bersih >= 0 ? 'primary' : 'warning'} mt-2">
                    <h4>Total Laba/Rugi Bersih: ${formatRupiah(item.laba_bersih)}</h4>
                </div>
            `;
            
            document.getElementById('detailModalContent').innerHTML = detailHtml;
            var detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            detailModal.show();
        }

        // Auto-format input mata uang
        document.addEventListener('DOMContentLoaded', () => {
            // No currency inputs to auto-format on this page now

            // Trigger saat halaman dimuat untuk menampilkan input yang sesuai jika ada data POST
            showClassInputs();
        });
        
        function formatRupiah(amount) {
            if (amount === null || amount === undefined || amount === '') return 'Rp 0';
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
        }
    </script>
</body>
</html>
