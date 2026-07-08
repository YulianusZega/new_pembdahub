<?php
require_once 'config.php';
require_once 'auth.php';

// Require login and admin permission
$auth->requireLogin();
$auth->requirePermission('users');

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
}

$message = '';
$error = '';

// Handle form submission
if ($_POST) {
    try {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add') {
                $username = $_POST['username'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $full_name = $_POST['full_name'];
                $role = $_POST['role'];
                $unit_id = $_POST['unit_id'] ? $_POST['unit_id'] : null;
                
                $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role, unit_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $password, $full_name, $role, $unit_id]);
                $message = "User berhasil ditambahkan!";
                
            } elseif ($_POST['action'] === 'edit') {
                $id = $_POST['id'];
                $username = $_POST['username'];
                $full_name = $_POST['full_name'];
                $role = $_POST['role'];
                $unit_id = $_POST['unit_id'] ? $_POST['unit_id'] : null;
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                if (!empty($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET username=?, password=?, full_name=?, role=?, unit_id=?, is_active=? WHERE id=?");
                    $stmt->execute([$username, $password, $full_name, $role, $unit_id, $is_active, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET username=?, full_name=?, role=?, unit_id=?, is_active=? WHERE id=?");
                    $stmt->execute([$username, $full_name, $role, $unit_id, $is_active, $id]);
                }
                $message = "User berhasil diupdate!";
                
            } elseif ($_POST['action'] === 'delete') {
                $id = $_POST['id'];
                if ($id != $auth->getUserId()) { // Don't allow deleting self
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
                    $stmt->execute([$id]);
                    $message = "User berhasil dihapus!";
                } else {
                    $error = "Tidak dapat menghapus user yang sedang login!";
                }
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get all users
$stmt = $pdo->query("SELECT u.*, unit.nama as unit_nama FROM users u LEFT JOIN unit ON u.unit_id = unit.id ORDER BY u.created_at DESC");
$users = $stmt->fetchAll();

// Get units for dropdown
$stmt = $pdo->query("SELECT * FROM unit ORDER BY nama");
$units = $stmt->fetchAll();

// Get menu items
$menuItems = getMenuItems();
$userRole = $auth->getRole();
$fullName = $auth->getFullName();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - SISFOPEMBDA</title>
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
                                <div class="text-light small">Administrator</div>
                            </div>
                        </div>
                    </div>
                    
                    <nav class="nav flex-column mt-3">
                        <?php foreach ($menuItems as $key => $menu): ?>
                            <a class="nav-link <?php echo $key === 'users' ? 'active' : ''; ?>" href="<?php echo $menu['file']; ?>">
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
                <div class="container-fluid py-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2><i class="fas fa-user-cog me-2"></i>Kelola User</h2>
                            <p class="text-muted">Manajemen pengguna sistem SISFOPEMBDA</p>
                        </div>
                        <button class="btn-sisfo btn-sisfo-primary" data-bs-toggle="modal" data-bs-target="#addUserModal" title="Tambah User">
                            <i class="fas fa-plus"></i><span class="ms-1">Tambah</span>
                        </button>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Users Table -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover sisfo-table">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Nama Lengkap</th>
                                            <th>Role</th>
                                            <th>Unit</th>
                                            <th>Status</th>
                                            <th>Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                                <td>
                                                    <?php
                                                    $roleClasses = [
                                                        'admin' => 'bg-danger',
                                                        'operator_sekolah' => 'bg-warning',
                                                        'kepala_sekolah' => 'bg-info'
                                                    ];
                                                    $roleLabels = [
                                                        'admin' => 'Administrator',
                                                        'operator_sekolah' => 'Operator Sekolah',
                                                        'kepala_sekolah' => 'Kepala Sekolah'
                                                    ];
                                                    ?>
                                                    <span class="badge <?php echo $roleClasses[$user['role']] ?? 'bg-secondary'; ?>">
                                                        <?php echo $roleLabels[$user['role']] ?? $user['role']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo $user['unit_nama'] ? htmlspecialchars($user['unit_nama']) : '-'; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $user['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?php echo $user['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if ($user['id'] != $auth->getUserId()): ?>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
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

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="admin">Administrator</option>
                                <option value="operator_sekolah">Operator Sekolah</option>
                                <option value="kepala_sekolah">Kepala Sekolah</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit (Opsional)</label>
                            <select class="form-select" name="unit_id">
                                <option value="">Pilih Unit</option>
                                <?php foreach ($units as $unit): ?>
                                    <option value="<?php echo $unit['id']; ?>"><?php echo htmlspecialchars($unit['nama']); ?></option>
                                <?php endforeach; ?>
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

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editUserForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="edit_username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="full_name" id="edit_full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" id="edit_role" required>
                                <option value="admin">Administrator</option>
                                <option value="operator_sekolah">Operator Sekolah</option>
                                <option value="kepala_sekolah">Kepala Sekolah</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit</label>
                            <select class="form-select" name="unit_id" id="edit_unit_id">
                                <option value="">Pilih Unit</option>
                                <?php foreach ($units as $unit): ?>
                                    <option value="<?php echo $unit['id']; ?>"><?php echo htmlspecialchars($unit['nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                                <label class="form-check-label" for="edit_is_active">User Aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editUser(user) {
            document.getElementById('edit_id').value = user.id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_full_name').value = user.full_name;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_unit_id').value = user.unit_id || '';
            document.getElementById('edit_is_active').checked = user.is_active == 1;
            
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }

        function deleteUser(id, username) {
            if (confirm('Yakin ingin menghapus user "' + username + '"?')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
