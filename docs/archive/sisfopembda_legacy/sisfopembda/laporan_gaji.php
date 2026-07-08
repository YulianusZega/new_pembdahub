<?php
require_once 'config.php';
require_once 'auth.php';

// Check authentication
$auth = new Auth($pdo);
$auth->requireLogin();

// Get available tahun pelajaran
$tahunStmt = $pdo->query("SELECT DISTINCT tahun_pelajaran FROM penugasan ORDER BY tahun_pelajaran DESC");
$tahunList = $tahunStmt->fetchAll();

// Get available units
$unitStmt = $pdo->query("SELECT id, nama FROM unit ORDER BY nama");
$unitList = $unitStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Daftar Gaji Pegawai</title>
    
        <link href="assets/css/dashboard.css" rel="stylesheet">
        <link href="assets/css/theme.css" rel="stylesheet">
        <style>
            .report-container{background:#fff;border-radius:10px;box-shadow:var(--shadow-elev-1);padding:30px;margin:20px 0;}
            .filter-section{background:var(--gradient-surface);border-radius:10px;padding:20px;margin-bottom:25px;box-shadow:0 2px 6px rgba(0,0,0,.05);} 
            .export-buttons{display:flex;gap:10px;margin-top:25px;flex-wrap:wrap;justify-content:center;}
            .export-buttons .btn-sisfo{min-width:150px;border-radius:30px;font-size:.7rem;padding:.65rem 1.2rem;text-transform:uppercase;letter-spacing:.5px;}
            .preview-section{max-height:500px;overflow-y:auto;border:1px solid #dee2e6;border-radius:8px;margin-top:20px;background:#fff;}
            .salary-table{font-size:.75rem;}
            .salary-table th{background:var(--gradient-dark);color:#fff;position:sticky;top:0;z-index:10;}
            .total-row{background:rgba(255,193,7,.25);font-weight:600;}
            .loading-overlay{position:absolute;inset:0;background:rgba(255,255,255,.85);display:flex;align-items:center;justify-content:center;z-index:1000;}
        </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="report-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-file-invoice-dollar me-2"></i>Laporan Daftar Gaji Pegawai</h2>
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <h5><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
                        <form id="filterForm">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="tahun_pelajaran" class="form-label">Tahun Pelajaran</label>
                                    <select name="tahun_pelajaran" id="tahun_pelajaran" class="form-select" required>
                                        <option value="">Pilih Tahun Pelajaran</option>
                                        <?php foreach ($tahunList as $tahun): ?>
                                            <option value="<?= htmlspecialchars($tahun['tahun_pelajaran']) ?>">
                                                <?= htmlspecialchars($tahun['tahun_pelajaran']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="unit_id" class="form-label">Unit Sekolah</label>
                                    <select name="unit_id" id="unit_id" class="form-select">
                                        <option value="">Semua Unit</option>
                                        <?php foreach ($unitList as $unit): ?>
                                            <option value="<?= $unit['id'] ?>">
                                                <?= htmlspecialchars($unit['nama']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="status_kepegawaian" class="form-label">Status Kepegawaian</label>
                                    <select name="status_kepegawaian" id="status_kepegawaian" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="PNS">PNS</option>
                                        <option value="GTY">GTY</option>
                                        <option value="Honorer">Honorer</option>
                                        <option value="PTY">PTY</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="show_details" name="show_details" checked>
                                        <label class="form-check-label" for="show_details">
                                            Tampilkan Detail Tunjangan
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="group_by_unit" name="group_by_unit" checked>
                                        <label class="form-check-label" for="group_by_unit">
                                            Kelompokkan Berdasarkan Unit
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="export-buttons">
                                <button type="button" class="btn-sisfo btn-sisfo-primary" onclick="generatePreview()" title="Preview">
                                    <i class="fas fa-filter"></i><span class="ms-1">Preview</span>
                                </button>
                                <button type="button" class="btn-sisfo btn-sisfo-success" onclick="exportToExcel()" title="Excel">
                                    <i class="fas fa-file-excel"></i><span class="ms-1">Excel</span>
                                </button>
                                <button type="button" class="btn-sisfo btn-sisfo-info" onclick="exportToCSV()" title="CSV">
                                    <i class="fas fa-file-csv"></i><span class="ms-1">CSV</span>
                                </button>
                                <button type="button" class="btn-sisfo btn-sisfo-warning" onclick="exportToPDF()" title="PDF">
                                    <i class="fas fa-file-pdf"></i><span class="ms-1">PDF</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Preview Section -->
                    <div id="previewSection" style="display: none;">
                        <h5><i class="fas fa-eye me-2"></i>Preview Laporan</h5>
                        <div class="preview-section position-relative">
                            <div id="loadingOverlay" class="loading-overlay" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div id="previewContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
        
        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }
        
        function getFormData() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }
            
            // Add checkboxes separately
            params.append('show_details', document.getElementById('show_details').checked ? '1' : '0');
            params.append('group_by_unit', document.getElementById('group_by_unit').checked ? '1' : '0');
            
            return params;
        }
        
        function generatePreview() {
            const tahunPelajaran = document.getElementById('tahun_pelajaran').value;
            if (!tahunPelajaran) {
                alert('Silakan pilih Tahun Pelajaran terlebih dahulu');
                return;
            }
            
            showLoading();
            
            const params = getFormData();
            params.append('action', 'preview');
            
            fetch('generate_salary_report.php?' + params.toString())
                .then(response => response.text())
                .then(html => {
                    document.getElementById('previewContent').innerHTML = html;
                    document.getElementById('previewSection').style.display = 'block';
                    hideLoading();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data preview');
                    hideLoading();
                });
        }
        
        function exportToExcel() {
            const tahunPelajaran = document.getElementById('tahun_pelajaran').value;
            if (!tahunPelajaran) {
                alert('Silakan pilih Tahun Pelajaran terlebih dahulu');
                return;
            }
            
            const params = getFormData();
            params.append('action', 'excel');
            
            window.open('generate_salary_report.php?' + params.toString(), '_blank');
        }
        
        function exportToCSV() {
            const tahunPelajaran = document.getElementById('tahun_pelajaran').value;
            if (!tahunPelajaran) {
                alert('Silakan pilih Tahun Pelajaran terlebih dahulu');
                return;
            }
            
            const params = getFormData();
            params.append('action', 'csv');
            
            window.open('generate_salary_report.php?' + params.toString(), '_blank');
        }
        
        function exportToPDF() {
            const tahunPelajaran = document.getElementById('tahun_pelajaran').value;
            if (!tahunPelajaran) {
                alert('Silakan pilih Tahun Pelajaran terlebih dahulu');
                return;
            }
            
            const params = getFormData();
            params.append('action', 'pdf');
            
            window.open('generate_salary_report.php?' + params.toString(), '_blank');
        }
        
        // Auto preview when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const filters = ['tahun_pelajaran', 'unit_id', 'status_kepegawaian', 'show_details', 'group_by_unit'];
            filters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    element.addEventListener('change', function() {
                        if (document.getElementById('tahun_pelajaran').value) {
                            setTimeout(generatePreview, 300);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
