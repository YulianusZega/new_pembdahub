<?php
// FILE: override_functions.php

/**
 * Mengambil semua aturan override yang aktif untuk seorang pegawai dari database.
 *
 * @param PDO $pdo Koneksi database PDO.
 * @param int $pegawaiId ID pegawai.
 * @return array Array assosiatif dari semua aturan override yang ditemukan.
 */
function getOverrideRules($pdo, $pegawaiId) {
    $stmt = $pdo->prepare("SELECT * FROM pegawai_override_rules WHERE pegawai_id = ? AND is_active = 1");
    $stmt->execute([$pegawaiId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Memeriksa apakah aturan override dengan nama tertentu ada.
 *
 * @param array $rules Array aturan override.
 * @param string $ruleName Nama aturan yang dicari (rule_type).
 * @return bool True jika aturan ditemukan, false jika tidak.
 */
function hasOverrideRule($rules, $ruleName) {
    foreach ($rules as $rule) {
        if ($rule['rule_type'] === $ruleName) {
            return true;
        }
    }
    return false;
}

/**
 * Mengambil nilai dari aturan override.
 *
 * @param array $rules Array aturan override.
 * @param string $ruleName Nama aturan yang dicari (rule_type).
 * @return string|null Nilai aturan (rule_value) atau null jika tidak ada.
 */
function getRuleValue($rules, $ruleName) {
    foreach ($rules as $rule) {
        if ($rule['rule_type'] === $ruleName) {
            return $rule['rule_value'];
        }
    }
    return null;
}

/**
 * Mengambil nilai jam wajib kustom.
 *
 * @param array $overrideRules Array aturan override.
 * @param int $defaultJamWajib Nilai jam wajib default.
 * @return int Nilai jam wajib kustom atau default.
 */
function getCustomJamWajib($overrideRules, $defaultJamWajib) {
    if (hasOverrideRule($overrideRules, 'custom_jam_wajib')) {
        $rule_value = getRuleValue($overrideRules, 'custom_jam_wajib');
        return (int)$rule_value;
    }
    return $defaultJamWajib;
}

/**
 * Calculates all allowances (tunjangan) for a given employee.
 *
 * @param PDO $pdo The database connection object.
 * @param int $pegawai_id The ID of the employee.
 * @param float $gaji_pokok The employee's base salary.
 * @param string $status_perkawinan The employee's marital status.
 * @param int $jumlah_anak The number of children.
 * @param string $status_kepegawaian The employee's employment status.
 * @return array An associative array of calculated allowances.
 */
function calculateTunjanganWithOverride($pdo, $pegawai_id, $gaji_pokok, $status_perkawinan, $jumlah_anak, $status_kepegawaian) {
    $overrideRules = getOverrideRules($pdo, $pegawai_id);

    // Hanya berikan tunjangan untuk GTY dan PTY
    if ($status_kepegawaian !== 'GTY' && $status_kepegawaian !== 'PTY') {
        return [
            'tunjangan_keluarga' => 0,
            'tunjangan_anak' => 0,
            'tunjangan_beras' => 0,
        ];
    }
    
    // Get tunjangan formula from database
    $stmt = $pdo->prepare("SELECT nama, tipe, nilai FROM tunjangan_formula");
    $stmt->execute();
    $formulas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $tunjangan_keluarga_persen = 0;
    $tunjangan_anak_persen = 0;
    $tunjangan_beras_nilai = 0;

    foreach ($formulas as $formula) {
        if ($formula['nama'] === 'keluarga' && $formula['tipe'] === 'persen') {
            $tunjangan_keluarga_persen = (float)$formula['nilai'];
        }
        if ($formula['nama'] === 'anak' && $formula['tipe'] === 'persen') {
            $tunjangan_anak_persen = (float)$formula['nilai'];
        }
        if ($formula['nama'] === 'beras' && $formula['tipe'] === 'fixed') {
            $tunjangan_beras_nilai = (float)$formula['nilai'];
        }
    }

    $tunjangan_keluarga = 0;
    $tunjangan_anak = 0;
    $tunjangan_beras = 0;
    
    // Perhitungan tunjangan hanya jika status_perkawinan adalah Menikah
    if ($status_perkawinan === 'Menikah') {
        $tunjangan_keluarga = ($tunjangan_keluarga_persen / 100) * $gaji_pokok;
        
        $jumlah_tanggungan = 1 + $jumlah_anak;
        $tunjangan_beras = $tunjangan_beras_nilai * $jumlah_tanggungan;

        if ($jumlah_anak > 0) {
            $tunjangan_anak = ($tunjangan_anak_persen / 100) * $gaji_pokok * min($jumlah_anak, 2);
        }
    }
    
    // Terapkan aturan override khusus tunjangan
    if (hasOverrideRule($overrideRules, 'no_tunjangan_keluarga')) {
        $tunjangan_keluarga = 0;
    }
    if (hasOverrideRule($overrideRules, 'no_tunjangan_anak')) {
        $tunjangan_anak = 0;
    }
    if (hasOverrideRule($overrideRules, 'no_tunjangan_beras')) {
        $tunjangan_beras = 0;
    }
    
    // Contoh untuk override dengan nilai kustom
    if (hasOverrideRule($overrideRules, 'custom_tunjangan_keluarga_persen')) {
        $custom_persen = getRuleValue($overrideRules, 'custom_tunjangan_keluarga_persen');
        if ($custom_persen !== null) {
             $tunjangan_keluarga = ((float)$custom_persen / 100) * $gaji_pokok;
        }
    }
    if (hasOverrideRule($overrideRules, 'custom_tunjangan_anak_persen')) {
        $custom_persen = getRuleValue($overrideRules, 'custom_tunjangan_anak_persen');
        if ($custom_persen !== null) {
            $tunjangan_anak = ((float)$custom_persen / 100) * $gaji_pokok * min($jumlah_anak, 2);
        }
    }
    if (hasOverrideRule($overrideRules, 'custom_tunjangan_beras_amount')) {
        $custom_amount = getRuleValue($overrideRules, 'custom_tunjangan_beras_amount');
        if ($custom_amount !== null) {
            $tunjangan_beras = (float)$custom_amount * (1 + $jumlah_anak);
        }
    }
    
    return [
        'tunjangan_keluarga' => $tunjangan_keluarga,
        'tunjangan_anak' => $tunjangan_anak,
        'tunjangan_beras' => $tunjangan_beras,
    ];
}

/**
 * Memperbarui semua data penugasan untuk seorang pegawai.
 *
 * @param PDO $pdo Koneksi database PDO.
 * @param int $pegawai_id ID pegawai yang datanya perlu diperbarui.
 * @return void
 */
function updatePenugasanForOverrideChanges($pdo, $pegawai_id) {
    // 1. Ambil semua penugasan yang ada untuk pegawai ini
    $penugasanStmt = $pdo->prepare("SELECT * FROM penugasan WHERE pegawai_id = ?");
    $penugasanStmt->execute([$pegawai_id]);
    $penugasanList = $penugasanStmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Jika tidak ada penugasan, hentikan proses
    if (empty($penugasanList)) {
        return;
    }

    // 3. Ambil data pegawai terbaru untuk perhitungan
    $pegawaiStmt = $pdo->prepare("SELECT status_kepegawaian, status_perkawinan, jumlah_anak, gaji_pokok FROM pegawai WHERE id = ?");
    $pegawaiStmt->execute([$pegawai_id]);
    $pegawaiData = $pegawaiStmt->fetch(PDO::FETCH_ASSOC);

    // 4. Lakukan loop dan perbarui setiap penugasan
    foreach ($penugasanList as $penugasan) {
        $unit_id = $penugasan['unit_id'];
        $jam_mengajar = $penugasan['jam_mengajar'];

        // Ambil aturan honor per jam
        $jamHonorStmt = $pdo->prepare("SELECT jam_wajib, honor_per_jam FROM jam_honor WHERE unit_id = ? AND status_kepegawaian = ?");
        $jamHonorStmt->execute([$unit_id, $pegawaiData['status_kepegawaian']]);
        $jamHonor = $jamHonorStmt->fetch(PDO::FETCH_ASSOC);
        
        $default_jam_wajib = $jamHonor ? $jamHonor['jam_wajib'] : 0;
        
        // Cek kembali override jam wajib
        $overrideRules = getOverrideRules($pdo, $pegawai_id);
        $jam_wajib = getCustomJamWajib($overrideRules, $default_jam_wajib);
        
        $jam_honor = max(0, $jam_mengajar - $jam_wajib);
        $honor_per_jam = $jamHonor ? $jamHonor['honor_per_jam'] : 0;
        $honor = $jam_honor * $honor_per_jam;

        // Hitung ulang tunjangan dengan fungsi yang sudah diperbaiki
        $tunjanganResult = calculateTunjanganWithOverride(
            $pdo,
            $pegawai_id,
            $pegawaiData['gaji_pokok'],
            $pegawaiData['status_perkawinan'],
            $pegawaiData['jumlah_anak'],
            $pegawaiData['status_kepegawaian']
        );
        $tunjangan_keluarga = $tunjanganResult['tunjangan_keluarga'];
        $tunjangan_anak = $tunjanganResult['tunjangan_anak'];
        $tunjangan_beras = $tunjanganResult['tunjangan_beras'];

        // Hitung tunjangan jabatan
        $tunjangan_jabatan = 0;
        $jabatanTunjanganStmt = $pdo->prepare("
            SELECT SUM(j.tunjangan_jabatan) as total_jabatan
            FROM penugasan_jabatan pj
            JOIN jabatan j ON pj.jabatan_id = j.id
            WHERE pj.penugasan_id = ?
        ");
        $jabatanTunjanganStmt->execute([$penugasan['id']]);
        $jabatanResult = $jabatanTunjanganStmt->fetch();
        $tunjangan_jabatan = $jabatanResult['total_jabatan'] ?: 0;
        
        // Hitung ulang total
        $total_tunjangan_keluarga = $tunjangan_keluarga + $tunjangan_anak + $tunjangan_beras;
        $total = $pegawaiData['gaji_pokok'] + $tunjangan_jabatan + $total_tunjangan_keluarga + $honor;
        
        // Perbarui data di tabel penugasan
        $updateStmt = $pdo->prepare("UPDATE penugasan SET jam_mengajar = ?, jam_wajib = ?, jam_honor = ?, honor = ?, tunjangan_keluarga = ?, tunjangan_anak = ?, tunjangan_beras = ?, tunjangan_jabatan = ?, total = ? WHERE id = ?");
        $updateStmt->execute([$jam_mengajar, $jam_wajib, $jam_honor, $honor, $tunjangan_keluarga, $tunjangan_anak, $tunjangan_beras, $tunjangan_jabatan, $total, $penugasan['id']]);
    }
}