<?php
require_once 'config.php';
require_once 'auth.php';

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($auth->login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SISFOPEMBDA</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link href="assets/css/dashboard.css" rel="stylesheet">
        <link href="assets/css/theme.css" rel="stylesheet">
        <style>
            body {background: var(--primary-gradient);min-height:100vh;display:flex;align-items:center;justify-content:center;font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;}
            .login-container {background:#fff;border-radius:20px;box-shadow:0 20px 40px rgba(0,0,0,0.1);overflow:hidden;width:100%;max-width:900px;min-height:500px;display:flex;}
            .login-left {background:var(--primary-gradient);color:#fff;padding:60px 40px;display:flex;flex-direction:column;justify-content:center;text-align:center;flex:1;}
            .login-right {padding:60px 40px;display:flex;flex-direction:column;justify-content:center;flex:1;}
            .btn-login {background:var(--gradient-action-blue);border:none;border-radius:10px;padding:12px;font-size:16px;font-weight:600;color:#fff;width:100%;transition:var(--transition);}
            .btn-login:hover {transform:translateY(-2px);box-shadow:0 10px 20px rgba(0,123,255,0.35);} 
            .form-control {border:2px solid #e9ecef;border-radius:10px;padding:12px 15px;font-size:16px;transition:var(--transition);} 
            .form-control:focus {border-color:#007bff;box-shadow:0 0 0 0.2rem rgba(0,123,255,.25);} 
            .demo-accounts {background:#f8f9fa;border-radius:10px;padding:20px;margin-top:20px;} 
            .demo-account {display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:#fff;border-radius:5px;margin-bottom:8px;font-size:14px;} 
            .demo-account .role {font-weight:600;color:#007bff;} .demo-account .username {color:#666;font-family:monospace;} 
            .icon-bg {background:rgba(255,255,255,0.1);width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;} 
        </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="login-container row g-0">
                    <!-- Left Side -->
                    <div class="col-md-6 login-left">
                        <div class="icon-bg">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h1>SISFOPEMBDA</h1>
                        <p>Sistem Informasi Perhitungan Tunjangan Pegawai<br>Yayasan Perguruan Pembda Nias</p>
                        <div class="demo-accounts">
                            <h6><i class="fas fa-info-circle"></i> Demo Accounts</h6>
                            <div class="demo-account">
                                <span class="role">Admin</span>
                                <span class="username">admin / password</span>
                            </div>
                            <div class="demo-account">
                                <span class="role">Operator</span>
                                <span class="username">operator1 / password</span>
                            </div>
                            <div class="demo-account">
                                <span class="role">Kepala Sekolah</span>
                                <span class="username">kepala1 / password</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side -->
                    <div class="col-md-6 login-right">
                        <div class="login-form">
                            <h2><i class="fas fa-sign-in-alt me-2"></i>Masuk ke Sistem</h2>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                    <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                                </div>
                                
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                                </div>
                                
                                <button type="submit" class="btn btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-fill demo accounts when clicked
        document.querySelectorAll('.demo-account').forEach(function(account) {
            account.style.cursor = 'pointer';
            account.addEventListener('click', function() {
                const usernameText = this.querySelector('.username').textContent;
                const username = usernameText.split(' / ')[0];
                document.getElementById('username').value = username;
                document.getElementById('password').value = 'password';
            });
        });
    </script>
</body>
</html>
