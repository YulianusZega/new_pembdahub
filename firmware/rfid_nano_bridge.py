"""
=================================================================
  RFID Nano Bridge - PembdaHub
=================================================================
  Script ini membaca UID dari Arduino Nano via USB Serial,
  lalu meneruskannya ke server PembdaHub scan-buffer API.

  Setelah script ini berjalan, tombol "Scan RFID" yang sudah
  ada di halaman Edit Siswa / Edit Pegawai bisa langsung
  mendeteksi kartu dari Arduino Nano.

  CARA PAKAI:
  1. Install Python: https://python.org
  2. Install library: pip install pyserial requests
  3. Colok Arduino Nano ke laptop via USB
  4. Jalankan script ini:
       python rfid_nano_bridge.py
  5. Buka halaman Edit Siswa di browser
  6. Klik "Scan RFID" → tempelkan kartu ke Nano → UID muncul!

  KONFIGURASI:
  Sesuaikan COM_PORT di bawah dengan port Arduino Nano Anda.
  (Cek di Arduino IDE → Tools → Port)
=================================================================
"""

import serial
import serial.tools.list_ports
import requests
import time
import sys

# ================================================================
#  KONFIGURASI - Sesuaikan di sini
# ================================================================

# URL server PembdaHub
SERVER_URL = "https://perguruanpembda.com/api/rfid/scan-buffer"

# API Key (harus sama dengan di firmware)
API_KEY    = "RAHASIA-PEMBDAHUB-12345"

# Baudrate (harus sama dengan firmware Nano: 115200)
BAUD_RATE  = 115200

# Timeout koneksi ke server (detik)
HTTP_TIMEOUT = 5

# ================================================================
#  FUNGSI UTILITAS
# ================================================================

def list_ports():
    """Tampilkan semua port serial yang tersedia"""
    ports = serial.tools.list_ports.comports()
    if not ports:
        print("  ⚠️  Tidak ada port serial yang terdeteksi!")
        return []
    print("  Port serial yang tersedia:")
    for i, p in enumerate(ports):
        print(f"  [{i}] {p.device} — {p.description}")
    return ports

def select_port():
    """Minta user memilih port atau otomatis pilih yang pertama"""
    ports = list_ports()
    if not ports:
        return None

    if len(ports) == 1:
        print(f"\n  ✅ Otomatis memilih: {ports[0].device}")
        return ports[0].device

    try:
        idx = int(input(f"\n  Pilih nomor port [0-{len(ports)-1}]: "))
        return ports[idx].device
    except (ValueError, IndexError):
        print("  Input tidak valid, memilih port pertama.")
        return ports[0].device

def send_to_buffer(uid):
    """Kirim UID ke scan-buffer API PembdaHub"""
    try:
        resp = requests.post(
            SERVER_URL,
            json={"uid": uid},
            headers={"X-Kiosk-API-Key": API_KEY},
            timeout=HTTP_TIMEOUT,
            verify=False  # Skip SSL verify (jika pakai HTTPS self-signed)
        )
        if resp.status_code == 200:
            print(f"  ✅ UID [{uid}] berhasil dikirim ke server!")
            return True
        else:
            print(f"  ❌ Server menolak UID. Status: {resp.status_code} | {resp.text[:80]}")
            return False
    except requests.exceptions.ConnectionError:
        print(f"  ❌ Tidak bisa menghubungi server. Cek koneksi internet!")
        return False
    except requests.exceptions.Timeout:
        print(f"  ❌ Timeout! Server tidak merespons dalam {HTTP_TIMEOUT} detik.")
        return False
    except Exception as e:
        print(f"  ❌ Error: {e}")
        return False

# ================================================================
#  MAIN
# ================================================================

def main():
    # Matikan warning SSL
    import urllib3
    urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

    print("=" * 60)
    print("  RFID NANO BRIDGE — PembdaHub")
    print("=" * 60)
    print(f"  Server  : {SERVER_URL}")
    print(f"  Baudrate: {BAUD_RATE}")
    print()

    # Pilih port
    com_port = select_port()
    if not com_port:
        print("\n  Tidak ada port yang dipilih. Program berhenti.")
        input("  Tekan Enter untuk keluar...")
        sys.exit(1)

    # Buka koneksi serial ke Arduino Nano
    print(f"\n  Membuka koneksi ke {com_port}...")
    try:
        ser = serial.Serial(com_port, BAUD_RATE, timeout=1)
        time.sleep(2)  # Tunggu Nano reset setelah koneksi dibuka
        ser.flushInput()
    except serial.SerialException as e:
        print(f"  ❌ Gagal membuka port {com_port}: {e}")
        input("  Tekan Enter untuk keluar...")
        sys.exit(1)

    print(f"  ✅ Terhubung ke Arduino Nano di {com_port}")
    print()
    print("  ─" * 30)
    print("  🟢 SIAP! Tempelkan kartu ke Arduino Nano.")
    print("     Lalu klik tombol [Scan RFID] di browser.")
    print("     Tekan Ctrl+C untuk berhenti.")
    print("  ─" * 30)
    print()

    scan_count = 0
    try:
        while True:
            if ser.in_waiting > 0:
                raw = ser.readline().decode('utf-8', errors='ignore').strip()
                if not raw:
                    continue

                # Debug: tampilkan semua data dari Nano
                if raw.startswith("INFO:") or raw.startswith("READY:") or raw.startswith("ERROR:"):
                    print(f"  [Nano] {raw}")
                    continue

                # Proses UID
                if raw.startswith("UID:"):
                    uid = raw[4:].strip().upper()
                    scan_count += 1
                    print(f"\n  📡 Kartu #{scan_count} terdeteksi! UID: {uid}")
                    send_to_buffer(uid)

    except KeyboardInterrupt:
        print(f"\n\n  Program dihentikan. Total scan: {scan_count}")
    finally:
        if ser.is_open:
            ser.close()
        print("  Koneksi serial ditutup. Sampai jumpa!")

if __name__ == "__main__":
    main()
