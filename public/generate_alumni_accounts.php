<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') {
    die('Akses Ditolak.');
}

echo "<div style='font-family: sans-serif; padding: 20px; max-width: 800px; margin: auto;'>";
echo "<h2 style='color: #4f46e5;'>Generate Akun Login untuk Alumni Lama</h2>";

$alumnis = \App\Models\AlumniDirectory::whereNull('user_id')->get();

if ($alumnis->isEmpty()) {
    echo "<div style='background: #ecfdf5; color: #065f46; padding: 15px; border-radius: 8px;'>";
    echo "✅ Semua alumni sudah memiliki akun Login! Tidak ada data yang perlu diproses.";
    echo "</div>";
    exit;
}

echo "<div style='background: #fffbeb; color: #92400e; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>";
echo "Ditemukan <b>" . $alumnis->count() . "</b> alumni yang belum memiliki akun login. Memproses...";
echo "</div>";

echo "<table style='width: 100%; border-collapse: collapse; text-align: left;'>";
echo "<tr style='background: #f1f5f9;'><th style='padding: 10px; border: 1px solid #cbd5e1;'>Nama Alumni</th><th style='padding: 10px; border: 1px solid #cbd5e1;'>Tahun Lulus</th><th style='padding: 10px; border: 1px solid #cbd5e1;'>Username</th><th style='padding: 10px; border: 1px solid #cbd5e1;'>Password</th></tr>";

$count = 0;
foreach ($alumnis as $alumni) {
    // Generate username dari nama depan
    $firstName = strtolower(explode(' ', trim(preg_replace('/[^a-zA-Z\s]/', '', $alumni->full_name)))[0]);
    if (empty($firstName)) $firstName = 'alumni';
    
    $username = $firstName . '_' . $alumni->graduation_year;
    
    // Pastikan unik
    $counter = 1;
    while (\App\Models\User::where('username', $username)->exists()) {
        $username = $firstName . '_' . $alumni->graduation_year . '_' . $counter;
        $counter++;
    }
    
    $passwordStr = 'pembda' . date('Y'); // pembda2026
    
    // Generate email dummy
    $email = $username . '@alumni.pembdahub.com';
    $counter = 1;
    while (\App\Models\User::where('email', $email)->exists()) {
        $email = $username . $counter . '@alumni.pembdahub.com';
        $counter++;
    }

    try {
        \Illuminate\Support\Facades\DB::transaction(function() use ($alumni, $username, $passwordStr, $email) {
            $user = \App\Models\User::create([
                'name' => $alumni->full_name,
                'username' => $username,
                'email' => $alumni->email ?? $email,
                'password' => \Illuminate\Support\Facades\Hash::make($passwordStr),
                'role' => 'alumni',
                'is_active' => true,
            ]);
            
            $alumni->user_id = $user->id;
            $alumni->save();
        });
        
        echo "<tr>";
        echo "<td style='padding: 10px; border: 1px solid #cbd5e1;'>" . htmlspecialchars($alumni->full_name) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #cbd5e1;'>" . htmlspecialchars($alumni->graduation_year) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #cbd5e1; font-family: monospace; font-weight: bold; color: #0284c7;'>" . $username . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #cbd5e1; font-family: monospace; font-weight: bold; color: #ea580c;'>" . $passwordStr . "</td>";
        echo "</tr>";
        
        $count++;
    } catch (\Exception $e) {
        echo "<tr><td colspan='4' style='padding: 10px; border: 1px solid #cbd5e1; color: red;'>Gagal memproses {$alumni->full_name}: " . $e->getMessage() . "</td></tr>";
    }
}

echo "</table>";
echo "<div style='margin-top: 20px; font-size: 14px; color: #475569;'>Silakan simpan informasi di atas dan berikan kepada alumni yang bersangkutan.</div>";
echo "</div>";
