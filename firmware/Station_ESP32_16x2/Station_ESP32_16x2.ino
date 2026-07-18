// ============================================================
//  FIRMWARE ESP32 - PEMBDAHUB ATTENDANCE STATION
//  ESP32 + RC522 (RFID) + LCD 16x2 I2C + Buzzer + LED + MP3
//  Tanpa QR Scanner, Tanpa Tombol Mode
//
//  LOGIKA KERJA:
//  - Satu mode saja: ABSENSI
//  - Kartu TERDAFTAR → absensi normal (CHECK_IN / CHECK_OUT)
//  - Kartu BELUM TERDAFTAR → server kirim action_code "NEW_CARD"
//    → LCD tampilkan "Daftarkan di Admin"
//    → Admin buka halaman web → klik "Daftarkan RFID" 
//    → Modal muncul dengan UID sudah terisi (via scan-buffer)
//
//  WIRING ESP32:
//  ┌────────────┬──────────┬──────────────────────────────┐
//  │ Komponen   │ GPIO     │ Catatan                      │
//  ├────────────┼──────────┼──────────────────────────────┤
//  │ RFID SDA   │ GPIO 5   │ SPI CS                       │
//  │ RFID SCK   │ GPIO 18  │ SPI CLK (VSPI default)       │
//  │ RFID MOSI  │ GPIO 23  │ SPI MOSI (VSPI default)      │
//  │ RFID MISO  │ GPIO 19  │ SPI MISO (VSPI default)      │
//  │ RFID RST   │ GPIO 4   │ RFID Reset                   │
//  │ LCD SDA    │ GPIO 21  │ I2C SDA default ESP32        │
//  │ LCD SCL    │ GPIO 22  │ I2C SCL default ESP32        │
//  │ Buzzer (+) │ GPIO 2   │ Buzzer aktif HIGH            │
//  │ LED Green  │ GPIO 15  │ LED Hijau (berhasil)         │
//  │ LED Red    │ GPIO 13  │ LED Merah (gagal)            │
//  │ MP3 RX     │ GPIO 17  │ TX2 ESP32 → RX DFPlayer      │
//  │ MP3 TX     │ GPIO 16  │ RX2 ESP32 → TX DFPlayer (opt)│
//  │ VCC        │ 3.3V     │ RFID & LCD = 3.3V!           │
//  │ MP3 VCC    │ 5V       │ DFPlayer Mini pakai 5V!      │
//  │ GND        │ GND      │ Common ground semua          │
//  └────────────┴──────────┴──────────────────────────────┘
//
//  WIRING MP3 PLAYER (DFPlayer Mini):
//  ┌──────────────────────────────────────────────────────┐
//  │  ESP32 GPIO17 (TX2) ──[1KΩ]──→ DFPlayer RX          │
//  │  ESP32 GPIO16 (RX2) ←──────── DFPlayer TX (opsional)│
//  │  ESP32 5V ────────────────────→ DFPlayer VCC         │
//  │  ESP32 GND ───────────────────→ DFPlayer GND         │
//  │  DFPlayer SPK_1 ──────────────→ Speaker (+)          │
//  │  DFPlayer SPK_2 ──────────────→ Speaker (-)          │
//  └──────────────────────────────────────────────────────┘
//  CATATAN: Resistor 1KΩ antara TX2 dan DFPlayer RX
//           untuk proteksi level (3.3V → 5V tolerant).
//
//  FILE MP3 DI SD CARD:
//  SD Card/
//  └── 01/              ← Folder 01
//      ├── 001.mp3      → "Selamat Pagi Silahkan Absen"
//      ├── 002.mp3      → "Akses Diterima Selamat Belajar"
//      ├── 003.mp3      → "Absen Pulang Sampai Jumpa Besok Pagi"
//      ├── 004.mp3      → "Absen Sudah Tercatat Terima Kasih"
//      ├── 005.mp3      → "Kartu Tidak Dikenali Hubungi Admin"
//      └── 006.mp3      → "Sistem Ada Gangguan Hubungi Admin"
//
//  BOARD SETTING DI ARDUINO IDE:
//  - Board         : "ESP32 Dev Module"
//  - Flash Size    : "4MB (Sketch 1.2MB / OTA ~1.5MB)"
//  - CPU Frequency : "240MHz"
//  - Upload Speed  : 115200
//  - Board Manager URL:
//    https://raw.githubusercontent.com/espressif/arduino-esp32/
//    gh-pages/package_esp32_index.json
//
//  LIBRARY YANG DIBUTUHKAN (Install via Library Manager):
//  - MFRC522 by GithubCommunity
//  - LiquidCrystal I2C by Frank de Brabander
//  - ArduinoJson by Benoit Blanchon (v6.x)
// ============================================================

#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <WiFiClientSecure.h>
#include <HardwareSerial.h>

// ============================================================
//  KONFIGURASI - Sesuaikan untuk setiap station!
// ============================================================

// WiFi Utama
const char* WIFI_SSID          = "VistaHotLine";
const char* WIFI_PASSWORD      = "pelita31";

// WiFi Alternatif (otomatis fallback jika utama gagal)
const char* WIFI_ALT_SSID      = "Xspace";
const char* WIFI_ALT_PASSWORD  = "12345678starlink";

// WiFi Alternatif 2
const char* WIFI_ALT2_SSID     = "VISTAFAMILY";
const char* WIFI_ALT2_PASSWORD = "pelita31";

// Server API - JANGAN DIUBAH kecuali domain berubah
const char* SERVER_URL        = "https://perguruanpembda.com/api/attendance/rfid-scan";
const char* SCAN_BUFFER_URL   = "https://perguruanpembda.com/api/rfid/scan-buffer";
const char* KIOSK_API_KEY     = "RAHASIA-PEMBDAHUB-12345";

// ── GANTI DEVICE_ID UNTUK SETIAP STATION! ──
// Contoh: "KIOSK-ESP32-01", "KIOSK-ESP32-02", dst.
const char* DEVICE_ID         = "KIOSK-NEW-03";

// ============================================================
//  PIN DEFINITIONS - ESP32 Dev Module
// ============================================================

// SPI Pins untuk RFID RC522 (menggunakan VSPI default ESP32)
// SCK  = GPIO 18 - otomatis
// MISO = GPIO 19 - otomatis
// MOSI = GPIO 23 - otomatis
#define RFID_SS_PIN    5    // GPIO 5  - SPI CS
#define RFID_RST_PIN   4    // GPIO 4  - RFID Reset

// Indikator
#define BUZZER_PIN     2    // GPIO 2  - Buzzer
#define LED_GREEN      15   // GPIO 15 - LED Hijau
#define LED_RED        13   // GPIO 13 - LED Merah

// I2C LCD 16x2 (menggunakan pin I2C default ESP32)
// SDA = GPIO 21 - otomatis
// SCL = GPIO 22 - otomatis
#define LCD_ADDRESS    0x27
#define LCD_COLS       16
#define LCD_ROWS       2

// MP3 Player (DFPlayer Mini) via Hardware Serial2
// ESP32 punya 3 UART hardware: Serial(0), Serial1, Serial2
// Serial2 default: RX2=GPIO16, TX2=GPIO17
#define MP3_TX_PIN     17   // GPIO 17 - TX2 → hubungkan ke RX DFPlayer via R 1KΩ
#define MP3_RX_PIN     16   // GPIO 16 - RX2 ← hubungkan ke TX DFPlayer (opsional)
#define MP3_VOLUME     22   // Tingkat volume MP3 (0 s.d 30)

// ============================================================
//  TIMEOUTS & COOLDOWNS
// ============================================================
#define HTTP_TIMEOUT        8000   // 8 detik timeout HTTP
#define DISPLAY_RESULT_MS   3000   // Durasi tampil hasil di LCD
#define SCAN_COOLDOWN_MS    3000   // Anti double-tap (3 detik)

// ============================================================
//  GLOBAL STATE
// ============================================================
String        lastUID       = "";
unsigned long lastTapTime   = 0;
unsigned long lastWiFiCheck = 0;
bool          isOnline      = false;
int           currentWifi   = 0;  // 0 = utama, 1 = alternatif

// ============================================================
//  OBJEK HARDWARE
// ============================================================
MFRC522           rfid(RFID_SS_PIN, RFID_RST_PIN);
LiquidCrystal_I2C lcd(LCD_ADDRESS, LCD_COLS, LCD_ROWS);
HardwareSerial    mp3Serial(2);  // UART2 ESP32 untuk MP3 Player

// ============================================================
//  SETUP
// ============================================================
void setup() {
  Serial.begin(115200);
  delay(1000);
  Serial.println(F("\n=== ESP32 STATION BOOTING ==="));
  Serial.println(F("Board: ESP32 Dev Module"));
  Serial.print(F("Device ID: ")); Serial.println(DEVICE_ID);
  Serial.print(F("Free Heap: ")); Serial.println(ESP.getFreeHeap());

  // Inisialisasi Pin Indikator
  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(LED_GREEN,  OUTPUT);
  pinMode(LED_RED,    OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);
  digitalWrite(LED_GREEN,  LOW);
  digitalWrite(LED_RED,    LOW);

  // Inisialisasi I2C LCD 16x2 (SDA=GPIO21, SCL=GPIO22)
  Wire.begin(21, 22);
  lcd.init();
  lcd.backlight();
  lcd.setCursor(0, 0); lcd.print(F("   PEMBDA HUB   "));
  lcd.setCursor(0, 1); lcd.print(F("  Silakan Scan  "));
  Serial.println(F("LCD 16x2 OK."));
  delay(2000);

  // Inisialisasi SPI & RFID RC522
  SPI.begin();
  rfid.PCD_Init();
  delay(100);

  Serial.println(F("Mengecek modul RFID RC522..."));
  rfid.PCD_DumpVersionToSerial();

  // Cek apakah RFID terbaca
  byte version = rfid.PCD_ReadRegister(rfid.VersionReg);
  if (version == 0x00 || version == 0xFF) {
    Serial.println(F("WARNING: RFID tidak terdeteksi! Cek wiring SPI."));
    Serial.println(F("  SS=GPIO5, SCK=GPIO18, MOSI=GPIO23, MISO=GPIO19, RST=GPIO4"));
    lcd.clear();
    lcd.setCursor(0, 0); lcd.print(F("!! RFID ERROR !!"));
    lcd.setCursor(0, 1); lcd.print(F("Cek kabel SPI   "));
    beep(5, 100);
    delay(3000);
  } else {
    Serial.print(F("RFID Firmware Version: 0x"));
    Serial.print(version, HEX);
    if (version == 0x91 || version == 0x92) Serial.println(F(" (MFRC522 original)"));
    else if (version == 0x88)               Serial.println(F(" (FM17522 clone)"));
    else if (version == 0xB2)               Serial.println(F(" (MFRC522 clone - OK)"));
    else                                    Serial.println(F(" (compatible)"));

    // Naikkan antenna gain ke MAXIMUM untuk clone chip
    rfid.PCD_SetAntennaGain(rfid.RxGain_max);
    delay(10);
    rfid.PCD_AntennaOn();
    Serial.println(F("RFID Antenna Gain: MAX 48dB"));
  }

  // Koneksi WiFi
  connectWiFi();
  isOnline = (WiFi.status() == WL_CONNECTED);

  // ── INISIALISASI MP3 PLAYER (DFPlayer Mini) ──
  // ESP32 Serial2: RX2=GPIO16, TX2=GPIO17
  mp3Serial.begin(9600, SERIAL_8N1, MP3_RX_PIN, MP3_TX_PIN);
  delay(500);  // DFPlayer butuh ~300ms untuk boot
  // Buang noise awal
  while (mp3Serial.available()) mp3Serial.read();

  // Set volume MP3
  setMp3Volume(MP3_VOLUME);
  delay(100);

  // Putar lagu pembuka 001.mp3 ("Selamat Pagi Silahkan Absen")
  playAudio(1, 1);
  Serial.println(F("MP3 Player OK. Volume: 22/30"));

  Serial.print(F("Free Heap setelah init: ")); Serial.println(ESP.getFreeHeap());
  Serial.println(F("System Ready. Silakan scan kartu RFID."));

  showReady();
}

// ============================================================
//  LOOP UTAMA
// ============================================================
void loop() {
  unsigned long now = millis();

  // Periksa status WiFi setiap 15 detik secara non-blocking
  if (now - lastWiFiCheck >= 15000 || lastWiFiCheck == 0) {
    lastWiFiCheck = now;
    bool connected = (WiFi.status() == WL_CONNECTED);
    if (connected && !isOnline) {
      isOnline = true;
      Serial.println(F("WiFi Terhubung Kembali."));
      showReady();
    } else if (!connected && isOnline) {
      isOnline = false;
      Serial.println(F("WiFi Terputus! Mencoba reconnect..."));
      WiFi.reconnect();
      showReady();
    }
  }

  // Cek RFID
  handleRfidScan();

  delay(20);
}

// ============================================================
//  HANDLER RFID SCAN
// ============================================================
void handleRfidScan() {
  // Cek ada kartu baru di field
  if (!rfid.PICC_IsNewCardPresent()) return;

  // Coba baca serial (retry 1x untuk clone chip)
  if (!rfid.PICC_ReadCardSerial()) {
    delay(10);
    if (!rfid.PICC_ReadCardSerial()) return;
  }

  Serial.println(F("Kartu RFID terdeteksi!"));
  String uid = getRfidUID();
  Serial.println("RFID UID: " + uid);

  // Anti double-tap: abaikan UID sama dalam cooldown window
  unsigned long now = millis();
  if (uid == lastUID && (now - lastTapTime) < SCAN_COOLDOWN_MS) {
    Serial.println(F("RFID cooldown, abaikan."));
    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();
    return;
  }
  lastUID     = uid;
  lastTapTime = now;

  // Halt kartu SEBELUM HTTP (cegah re-trigger selama request berlangsung)
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();

  // Tampilkan "Memproses..." di LCD
  lcd.clear();
  lcd.setCursor(0, 0); lcd.print(F("  MEMPROSES...  "));
  lcd.setCursor(0, 1); lcd.print(uid.substring(0, 16));
  beep(1, 80);

  // Kirim ke server
  sendToServer(uid, "rfid");

  // Reset lastUID agar kartu yang sama bisa scan lagi (check-out)
  lastUID = "";
}

// ============================================================
//  KIRIM DATA KE SERVER (HTTPS)
// ============================================================
void sendToServer(String uid, String type) {
  Serial.println("Mengirim ke server: " + uid + " (" + type + ")");
  Serial.print(F("Free Heap sebelum HTTPS: ")); Serial.println(ESP.getFreeHeap());

  if (WiFi.status() != WL_CONNECTED) {
    playAudio(1, 7); // 007.MP3 : "Sistem Ada Gangguan Hubungi Admin"
    showError("Tidak ada WiFi!");
    return;
  }

  WiFiClientSecure client;
  client.setInsecure();  // Skip verifikasi SSL cert

  HTTPClient http;
  http.begin(client, SERVER_URL);
  http.addHeader("Content-Type",    "application/json");
  http.addHeader("X-Kiosk-API-Key", KIOSK_API_KEY);
  http.addHeader("Accept",          "application/json");
  http.setTimeout(HTTP_TIMEOUT);

  // Buat JSON request body
  StaticJsonDocument<128> doc;
  doc["uid"]       = uid;
  doc["type"]      = type;
  doc["device_id"] = DEVICE_ID;
  String body;
  serializeJson(doc, body);

  Serial.println("POST body: " + body);
  int code = http.POST(body);
  Serial.print("HTTP Response Code: "); Serial.println(code);
  Serial.print(F("Free Heap setelah POST: ")); Serial.println(ESP.getFreeHeap());

  if (code == 200 || code == 201) {
    String payload = http.getString();
    Serial.println("Server Response: " + payload);
    parseAndDisplay(payload, uid);
  } else if (code == 401) {
    playAudio(1, 7); // 007.MP3 : "Sistem Ada Gangguan Hubungi Admin"
    showError("API Key Salah!");
  } else if (code == 404) {
    playAudio(1, 6); // 006.MP3 : "Kartu Tidak Dikenali Hubungi Admin"
    showError("Kartu Tdk Drftrkn");
  } else if (code > 0) {
    playAudio(1, 7); // 007.MP3 : "Sistem Ada Gangguan Hubungi Admin"
    showError("Server: " + String(code));
  } else {
    String errMsg = http.errorToString(code);
    Serial.println("HTTP Error: " + errMsg);
    // Coba baca body meski error code
    String payload = http.getString();
    if (payload.length() > 10 && payload.indexOf("status") > 0) {
      Serial.println("Response walau error: " + payload);
      parseAndDisplay(payload, uid);
    } else {
      playAudio(1, 7); // 007.MP3 : "Sistem Ada Gangguan Hubungi Admin"
      showError("Koneksi Gagal");
    }
  }

  http.end();

  Serial.print(F("Free Heap setelah http.end: ")); Serial.println(ESP.getFreeHeap());
  delay(DISPLAY_RESULT_MS);
  showReady();
}

// ============================================================
//  KIRIM UID KE SCAN BUFFER (untuk fitur daftar RFID di web)
//  Dipanggil saat action_code == "NEW_CARD"
//  Admin buka halaman daftar → UID sudah muncul otomatis di modal
// ============================================================
void sendScanBuffer(String uid) {
  if (WiFi.status() != WL_CONNECTED) return;

  Serial.println(F("Mengirim UID ke scan-buffer..."));

  WiFiClientSecure client;
  client.setInsecure();

  HTTPClient http;
  http.begin(client, SCAN_BUFFER_URL);
  http.addHeader("Content-Type",    "application/json");
  http.addHeader("X-Kiosk-API-Key", KIOSK_API_KEY);
  http.setTimeout(5000);

  StaticJsonDocument<64> doc;
  doc["uid"] = uid;
  String body;
  serializeJson(doc, body);

  int code = http.POST(body);
  Serial.print(F("Scan-buffer response: ")); Serial.println(code);

  http.end();
}

// ============================================================
//  PARSE RESPONSE JSON & TAMPILKAN DI LCD 16x2
// ============================================================
void parseAndDisplay(String json, String uid) {
  StaticJsonDocument<256> doc;
  if (deserializeJson(doc, json)) {
    showError("Data Rusak");
    return;
  }

  String status      = doc["status"]      | "error";
  String nama        = doc["nama"]        | "Tidak dikenal";
  String kelas       = doc["kelas"]       | "";
  String message     = doc["message"]     | "";
  String waktu       = doc["waktu"]       | "";
  String action_code = doc["action_code"] | "";

  // Potong nama agar pas di 16 karakter
  String namaDisplay  = nama.substring(0, min((int)nama.length(), 16));
  String kelasDisplay = kelas.substring(0, min((int)kelas.length(), 16));

  if (status == "success" || status == "info") {

    if (action_code == "CHECK_IN") {
      // ─ Baris 0: Nama
      // ─ Baris 1: MASUK HH:MM
      lcd.clear();
      lcd.setCursor(0, 0); lcd.print(namaDisplay);
      lcd.setCursor(0, 1); lcd.print("MASUK: " + waktu.substring(0, 8));
      // Bedakan audio: Guru/Staf → 005, Siswa → 002
      if (kelasDisplay.indexOf("Guru") >= 0 || kelasDisplay.indexOf("Staf") >= 0) {
        playAudio(1, 5); // 005.MP3 : "Selamat Pagi Selamat Bekerja"
      } else {
        playAudio(1, 2); // 002.MP3 : "Akses Diterima Selamat Belajar"
      }
      indicatorCheckIn();
    }
    else if (action_code == "CHECK_OUT") {
      // ─ Baris 0: Nama
      // ─ Baris 1: PULANG HH:MM
      lcd.clear();
      lcd.setCursor(0, 0); lcd.print(namaDisplay);
      lcd.setCursor(0, 1); lcd.print("PULANG: " + waktu.substring(0, 7));
      playAudio(1, 3); // 003.MP3 : "Absen Pulang Sampai Jumpa Besok Pagi"
      indicatorCheckOut();
    }
    else if (action_code == "COOLDOWN") {
      // ─ Baris 0: Nama
      // ─ Baris 1: Sudah absen jam XX
      lcd.clear();
      lcd.setCursor(0, 0); lcd.print(namaDisplay);
      if (waktu != "") {
        lcd.setCursor(0, 1); lcd.print("Sdh absen " + waktu.substring(0, 5));
      } else {
        lcd.setCursor(0, 1); lcd.print(message.substring(0, 16));
      }
      playAudio(1, 4); // 004.MP3 : "Absen Sudah Tercatat Terima Kasih"
      indicatorCooldown();
    }
    else if (action_code == "NEW_CARD") {
      // Kartu belum terdaftar → kirim UID ke scan-buffer agar
      // muncul otomatis di modal pendaftaran web admin
      sendScanBuffer(uid);

      lcd.clear();
      lcd.setCursor(0, 0); lcd.print(F("** KARTU BARU **"));
      lcd.setCursor(0, 1); lcd.print(F("Daftar di Admin!"));
      playAudio(1, 6); // 006.MP3 : "Kartu Tidak Dikenali Hubungi Admin"
      indicatorNewCard();
    }
    else {
      // Status lain yang tidak dikenal
      lcd.clear();
      lcd.setCursor(0, 0); lcd.print(namaDisplay);
      lcd.setCursor(0, 1); lcd.print(message.substring(0, 16));
      playAudio(1, 2); // Default = pola check-in
      indicatorCheckIn();
    }

  } else {
    // Status error dari server
    String errMsg;
    if (action_code == "ALREADY_ATTENDED") {
      errMsg = "Sudah Absen!";
      playAudio(1, 4); // 004.MP3 : "Absen Sudah Tercatat Terima Kasih"
    } else {
      errMsg = message.substring(0, 16);
      playAudio(1, 7); // 007.MP3 : "Sistem Ada Gangguan Hubungi Admin"
    }
    showError(errMsg);
  }
}

// ============================================================
//  KONEKSI WIFI (dengan fallback ke SSID alternatif)
// ============================================================
void connectWiFi() {
  WiFi.mode(WIFI_STA);

  // Daftar semua WiFi yang akan dicoba berurutan
  struct WifiEntry {
    const char* ssid;
    const char* pass;
  };
  WifiEntry wifiList[] = {
    { WIFI_SSID,       WIFI_PASSWORD       },
    { WIFI_ALT_SSID,   WIFI_ALT_PASSWORD   },
    { WIFI_ALT2_SSID,  WIFI_ALT2_PASSWORD  }
  };
  const int wifiCount = 3;

  for (int w = 0; w < wifiCount; w++) {
    Serial.print(F("Mencoba WiFi: "));
    Serial.println(wifiList[w].ssid);

    lcd.clear();
    lcd.setCursor(0, 0); lcd.print(F("Menghub. WiFi..."));
    lcd.setCursor(0, 1); lcd.print(String(wifiList[w].ssid).substring(0, 16));

    WiFi.disconnect(true);
    delay(200);
    WiFi.begin(wifiList[w].ssid, wifiList[w].pass);

    int attempt = 0;
    int maxAttempt = (w == 0) ? 20 : 14;  // Utama 10s, alternatif 7s
    while (WiFi.status() != WL_CONNECTED && attempt < maxAttempt) {
      delay(500);
      Serial.print(".");
      attempt++;
      String dots = "";
      for (int i = 0; i < (attempt % 5) + 1; i++) dots += ".";
      lcd.setCursor(0, 1); lcd.print(String(wifiList[w].ssid).substring(0, 11) + dots + "    ");
    }

    if (WiFi.status() == WL_CONNECTED) break;  // Berhasil, hentikan loop
    Serial.println(F(" Gagal."));
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nIP: " + WiFi.localIP().toString());
    Serial.println("SSID: " + WiFi.SSID());
    Serial.print("RSSI: "); Serial.print(WiFi.RSSI()); Serial.println(" dBm");

    lcd.clear();
    lcd.setCursor(0, 0); lcd.print(F("WiFi Terhubung! "));
    lcd.setCursor(0, 1); lcd.print(WiFi.localIP().toString());
    beep(1, 200);
    delay(2000);
  } else {
    Serial.println(F("\nWiFi Gagal! Mode offline."));
    lcd.clear();
    lcd.setCursor(0, 0); lcd.print(F("!! WiFi GAGAL !!"));
    lcd.setCursor(0, 1); lcd.print(F("Cek SSID/Sandi  "));
    beep(3, 200);
    delay(2000);
  }
}

// ============================================================
//  FUNGSI DISPLAY LCD 16x2
// ============================================================

void showReady() {
  lcd.clear();
  lcd.setCursor(0, 0); lcd.print(F("   PEMBDA HUB   "));
  if (isOnline) {
    lcd.setCursor(0, 1); lcd.print(F("  Silakan Scan  "));
  } else {
    lcd.setCursor(0, 1); lcd.print(F("  [ OFFLINE ]   "));
  }
}

void showError(String msg) {
  lcd.clear();
  lcd.setCursor(0, 0); lcd.print(F("!! ERROR !!     "));
  lcd.setCursor(0, 1); lcd.print(msg.substring(0, 16));
  indicatorFail();
}

// ============================================================
//  INDIKATOR (Buzzer + LED)
//
//  Pola Buzzer sama seperti NodeMCU V3:
//  ┌────────────┬──────────────────────────────────────┐
//  │ Status     │ Pola Buzzer        │ LED              │
//  ├────────────┼────────────────────┼──────────────────┤
//  │ CHECK_IN   │ ♪♪  (2x 100ms)    │ Hijau 200ms      │
//  │ CHECK_OUT  │ ♪♪♪ (3x 80ms)     │ Hijau 200ms      │
//  │ COOLDOWN   │ ♪── (1x 300ms)    │ Merah 200ms      │
//  │ NEW_CARD   │ ♪♪♪♪(4x 60ms)     │ Merah kedip 4x   │
//  │ ERROR/FAIL │ ♪─── (1x 600ms)   │ Merah 300ms      │
//  └────────────┴────────────────────┴──────────────────┘
// ============================================================

// CHECK_IN: 2 beep pendek "tit-tit" + LED Hijau
void indicatorCheckIn() {
  digitalWrite(LED_GREEN, HIGH);
  for (int i = 0; i < 2; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(100);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < 1) delay(100);
  }
  delay(200);
  digitalWrite(LED_GREEN, LOW);
}

// CHECK_OUT: 3 beep cepat "tit-tit-tit" + LED Hijau
void indicatorCheckOut() {
  digitalWrite(LED_GREEN, HIGH);
  for (int i = 0; i < 3; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(80);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < 2) delay(80);
  }
  delay(200);
  digitalWrite(LED_GREEN, LOW);
}

// COOLDOWN: 1 beep panjang "tiiiit" + LED Merah
void indicatorCooldown() {
  digitalWrite(LED_RED, HIGH);
  digitalWrite(BUZZER_PIN, HIGH);
  delay(300);
  digitalWrite(BUZZER_PIN, LOW);
  delay(200);
  digitalWrite(LED_RED, LOW);
}

// NEW_CARD: 4 beep sangat cepat + LED Merah kedip
void indicatorNewCard() {
  for (int i = 0; i < 4; i++) {
    digitalWrite(LED_RED, HIGH);
    digitalWrite(BUZZER_PIN, HIGH);
    delay(60);
    digitalWrite(BUZZER_PIN, LOW);
    digitalWrite(LED_RED, LOW);
    if (i < 3) delay(60);
  }
}

// ERROR/FAIL: 1 beep panjang sekali + LED Merah
void indicatorFail() {
  digitalWrite(LED_RED, HIGH);
  digitalWrite(BUZZER_PIN, HIGH);
  delay(600);
  digitalWrite(BUZZER_PIN, LOW);
  delay(200);
  digitalWrite(LED_RED, LOW);
}

// Beep generik (untuk boot, WiFi, dll.)
void beep(int count, int duration) {
  for (int i = 0; i < count; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(duration);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < count - 1) delay(100);
  }
}

// ============================================================
//  FUNGSI MP3 PLAYER (DFPlayer Mini) via Hardware Serial2
//
//  Menggunakan protokol serial DFPlayer Mini:
//  [0x7E][0xFF][0x06][CMD][0x00][PAR1][PAR2][0xEF]
//
//  Daftar Audio:
//  ┌───────┬──────────────────────────────────────────────┐
//  │ Track │ Isi Audio                                    │
//  ├───────┼──────────────────────────────────────────────┤
//  │ 001   │ "Selamat Pagi Silahkan Absen"                │
//  │ 002   │ "Akses Diterima Selamat Belajar"             │
//  │ 003   │ "Absen Pulang Sampai Jumpa Besok Pagi"       │
//  │ 004   │ "Absen Sudah Tercatat Terima Kasih"          │
//  │ 005   │ "Kartu Tidak Dikenali Hubungi Admin"         │
//  │ 006   │ "Sistem Ada Gangguan Hubungi Admin"          │
//  └───────┴──────────────────────────────────────────────┘
// ============================================================

// Kirim perintah raw byte ke DFPlayer Mini via Serial2
void sendMp3Command(uint8_t cmd, uint8_t para1, uint8_t para2) {
  uint8_t cmdBuffer[8] = { 0x7E, 0xFF, 0x06, cmd, 0x00, para1, para2, 0xEF };
  mp3Serial.write(cmdBuffer, 8);
  delay(100); // Jeda aman pemrosesan chip DFPlayer (clone butuh lebih lama)
}

// Putar track tertentu di folder tertentu (1-indexed)
// Contoh: playAudio(1, 2) = putar folder 01, file 002.mp3
void playAudio(uint8_t folder, uint8_t track) {
  sendMp3Command(0x0F, folder, track);
}

// Atur volume (0-30)
void setMp3Volume(uint8_t vol) {
  if (vol > 30) vol = 30;
  sendMp3Command(0x06, 0x00, vol);
}

// ============================================================
//  BACA UID RFID (format HEX uppercase, contoh: "A3B2C1D4")
// ============================================================
String getRfidUID() {
  String uid = "0000";
  for (byte i = 0; i < 4; i++) {
    if (rfid.uid.uidByte[i] < 0x10) uid += "0";
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  return uid;
}
