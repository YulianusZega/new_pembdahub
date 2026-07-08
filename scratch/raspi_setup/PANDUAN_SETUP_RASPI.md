# 📺 Panduan Setup Raspberry Pi 3 – PEMBDAHUB Live Attendance Display

Panduan ini menjelaskan cara menyiapkan Raspberry Pi 3 sebagai
**papan informasi kehadiran digital** yang menampilkan data absensi
siswa dan guru secara real-time di monitor HDMI.

---

## Apa yang Dibutuhkan

| Item | Keterangan |
|------|-----------|
| Raspberry Pi 3 Model B/B+ | Board utama |
| MicroSD Card ≥ 16GB | Untuk OS (Class 10 / A1) |
| Kabel HDMI | Ke monitor/TV |
| Monitor / TV | Minimal 32 inci untuk keterbacaan |
| Charger 5V 2.5A | Power supply Raspberry Pi |
| Kabel LAN / WiFi | Koneksi internet sekolah |
| Keyboard + Mouse | Hanya untuk setup awal |

---

## Langkah 1 – Install Raspberry Pi OS

1. **Download** Raspberry Pi Imager dari:
   `https://www.raspberrypi.com/software/`

2. **Pilih OS**: `Raspberry Pi OS (64-bit)` — **dengan Desktop** (bukan Lite)

3. Klik ⚙️ (Advanced Options) sebelum flash:
   - ✅ Set hostname: `pembda-display`
   - ✅ Enable SSH
   - ✅ Set username: `pembda` / password: `pembda2024`
   - ✅ Configure WiFi: masukkan SSID & password WiFi sekolah
   - ✅ Set locale: Asia/Jakarta

4. **Flash** ke MicroSD Card, masukkan ke Raspberry Pi, nyalakan.

---

## Langkah 2 – Koneksi ke Raspberry Pi

Setelah boot pertama (tunggu ±2 menit), buka Terminal dan jalankan update:

```bash
sudo apt-get update && sudo apt-get upgrade -y
```

---

## Langkah 3 – Jalankan Script Setup Otomatis

Copy file `setup_kiosk.sh` ke Raspberry Pi, lalu jalankan:

```bash
# Cara 1: Via SCP dari PC Windows
scp setup_kiosk.sh pembda@pembda-display.local:~/

# Cara 2: Ketik langsung di terminal Raspberry Pi
nano setup_kiosk.sh
# (paste isi file, Ctrl+X, Y, Enter)

# Jalankan script
chmod +x setup_kiosk.sh
./setup_kiosk.sh
```

Script akan otomatis:
- ✅ Update sistem
- ✅ Install Chromium (jika belum ada)
- ✅ Nonaktifkan screensaver & power management
- ✅ Buat autostart kiosk mode
- ✅ Sembunyikan kursor mouse

---

## Langkah 4 – Reboot

```bash
sudo reboot
```

Setelah reboot, Raspberry Pi akan **otomatis** membuka:
`https://www.perguruanpembda.com/display`

Dalam mode fullscreen tanpa taskbar, cursor, atau notifikasi.

---

## Tampilan Dashboard

```
┌─────────────────────────────────────────────────────────────────────┐
│  🏫 PERGURUAN PEMBDA        ● LIVE   Senin, 23 Juni 2026  14:37:40  │
├─────────────┬───────────────┬──────────────┬────────────────────────┤
│  ✅ HADIR   │  🕐 TERLAMBAT │  🚪 PULANG   │  ❌ BELUM ABSEN        │
│    247      │      12       │     45       │        58              │
├─────────────┴───────────────┴──────────────┴────────────────────────┤
│  ⚡ AKTIVITAS TERBARU                                               │
│  14:37  Ahmad Fauzi Ramadhan        X IPA 1        ✅ Masuk         │
│  14:36  Siti Rahayu Putri           IX B           ✅ Masuk         │
│  14:35  Budi Santoso               XI IPS 2        🚪 Pulang        │
│  14:34  Dewi Lestari               X IPA 3         🕐 Terlambat     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Konfigurasi Tambahan (Opsional)

### Rotasi Layar (jika monitor dipasang portrait)
```bash
# Edit /boot/config.txt
sudo nano /boot/config.txt

# Tambahkan:
display_rotate=1   # 90° (portrait kanan)
display_rotate=2   # 180° (terbalik)
display_rotate=3   # 270° (portrait kiri)
```

### Ganti URL Display
```bash
nano ~/.config/autostart/kiosk.desktop
# Edit baris Exec= → ganti URL
```

### Reboot Otomatis Tiap Hari (mencegah memory leak)
```bash
# Tambahkan cron job: reboot tiap hari pukul 05:00
sudo crontab -e
# Tambahkan baris:
0 5 * * * /sbin/reboot
```

### Nonaktifkan update notifikasi
```bash
sudo apt-get remove -y update-notifier
```

---

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Layar hitam setelah boot | Cek kabel HDMI. Coba tambah `hdmi_force_hotplug=1` di `/boot/config.txt` |
| Chromium tidak mau fullscreen | Hapus folder `~/.config/chromium/` lalu reboot |
| Halaman tidak muncul (error DNS) | Pastikan WiFi sekolah terhubung: `ping perguruanpembda.com` |
| Cursor masih muncul | Jalankan manual: `unclutter -root &` |
| Data tidak update | Cek koneksi internet: `curl https://www.perguruanpembda.com/display/live-data` |

---

## Remote Management (via SSH)

Dari PC lain di jaringan yang sama:

```bash
# Koneksi SSH
ssh pembda@pembda-display.local

# Restart display tanpa matikan
sudo systemctl restart lightdm

# Cek log Chromium
journalctl -u chromium --follow

# Update URL display
nano ~/.config/autostart/kiosk.desktop
sudo reboot
```

---

> **Tip**: Gantungkan Raspberry Pi di belakang monitor menggunakan
> bracket VESA mount agar rapih dan tersembunyi. Hanya kabel HDMI
> dan power yang terlihat.
