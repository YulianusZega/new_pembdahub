<?php

// Pastikan parameter secret dikirim dan benar
if (!isset($_GET['secret']) || $_GET['secret'] !== 'pembda99') {
    http_response_code(403);
    die('Akses ditolak: Secret token tidak valid.');
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$searchName = $_GET['name'] ?? 'abraham';

// Cari siswa dengan nama mirip
$students = App\Models\Student::where('full_name', 'like', '%' . $searchName . '%')->get();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Proposal Tugas Akhir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; padding-top: 50px; }
        .card { border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h3 class="card-title text-center mb-4 text-primary">Utility Hapus Proposal Tugas Akhir</h3>
                
                <form method="GET" class="mb-4">
                    <input type="hidden" name="secret" value="pembda99">
                    <div class="input-group">
                        <input type="text" name="name" class="form-control" placeholder="Cari nama siswa..." value="<?php echo htmlspecialchars($searchName); ?>">
                        <button class="btn btn-primary" type="submit">Cari Siswa</button>
                    </div>
                </form>

                <?php if ($students->isEmpty()): ?>
                    <div class="alert alert-warning">
                        Siswa dengan pencarian nama "<strong><?php echo htmlspecialchars($searchName); ?></strong>" tidak ditemukan.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>Proposal / Tugas Akhir Aktif</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): 
                                    $project = $student->currentFinalProject();
                                ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($student->full_name); ?></strong></td>
                                        <td><?php echo htmlspecialchars($student->nisn ?? '-'); ?></td>
                                        <td>
                                            <?php if ($project): ?>
                                                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($project->type); ?></span><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($project->title); ?></small><br>
                                                <small class="text-danger font-monospace">Status: <?php echo $project->status; ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">Tidak ada proposal aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($project): ?>
                                                <form action="" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen proposal dan seluruh logbook untuk siswa ini? Tindakan ini tidak dapat dibatalkan.');">
                                                    <input type="hidden" name="action" value="delete_project">
                                                    <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus Proposal</button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" disabled>Tidak Ada</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php
                // Proses POST Action untuk menghapus
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_project') {
                    $projectId = $_POST['project_id'];
                    $projectToDelete = App\Models\FinalProject::find($projectId);
                    
                    if ($projectToDelete) {
                        $deletedMembers = $projectToDelete->members()->delete();
                        $deletedLogs = $projectToDelete->logs()->delete();
                        $projectToDelete->delete();
                        
                        echo '<div class="alert alert-success mt-4">';
                        echo '<h5>Berhasil Dihapus!</h5>';
                        echo '<ul>';
                        echo '<li>Judul Proposal: <strong>' . htmlspecialchars($projectToDelete->title) . '</strong></li>';
                        echo '<li>Anggota kelompok dihapus: ' . $deletedMembers . '</li>';
                        echo '<li>Jurnal/Logbook dihapus: ' . $deletedLogs . '</li>';
                        echo '</ul>';
                        echo 'Siswa sekarang dapat masuk ke portal dan membuat pengajuan baru.';
                        echo '</div>';
                        echo '<script>setTimeout(function(){ window.location.reload(); }, 3000);</script>';
                    } else {
                        echo '<div class="alert alert-danger mt-4">Gagal: Proposal tidak ditemukan.</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
