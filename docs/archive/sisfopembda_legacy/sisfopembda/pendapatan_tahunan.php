<?php
require_once 'config.php';
require_once 'auth.php';
$auth = new Auth($pdo);
$auth->requireLogin();
$auth->requirePermission('evaluasi');
$menuItems = getMenuItems();
$userRole = $auth->getRole();
$fullName = $auth->getFullName();

// Ambil daftar unit (exclude Yayasan agar entri per unit, Yayasan rekap di evaluasi)
$units = $pdo->query("SELECT * FROM unit WHERE level <> 'Yayasan' ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

$tahun = isset($_GET['tahun_pelajaran']) ? $_GET['tahun_pelajaran'] : (date('n')>=7 ? date('Y').'/'.(date('Y')+1) : (date('Y')-1).'/'.date('Y'));
$selectedUnit = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;
$kelasMap = ['SMP'=>['VII','VIII','IX'], 'SMA'=>['X','XI','XII'], 'SMK'=>['X','XI','XII']];

// Detect level unit terpilih
$unitLevel = '';
if ($selectedUnit) {
  $st = $pdo->prepare("SELECT level FROM unit WHERE id=?");
  $st->execute([$selectedUnit]);
  $unitLevel = $st->fetchColumn();
}

$success=''; $error='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $selectedUnit = (int)$_POST['unit_id'];
  $tahun = $_POST['tahun_pelajaran'];
  $st = $pdo->prepare("SELECT level FROM unit WHERE id=?");
  $st->execute([$selectedUnit]);
  $unitLevel = $st->fetchColumn();
  if (!$unitLevel) { $error='Unit tidak valid.'; }
  else {
    $kelasList = $kelasMap[$unitLevel] ?? [];
    $pdo->beginTransaction();
    try {
      foreach ($kelasList as $kls) {
        $jumlah = (int)($_POST['jumlah_'.$kls] ?? 0);
        $uang = preg_replace('/[^0-9]/','', $_POST['uang_'.$kls] ?? '0');
        $uang = (int)$uang;
        $cek = $pdo->prepare("SELECT id FROM pendapatan_unit_kelas WHERE unit_id=? AND tahun_pelajaran=? AND kelas=?");
        $cek->execute([$selectedUnit,$tahun,$kls]);
        $id = $cek->fetchColumn();
        if ($id) {
          $up = $pdo->prepare("UPDATE pendapatan_unit_kelas SET jumlah_siswa=?, uang_sekolah=? WHERE id=?");
          $up->execute([$jumlah,$uang,$id]);
        } else {
          $ins = $pdo->prepare("INSERT INTO pendapatan_unit_kelas (unit_id,tahun_pelajaran,kelas,jumlah_siswa,uang_sekolah) VALUES (?,?,?,?,?)");
          $ins->execute([$selectedUnit,$tahun,$kls,$jumlah,$uang]);
        }
      }
      $pdo->commit();
      $success='Data pendapatan tahunan berhasil disimpan.';
    } catch(Exception $e) {
      $pdo->rollBack();
      $error='Gagal simpan: '.$e->getMessage();
    }
  }
}

// Ambil data existing
$dataKelas = [];
if ($selectedUnit && $unitLevel) {
  $kelasList = $kelasMap[$unitLevel] ?? [];
  if ($kelasList) {
    $pl = implode(',', array_fill(0,count($kelasList),'?'));
    $params = array_merge([$selectedUnit,$tahun], $kelasList);
    $st = $pdo->prepare("SELECT kelas,jumlah_siswa,uang_sekolah FROM pendapatan_unit_kelas WHERE unit_id=? AND tahun_pelajaran=? AND kelas IN ($pl)");
    $st->execute($params);
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) { $dataKelas[$r['kelas']] = $r; }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pendapatan Tahunan - SISFOPEMBDA</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="assets/css/dashboard.css" rel="stylesheet">
<link href="assets/css/theme.css" rel="stylesheet">
<style>.money{text-align:right}</style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3 col-lg-2 sidebar">
      <div class="d-flex flex-column">
        <div class="p-3 text-center border-bottom border-light">
          <h4 class="text-white mb-0">SISFOPEMBDA</h4>
          <small class="text-light">Sistem Informasi Administrasi</small>
        </div>
        <nav class="nav flex-column mt-3">
          <?php foreach($menuItems as $key=>$menu): ?>
            <a class="nav-link <?php echo $key==='evaluasi'?'active':''; ?>" href="<?php echo $menu['file']; ?>"><i class="<?php echo $menu['icon']; ?> me-2"></i><?php echo $menu['title']; ?></a>
          <?php endforeach; ?>
          <hr class="border-light mx-3">
          <a class="nav-link text-danger" href="?logout=1"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
        </nav>
      </div>
    </div>
    <div class="col-md-9 col-lg-10 main-content">
      <nav class="navbar navbar-light"><div class="container-fluid"><h5 class="mb-0">Pendapatan Tahunan (Jumlah Siswa & Uang Sekolah)</h5></div></nav>
      <div class="container-fluid p-4">
        <?php if($error):?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
        <?php if($success):?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
        <form method="GET" class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label">Tahun Pelajaran</label>
            <select name="tahun_pelajaran" class="form-select" onchange="this.form.submit()">
              <?php for($y=date('Y')-1;$y<=date('Y')+2;$y++): $tp=$y.'/'.($y+1); ?>
              <option value="<?php echo $tp; ?>" <?php echo $tp==$tahun?'selected':''; ?>><?php echo $tp; ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Unit</label>
            <select name="unit_id" class="form-select" onchange="this.form.submit()">
              <option value="0">Pilih Unit</option>
              <?php foreach($units as $u): ?>
                <option value="<?php echo $u['id']; ?>" <?php echo $u['id']==$selectedUnit?'selected':''; ?>><?php echo htmlspecialchars($u['nama']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </form>
        <?php if($selectedUnit && $unitLevel): ?>
        <form method="POST" class="card p-3">
          <input type="hidden" name="unit_id" value="<?php echo $selectedUnit; ?>">
          <input type="hidden" name="tahun_pelajaran" value="<?php echo htmlspecialchars($tahun); ?>">
          <h6 class="fw-bold mb-3">Input Data Kelas (<?php echo $unitLevel; ?>)</h6>
          <div class="row g-3">
            <?php foreach(($kelasMap[$unitLevel]??[]) as $kls): $d=$dataKelas[$kls]??['jumlah_siswa'=>'','uang_sekolah'=>'']; ?>
            <div class="col-md-4">
              <label class="form-label">Kelas <?php echo $kls; ?></label>
              <input type="number" name="jumlah_<?php echo $kls; ?>" class="form-control mb-1" placeholder="Jumlah Siswa" min="0" value="<?php echo $d['jumlah_siswa']; ?>">
              <input type="text" name="uang_<?php echo $kls; ?>" class="form-control money" placeholder="Uang Sekolah" value="<?php echo $d['uang_sekolah']; ?>">
            </div>
            <?php endforeach; ?>
          </div>
          <div class="mt-3">
            <button class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
          </div>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('.money').forEach(inp=>{
    inp.addEventListener('input', e=>{
      let v=e.target.value.replace(/[^0-9]/g,'');
      if(!v){e.target.value='';return;}
      e.target.value = new Intl.NumberFormat('id-ID').format(v);
    });
  });
</script>
</body></html>
