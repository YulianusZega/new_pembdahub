<?php
/**
 * Diagnosa SSH Key & Git Config di Server
 * Akses: https://perguruanpembda.com/check_ssh.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Forbidden'); }

header('Content-Type: text/plain; charset=utf-8');

echo "=== SSH & GIT DIAGNOSTICS ===\n\n";

// 1. Check SSH keys
echo "--- 1. SSH Keys yang tersedia ---\n";
$sshDir = getenv('HOME') . '/.ssh';
echo "SSH dir: {$sshDir}\n";
if (is_dir($sshDir)) {
    $files = scandir($sshDir);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $path = "{$sshDir}/{$f}";
        echo "  {$f} (" . filesize($path) . " bytes)\n";
    }
    // Show public keys
    foreach (glob("{$sshDir}/*.pub") as $pubKey) {
        echo "\n  Content of " . basename($pubKey) . ":\n";
        echo "  " . trim(file_get_contents($pubKey)) . "\n";
    }
    // If no .pub, try id_rsa.pub or id_ed25519.pub
    if (empty(glob("{$sshDir}/*.pub"))) {
        echo "\n  (Tidak ada file .pub)\n";
    }
} else {
    echo "  SSH directory TIDAK ADA!\n";
}

// 2. Check git config
echo "\n--- 2. Git Config ---\n";
$root = '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub';
echo shell_exec("git -C {$root} config --list 2>&1") . "\n";

// 3. Check SSH connectivity to GitHub
echo "--- 3. Test SSH ke GitHub ---\n";
echo shell_exec("ssh -T git@github.com -o StrictHostKeyChecking=no -o ConnectTimeout=10 2>&1") . "\n";

// 4. Check known_hosts
echo "--- 4. Known Hosts ---\n";
$knownHosts = getenv('HOME') . '/.ssh/known_hosts';
if (file_exists($knownHosts)) {
    $content = file_get_contents($knownHosts);
    $hasGithub = strpos($content, 'github.com') !== false;
    echo "  known_hosts exists: YES\n";
    echo "  Contains github.com: " . ($hasGithub ? 'YES' : 'NO') . "\n";
} else {
    echo "  known_hosts: TIDAK ADA\n";
}

// 5. Try git fetch
echo "\n--- 5. Test git fetch ---\n";
echo shell_exec("GIT_SSH_COMMAND='ssh -o StrictHostKeyChecking=no -o ConnectTimeout=10' git -C {$root} fetch origin 2>&1") . "\n";

// 6. Check git remote & refs
echo "--- 6. Remote & Refs ---\n";
echo "Remote URL: " . trim(shell_exec("git -C {$root} remote get-url origin 2>&1")) . "\n";
echo "Local HEAD: " . trim(shell_exec("git -C {$root} rev-parse --short HEAD 2>&1")) . "\n";
echo "Origin/main: " . trim(shell_exec("git -C {$root} rev-parse --short origin/main 2>&1")) . "\n";

// 7. Whoami
echo "\n--- 7. User Info ---\n";
echo "whoami: " . trim(shell_exec("whoami 2>&1")) . "\n";
echo "HOME: " . getenv('HOME') . "\n";
echo "USER: " . getenv('USER') . "\n";
