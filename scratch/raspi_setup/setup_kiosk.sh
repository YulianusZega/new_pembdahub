#!/bin/bash
# ============================================================
#  SCRIPT SETUP RASPBERRY PI 3 - KIOSK MODE
#  Untuk: Live Monitoring Kehadiran PEMBDAHUB
#  Jalankan SEKALI setelah fresh install Raspberry Pi OS Desktop
#  
#  Cara pakai:
#    chmod +x setup_kiosk.sh
#    ./setup_kiosk.sh
# ============================================================

set -e  # Hentikan jika ada error

TARGET_URL="https://www.perguruanpembda.com/display"
AUTOSTART_DIR="$HOME/.config/autostart"
AUTOSTART_FILE="$AUTOSTART_DIR/kiosk.desktop"

echo ""
echo "======================================================"
echo "  SETUP KIOSK MODE - PEMBDAHUB LIVE ATTENDANCE"
echo "======================================================"
echo ""

# ── 1. Update sistem ──────────────────────────────────────────
echo "[1/7] Update sistem..."
sudo apt-get update -y
sudo apt-get upgrade -y

# ── 2. Pastikan Chromium terinstal ───────────────────────────
echo "[2/7] Memeriksa Chromium Browser..."
if ! command -v chromium-browser &> /dev/null && ! command -v chromium &> /dev/null; then
    echo "     Chromium tidak ditemukan. Menginstal..."
    sudo apt-get install -y chromium-browser
else
    echo "     Chromium sudah terinstal. ✓"
fi

# ── 3. Install xdotool (untuk disable screensaver) ───────────
echo "[3/7] Menginstal tools tambahan..."
sudo apt-get install -y xdotool unclutter x11-xserver-utils

# ── 4. Disable screensaver & power management ─────────────────
echo "[4/7] Menonaktifkan screensaver..."

# Buat file konfigurasi X untuk matikan screensaver
sudo bash -c 'cat > /etc/X11/xorg.conf.d/10-disable-dpms.conf << EOF
Section "ServerFlags"
    Option "StandbyTime"  "0"
    Option "SuspendTime"  "0"
    Option "OffTime"      "0"
    Option "BlankTime"    "0"
EndSection
EOF'

# ── 5. Buat script utama kiosk (dengan koneksi instan check & fallback) ─────
echo "[5/7] Membuat script utama kiosk..."

# Buat file offline.html lokal
echo "     Membuat halaman offline lokal di $HOME/offline.html..."
cat > "$HOME/offline.html" << 'EOF'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1920, initial-scale=1.0">
    <title>Menghubungkan Jaringan – Perguruan PEMBDA</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            margin: 0;
            padding: 0;
            background-color: #0f172a;
            color: #f8fafc;
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 650px;
            padding: 50px 40px;
            border-radius: 28px;
            background: rgba(30, 41, 59, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }
        .logo-box {
            position: relative;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-icon {
            font-size: 64px;
            z-index: 2;
            animation: bounce 2s infinite ease-in-out;
        }
        .logo-pulse {
            position: absolute;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.2);
            animation: pulse-ring 2s infinite ease-out;
            z-index: 1;
        }
        h1 {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #ffffff;
            margin-bottom: -8px;
        }
        p {
            color: #94a3b8;
            font-size: 15px;
            line-height: 1.6;
        }
        .loader {
            width: 44px;
            height: 44px;
            border: 3px solid rgba(59, 130, 246, 0.15);
            border-radius: 50%;
            border-top-color: #3b82f6;
            animation: spin 1s linear infinite;
        }
        .status-badge {
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.25);
            color: #93c5fd;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin-top: 8px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { opacity: 0.4; }
            100% { transform: scale(1.6); opacity: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-box">
            <div class="logo-icon">📡</div>
            <div class="logo-pulse"></div>
        </div>
        <h1>Mencari Koneksi Internet</h1>
        <p>
            Sistem sedang menunggu koneksi internet aktif.<br>
            Harap pastikan kabel LAN terpasang dengan benar atau Wi-Fi sekolah telah terhubung.
        </p>
        <div class="loader"></div>
        <div class="status-badge" id="status-text">Menghubungkan ke perguruanpembda.com...</div>
    </div>

    <script>
        const targetUrl = "https://www.perguruanpembda.com/display";
        const checkUrl  = "https://www.perguruanpembda.com/display/live-data";
        let attempt = 0;
        
        async function checkConnection() {
            attempt++;
            const statusText = document.getElementById('status-text');
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 4000);
                
                const res = await fetch(checkUrl + '?t=' + Date.now(), { 
                    signal: controller.signal 
                });
                clearTimeout(timeoutId);
                
                if (res.ok) {
                    statusText.style.borderColor = 'rgba(34, 197, 94, 0.4)';
                    statusText.style.background = 'rgba(34, 197, 94, 0.15)';
                    statusText.style.color = '#4ade80';
                    statusText.textContent = "Koneksi terhubung! Mengalihkan ke display...";
                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 1200);
                    return;
                }
            } catch (e) {
                console.log("Belum terhubung ke server:", e);
            }
            
            statusText.textContent = `Mencoba kembali (Percobaan ke-${attempt})...`;
            setTimeout(checkConnection, 5000);
        }
        
        setTimeout(checkConnection, 2000);
    </script>
</body>
</html>
EOF

# Buat kiosk.sh
cat > "$HOME/kiosk.sh" << 'EOF'
#!/bin/bash
# Matikan screensaver dan hemat daya monitor
xset s off
xset s noblank
xset -dpms

# Sembunyikan kursor mouse setelah 2 detik tidak ada aktivitas
unclutter -idle 2 -root &

# Bersihkan Chromium session yang crash agar tidak muncul popup restore
sed -i 's/"exit_type":"Crashed"/"exit_type":"Normal"/' "$HOME/.config/chromium/Default/Preferences" || true
sed -i 's/"exited_cleanly":false/"exited_cleanly":true/' "$HOME/.config/chromium/Default/Preferences" || true

# Deteksi nama binary chromium
CHROMIUM_BIN="chromium-browser"
if ! command -v chromium-browser &>/dev/null; then
    CHROMIUM_BIN="chromium"
fi

# Cek koneksi internet awal secara instan (timeout 2 detik)
if ping -c 1 -W 2 perguruanpembda.com &>/dev/null; then
    START_URL="https://www.perguruanpembda.com/display"
    echo "Internet aktif. Membuka URL utama: $START_URL"
else
    START_URL="file://$HOME/offline.html"
    echo "Internet tidak aktif. Membuka halaman offline lokal: $START_URL"
fi

# Jalankan Chromium dengan mode Kiosk dan penanganan GPU bug pada Raspberry Pi
$CHROMIUM_BIN \
  --kiosk \
  --noerrdialogs \
  --disable-infobars \
  --no-first-run \
  --disable-session-crashed-bubble \
  --disable-restore-session-state \
  --disable-translate \
  --disable-features=TranslateUI \
  --autoplay-policy=no-user-gesture-required \
  --start-fullscreen \
  --window-position=0,0 \
  --check-for-update-interval=31536000 \
  --disable-gpu \
  --disable-software-rasterizer \
  --password-store=basic \
  "$START_URL" &
EOF

chmod +x "$HOME/kiosk.sh"
echo "     Script $HOME/kiosk.sh berhasil dibuat! ✓"

# ── 6. Buat shortcut autostart desktop ───────────────────────
echo "[6/7] Membuat shortcut autostart desktop..."
mkdir -p "$AUTOSTART_DIR"

# Hapus desktop autostart lama agar tidak bentrok
rm -f "$AUTOSTART_DIR/kiosk-helper.desktop"

cat > "$AUTOSTART_FILE" << EOF
[Desktop Entry]
Type=Application
Name=PEMBDAHUB Kiosk
Comment=Live Attendance Monitor
Exec=$HOME/kiosk.sh
Hidden=false
NoDisplay=false
X-GNOME-Autostart-enabled=true
EOF

echo "     Autostart $AUTOSTART_FILE berhasil dibuat! ✓"

# ── 7. Konfigurasi WiFi (opsional) ───────────────────────────
echo "[7/7] Konfigurasi selesai!"
echo ""
echo "======================================================"
echo "  ✅ SETUP BERHASIL!"
echo "======================================================"
echo ""
echo "  URL yang akan dibuka: $TARGET_URL"
echo ""
echo "  Langkah selanjutnya:"
echo "  1. Hubungkan Raspberry Pi ke WiFi sekolah"
echo "     (jika belum, pakai: sudo raspi-config → System → WiFi)"
echo "  2. Hubungkan ke monitor HDMI"
echo "  3. Reboot: sudo reboot"
echo ""
echo "  Setelah reboot, monitor akan otomatis menampilkan"
echo "  dashboard kehadiran dalam mode fullscreen."
echo ""
echo "  Untuk keluar dari kiosk: Alt+F4 atau Ctrl+Alt+T"
echo "======================================================"
