// ============================================================
//  FIRMWARE NODEMCU V3 (ESP-12F) - PEMBDAHUB ATTENDANCE STATION
//  Opsi B: RFID + QR Scanner + LCD 20x4 + Buzzer (tanpa LED)
//  
//  Port dari firmware ESP32 tanpa mengubah sisi web/API.
//  Server endpoint, format JSON, dan logika bisnis 100% identik.
//
//  PERBEDAAN UTAMA DARI VERSI ESP32:
//  1. Library WiFi    : ESP8266WiFi (bukan WiFi.h)
//  2. HTTPS           : BearSSL::WiFiClientSecure (bukan WiFiClientSecure)
//  3. QR Scanner      : SoftwareSerial (bukan Hardware Serial2)
//  4. Indikator       : Buzzer saja (pin GPIO terbatas, LED dihilangkan)
//  5. Pin mapping     : Disesuaikan untuk NodeMCU V3
//  6. Memory          : Dioptimalkan untuk RAM 80KB ESP8266
//
//  WIRING DIAGRAM NODEMCU V3:
//  ┌─────────────┬──────────┬───────────────────────────────┐
//  │ Komponen    │ Pin MCU  │ Catatan                       │
//  ├─────────────┼──────────┼───────────────────────────────┤
//  │ RFID SDA/SS │ D0 (16)  │ SPI CS (D8 pull-down ganggu!) │
//  │ RFID SCK    │ D5 (14)  │ SPI CLK default               │
//  │ RFID MOSI   │ D7 (13)  │ SPI MOSI default              │
//  │ RFID MISO   │ D6 (12)  │ SPI MISO default              │
//  │ RFID RST    │ D4 (2)   │ HIGH saat boot = aman         │
//  │ LCD SDA     │ D2 (4)   │ I2C SDA default               │
//  │ LCD SCL     │ D1 (5)   │ I2C SCL default               │
//  │ QR RX       │ D3 (0)   │ SoftwareSerial, UART idle=HIGH│
//  │ Buzzer (+)  │ D8 (15)  │ Pull-down = buzzer OFF @boot  │
//  │ RFID 3.3V   │ 3V3      │ JANGAN pakai 5V untuk RC522!  │
//  │ LCD VCC     │ 3V3      │ HARUS 3.3V! (5V = I2C gagal)  │
//  │ GND         │ GND      │ Common ground semua komponen  │
//  └─────────────┴──────────┴───────────────────────────────┘
//
//  CATATAN PIN BOOT ESP8266:
//  - GPIO0  (D3): Harus HIGH saat boot. QR scanner TX idle=HIGH ✓
//  - GPIO2  (D4): Harus HIGH saat boot. RFID RST idle=HIGH ✓  
//  - GPIO15 (D8): Harus LOW saat boot. NodeMCU pull-down ✓
//  Semua persyaratan boot terpenuhi secara natural oleh idle
//  state dari peripheral yang terhubung.
//
//  BOARD SETTING DI ARDUINO IDE:
//  - Board      : "NodeMCU 1.0 (ESP-12E Module)"
//  - Flash Size : "4MB (FS:2MB OTA:~1019KB)"  
//  - CPU Freq   : 80 MHz (hemat daya) atau 160 MHz (performa)
//  - Upload     : 115200
//  - Board Manager URL: 
//    http://arduino.esp8266.com/stable/package_esp8266com_index.json
//
//  LIBRARY YANG DIBUTUHKAN (Install via Library Manager):
//  - MFRC522 by GithubCommunity
//  - LiquidCrystal I2C by Frank de Brabander  
//  - ArduinoJson by Benoit Blanchon (v6.x)
//  - EspSoftwareSerial (otomatis ada di ESP8266 core)
// ============================================================

#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <ArduinoJson.h>
#include <WiFiClientSecure.h>
#include <ESP8266WiFiMulti.h>
#include <SoftwareSerial.h>

// ============================================================
//  KONFIGURASI - Sesuaikan untuk setiap station!
// ============================================================

// WiFi Utama
const char* WIFI_SSID         = "VistaHotLine";
const char* WIFI_PASSWORD     = "pelita31";

// WiFi Alternatif (otomatis fallback jika utama gagal)
const char* WIFI_ALT_SSID     = "Xspace";
const char* WIFI_ALT_PASSWORD = "12345678starlink";

// WiFi Alternatif 2
const char* WIFI_ALT2_SSID    = "VISTAFAMILY";
const char* WIFI_ALT2_PASSWORD= "pealita31";

// Server API - JANGAN DIUBAH kecuali domain berubah
const char* SERVER_URL        = "https://perguruanpembda.com/api/attendance/rfid-scan";
const char* KIOSK_API_KEY     = "RAHASIA-PEMBDAHUB-12345";

// ── GANTI DEVICE_ID UNTUK SETIAP STATION! ──
// Station 3: "KIOSK-NODEMCU-03"
// Station 4: "KIOSK-NODEMCU-04"
const char* DEVICE_ID         = "KIOSK-NODEMCU-03";

// ============================================================
//  PIN DEFINITIONS - NodeMCU V3 (ESP-12F)
// ============================================================

// SPI Pins untuk RFID RC522 (menggunakan HSPI default ESP8266)
// SCK  = D5 (GPIO14) - otomatis
// MISO = D6 (GPIO12) - otomatis
// MOSI = D7 (GPIO13) - otomatis
#define RFID_SS_PIN    16   // D0 (GPIO16) - SPI CS (D8 pull-down ganggu SPI!)
#define RFID_RST_PIN  255   // UNUSED - Hubungkan pin RST RFID langsung ke 3.3V NodeMCU

// MP3 Player TX Pin (menggunakan SoftwareSerial bersama QR Scanner RX)
#define MP3_TX_PIN      2   // D4 (GPIO2) - Hubungkan ke RX MP3 Player via resistor 1K Ohm
#define MP3_VOLUME     22   // Tingkat volume MP3 (0 s.d 30)

// Buzzer (satu-satunya indikator - Opsi B tanpa LED)
#define BUZZER_PIN     15   // D8 (GPIO15) - pull-down bawaan = buzzer OFF saat boot

// QR Scanner GM65/GM50 via SoftwareSerial (RX only)
#define QR_RX_PIN       0   // D3 (GPIO0)  - UART idle HIGH = boot-safe

// I2C LCD 20x4 (menggunakan pin I2C default ESP8266)
// SDA = D2 (GPIO4)  - otomatis via Wire.begin()
// SCL = D1 (GPIO5)  - otomatis via Wire.begin()
#define LCD_ADDRESS    0x27
#define LCD_COLS       20
#define LCD_ROWS       4

// ============================================================
//  TIMEOUTS & COOLDOWNS
// ============================================================
#define HTTP_TIMEOUT        10000   // 10 detik (ESP8266 TLS lebih lambat)
#define DISPLAY_RESULT_MS   3500    // Durasi tampil hasil di LCD
#define SCAN_COOLDOWN_MS    3000    // Anti double-tap (3 detik)
#define QR_MIN_LENGTH       3       // Minimum panjang QR valid

// ============================================================
//  GLOBAL STATE
// ============================================================
String        lastUID          = "";
unsigned long lastTapTime      = 0;
String        qrBuffer         = "";
unsigned long qrPauseUntil     = 0;
unsigned long lastWiFiCheck    = 0;
bool          isOnline         = false;

// ============================================================
//  OBJEK HARDWARE
// ============================================================
MFRC522           rfid(RFID_SS_PIN, RFID_RST_PIN);
LiquidCrystal_I2C lcd(LCD_ADDRESS, LCD_COLS, LCD_ROWS);
ESP8266WiFiMulti  wifiMulti;
SoftwareSerial    kioskSerial(QR_RX_PIN, MP3_TX_PIN); // RX untuk QR Scanner, TX untuk MP3 Player

// ============================================================
//  SETUP
// ============================================================
void setup() {
  Serial.begin(115200);
  delay(1000);
  Serial.println(F("\n=== NODEMCU STATION BOOTING ==="));
  Serial.println(F("Board: NodeMCU V3 (ESP-12F)"));
  Serial.print(F("Device ID: ")); Serial.println(DEVICE_ID);
  Serial.print(F("Free Heap: ")); Serial.println(ESP.getFreeHeap());

  // Inisialisasi Buzzer
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);

  // Inisialisasi I2C LCD (SDA=GPIO4/D2, SCL=GPIO5/D1)
  Wire.begin(4, 5);
  lcd.init();
  lcd.backlight();
  lcd.setCursor(0, 0); lcd.print(F("===================="));
  lcd.setCursor(0, 1); lcd.print(F("   STATION ABSENI   "));
  lcd.setCursor(0, 2); lcd.print(F(" PEMBDAHUB NodeMCU  "));
  lcd.setCursor(0, 3); lcd.print(F("===================="));
  Serial.println(F("LCD OK."));
  delay(2000);

  // ── INISIALISASI SPI & RFID RC522 ──
  SPI.begin();
  // FIX KRITIS #1: Turunkan SPI ke 1MHz!
  // Clone chip 0xB2 sering gagal baca kartu di 4MHz default ESP8266.
  // Dengan 1MHz timing lebih longgar dan clone chip bekerja normal.
  SPI.setFrequency(1000000);
  delay(50);
  
  rfid.PCD_Init();
  delay(150); // Clone chip butuh waktu lebih lama untuk init

  Serial.println(F("Mengecek modul RFID RC522..."));
  rfid.PCD_DumpVersionToSerial();

  // Periksa apakah RFID terbaca (0x91/0x92=original, 0x88/0xB2=clone)
  byte version = rfid.PCD_ReadRegister(rfid.VersionReg);
  if (version == 0x00 || version == 0xFF) {
    Serial.println(F("WARNING: RFID tidak terdeteksi! Cek wiring SPI."));
    Serial.println(F("  SS  = D0 (GPIO16), SCK = D5, MOSI = D7, MISO = D6, RST = 3.3V"));
    lcd.setCursor(0, 2); lcd.print(F("RFID ERROR! Cek SPI "));
    beep(5, 100);
    delay(2000);
  } else {
    Serial.print(F("RFID Firmware Version: 0x"));
    Serial.print(version, HEX);
    if (version == 0x91 || version == 0x92) Serial.println(F(" (MFRC522 original)"));
    else if (version == 0x88) Serial.println(F(" (FM17522 clone)"));
    else if (version == 0xB2) Serial.println(F(" (MFRC522 clone - OK)"));
    else Serial.println(F(" (unknown compatible)"));

    // FIX KRITIS #2: Naikkan antenna gain ke MAXIMUM!
    // Clone 0xB2 default gain hanya ~23dB, terlalu rendah untuk baca kartu.
    // RxGain_max = 0x70 = 48dB (batas tertinggi hardware).
    rfid.PCD_SetAntennaGain(rfid.RxGain_max);
    delay(10);
    byte gain = rfid.PCD_GetAntennaGain();
    Serial.print(F("Antenna Gain: 0x")); Serial.print(gain, HEX);
    if (gain == rfid.RxGain_max) Serial.println(F(" = MAX 48dB ✓"));
    else                         Serial.println(F(" = gagal set max!"));

    // Pastikan antenna ON setelah gain diset
    rfid.PCD_AntennaOn();
  }

  // Koneksi WiFi dengan multi-AP
  wifiMulti.addAP(WIFI_SSID, WIFI_PASSWORD);
  wifiMulti.addAP(WIFI_ALT_SSID, WIFI_ALT_PASSWORD);
  wifiMulti.addAP(WIFI_ALT2_SSID, WIFI_ALT2_PASSWORD);
  connectWiFi();
  isOnline = (WiFi.status() == WL_CONNECTED);
  showReady();

  // Inisialisasi SoftwareSerial untuk Kiosk (RX=QR Scanner, TX=MP3 Player)
  kioskSerial.begin(9600);
  delay(100);
  // Buang data noise awal saat QR module boot
  while (kioskSerial.available()) kioskSerial.read();
  qrBuffer = "";

  // Inisialisasi Volume MP3 Player
  setMp3Volume(MP3_VOLUME);

  // Putar lagu pembuka 001.mp3 ("Selamat Pagi Silahkan Absen")
  playAudio(1, 1);

  Serial.print(F("Free Heap setelah init: ")); Serial.println(ESP.getFreeHeap());
  Serial.println(F("System Ready. Silakan scan kartu RFID atau QR Code."));
}

// ============================================================
//  LOOP UTAMA
// ============================================================
void loop() {
  unsigned long now = millis();

  // Periksa status WiFi setiap 10 detik secara non-blocking
  if (now - lastWiFiCheck >= 10000 || lastWiFiCheck == 0) {
    lastWiFiCheck = now;
    if (wifiMulti.run() == WL_CONNECTED) {
      if (!isOnline) {
        isOnline = true;
        Serial.println(F("WiFi Terhubung Kembali."));
        showReady();
      }
    } else {
      if (isOnline) {
        isOnline = false;
        Serial.println(F("WiFi Terputus! Mencoba mencari jaringan..."));
        showReady();
      }
    }
  }

  // 1. Cek RFID
  handleRfidScan();

  // 2. Cek QR Code (dengan perlindungan noise)
  handleQrScan();

  // Delay kecil agar WDT ESP8266 tidak trigger & WiFi stack berjalan
  // JANGAN lebih dari 100ms atau SoftwareSerial bisa kehilangan data
  delay(20);
}

// ============================================================
//  HANDLER RFID SCAN
// ============================================================
void handleRfidScan() {
  // Cek ada kartu baru di field
  if (!rfid.PICC_IsNewCardPresent()) return;

  // Coba baca serial, jika gagal coba sekali lagi (untuk clone chip)
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

  // Halt kartu DULU sebelum HTTP (cegah re-trigger selama HTTP berlangsung)
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();

  // Tampilkan di LCD
  lcd.clear();
  lcd.setCursor(0, 0); lcd.print(F("=== MEMPROSES ======"));
  lcd.setCursor(0, 1); lcd.print(F("Membaca kartu RFID  "));
  lcd.setCursor(0, 2); lcd.print("UID: " + uid);
  lcd.setCursor(0, 3); lcd.print(F("Mohon tunggu...     "));
  beep(1, 100);

  // Halt kartu SEBELUM kirim HTTP (agar tidak re-trigger selama HTTP berlangsung)
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();

  // Kirim ke server
  sendToServer(uid, "rfid");

  // Reset lastUID setelah selesai agar kartu yang sama bisa scan lagi
  // (misalnya check-out setelah check-in)
  // lastTapTime tetap diset agar cooldown 3 detik berlaku
  // lastUID di-reset ke "" agar scan kedua (check-out) bisa terdeteksi
  // setelah DISPLAY_RESULT_MS berlalu
  lastUID = "";
}

// ============================================================
//  HANDLER QR CODE - ANTI NOISE (via SoftwareSerial)
//  Membaca MAKSIMAL 5 byte per loop.
//  Hanya menerima karakter printable ASCII.
//  Jika noise terdeteksi berlebihan, pause 1 detik.
// ============================================================
void handleQrScan() {
  // Jika sedang dalam masa pause karena noise, skip & buang
  if (millis() < qrPauseUntil) {
    while (kioskSerial.available()) kioskSerial.read();
    return;
  }

  int noiseCount = 0;
  int maxRead = 5;  // Batasi baca per loop agar tidak blocking

  for (int i = 0; i < maxRead && kioskSerial.available() > 0; i++) {
    char c = kioskSerial.read();

    // Karakter akhir baris = data QR selesai
    if (c == '\r' || c == '\n') {
      if (qrBuffer.length() >= QR_MIN_LENGTH) {
        String qrData = qrBuffer;
        qrData.trim();
        qrBuffer = "";

        Serial.println(F("================================="));
        Serial.println("QR Code Terdeteksi: " + qrData);
        Serial.println(F("================================="));

        // Anti double-scan
        unsigned long now = millis();
        if (qrData == lastUID && (now - lastTapTime) < SCAN_COOLDOWN_MS) {
          Serial.println(F("QR Cooldown. Abaikan."));
          return;
        }
        lastUID     = qrData;
        lastTapTime = now;

        // Tampilkan di LCD
        lcd.clear();
        lcd.setCursor(0, 0); lcd.print(F("=== MEMPROSES ======"));
        lcd.setCursor(0, 1); lcd.print(F("Kode QR Terbaca     "));
        lcd.setCursor(0, 2); lcd.print("ID: " + qrData.substring(0, min((int)qrData.length(), 16)));
        lcd.setCursor(0, 3); lcd.print(F("Menghubungi server.."));
        beep(1, 150);

        sendToServer(qrData, "qr");

        // Reset lastUID setelah selesai
        lastUID = "";
        return;
      }
      qrBuffer = "";
    }
    // Karakter printable valid (spasi sampai tilde)
    else if (c >= 32 && c <= 126) {
      noiseCount = 0;
      if (qrBuffer.length() < 128) {
        qrBuffer += c;
      } else {
        qrBuffer = "";  // Buffer overflow, reset
      }
    }
    // Karakter noise (non-printable)
    else {
      noiseCount++;
      if (noiseCount >= 3) {
        qrPauseUntil = millis() + 1000;
        qrBuffer = "";
        while (kioskSerial.available()) kioskSerial.read();
        Serial.println(F("[QR] Noise terdeteksi, pause 1 detik."));
        return;
      }
    }
  }
}

// ============================================================
//  KIRIM DATA KE SERVER (HTTPS via BearSSL)
// ============================================================
void sendToServer(String uid, String type) {
  Serial.println("Mengirim ke server: " + uid + " (" + type + ")");
  Serial.print(F("Free Heap sebelum HTTPS: ")); Serial.println(ESP.getFreeHeap());

  if (WiFi.status() != WL_CONNECTED) {
    showError("Koneksi Internet Off");
    return;
  }

  // BearSSL WiFiClientSecure - skip validasi sertifikat
  BearSSL::WiFiClientSecure client;
  client.setInsecure();
  // Buffer TLS harus cukup besar untuk response server
  // 2048 RX untuk terima response, 512 TX untuk kirim request
  client.setBufferSizes(2048, 512);

  HTTPClient http;
  http.begin(client, String(SERVER_URL));
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
  Serial.print(F("Free Heap after POST: ")); Serial.println(ESP.getFreeHeap());

  if (code == 200 || code == 201) {
    String payload = http.getString();
    Serial.println("Server Response: " + payload);
    parseAndDisplay(payload);
  } else if (code == 401) {
    playAudio(1, 6); // 006.MP3 : "Sistem ada gangguan hubungi admin"
    showError("API Key Tidak Valid!");
  } else if (code == 404) {
    playAudio(1, 5); // 005.MP3 : "Kartu tidak dikenali hubungi admin"
    showError("Kartu/QR Tdk Terdaftar");
  } else if (code > 0) {
    playAudio(1, 6); // 006.MP3 : "Sistem ada gangguan hubungi admin"
    showError("Gagal Server: " + String(code));
  } else {
    // code < 0 = error koneksi/TLS
    String errMsg = http.errorToString(code);
    Serial.println("HTTP Error Detail: " + errMsg);
    Serial.print(F("Error Code: ")); Serial.println(code);
    
    // Coba baca response body walau error code (kadang server tetap respond)
    String payload = http.getString();
    if (payload.length() > 10 && payload.indexOf("status") > 0) {
      Serial.println("Response body ditemukan walau error: " + payload);
      parseAndDisplay(payload);
    } else {
      playAudio(1, 6); // 006.MP3 : "Sistem ada gangguan hubungi admin"
      showError("Server Error: " + errMsg.substring(0, 14));
    }
  }

  http.end();

  // Buang noise QR yang masuk selama proses HTTP
  while (kioskSerial.available()) kioskSerial.read();
  qrBuffer = "";

  Serial.print(F("Free Heap setelah HTTPS: ")); Serial.println(ESP.getFreeHeap());

  delay(DISPLAY_RESULT_MS);
  showReady();
}

// ============================================================
//  PARSE RESPONSE JSON
// ============================================================
void parseAndDisplay(String json) {
  StaticJsonDocument<256> doc;
  if (deserializeJson(doc, json)) {
    showError("Format Data Rusak");
    return;
  }

  String status      = doc["status"]      | "error";
  String nama        = doc["nama"]        | "Tidak dikenal";
  String kelas       = doc["kelas"]       | "";
  String message     = doc["message"]     | "";
  String waktu       = doc["waktu"]       | "";
  String action_code = doc["action_code"] | "";

  if (status == "success" || status == "info") {
    String namaDisplay  = nama.substring(0, min((int)nama.length(), 20));
    String kelasDisplay = kelas.substring(0, min((int)kelas.length(), 20));

    lcd.clear();
    lcd.setCursor(0, 0); lcd.print(namaDisplay);

    if (kelasDisplay != "") {
      lcd.setCursor(0, 1); lcd.print(kelasDisplay);
    } else {
      lcd.setCursor(0, 1); lcd.print("-");
    }

    // ── POLA BUZZER PEMBEDA (Opsi B - tanpa LED) ──
    // CHECK_IN  : 2 beep pendek (tit-tit)
    // CHECK_OUT : 3 beep cepat  (tit-tit-tit)
    // COOLDOWN  : 1 beep panjang (tiiiit)
    // ERROR     : 1 beep sangat panjang (tiiiiiiit)
    // NEW_CARD  : 4 beep cepat  (tit-tit-tit-tit)

    if (action_code == "CHECK_IN") {
      lcd.setCursor(0, 2); lcd.print("MASUK PADA: " + waktu);
      if (kelasDisplay.indexOf("Guru") >= 0 || kelasDisplay.indexOf("Staff") >= 0) {
        lcd.setCursor(0, 3); lcd.print(F("Selamat Bertugas!   "));
      } else {
        lcd.setCursor(0, 3); lcd.print(F("Selamat Belajar!    "));
      }
      playAudio(1, 2); // 002.MP3 : "Akses Diterima Selamat Belajar"
      indicatorCheckIn();
    }
    else if (action_code == "CHECK_OUT") {
      lcd.setCursor(0, 2); lcd.print("PULANG PADA: " + waktu);
      lcd.setCursor(0, 3); lcd.print(F("Hati-hati di jalan! "));
      playAudio(1, 3); // 003.MP3 : "Absen Pulang Sampai Jumpa Besok Pagi"
      indicatorCheckOut();
    }
    else if (action_code == "COOLDOWN") {
      lcd.setCursor(0, 2); lcd.print(F("Sudah Absen Masuk!  "));
      if (waktu != "") {
        lcd.setCursor(0, 3); lcd.print("Jam Masuk: " + waktu);
      } else {
        lcd.setCursor(0, 3); lcd.print(message.substring(0, min((int)message.length(), 20)));
      }
      playAudio(1, 4); // 004.MP3 : "Absen Hadir dan Pulang Sudah Tercatat Terima Kasih"
      indicatorCooldown();
    }
    else if (action_code == "NEW_CARD") {
      lcd.clear();
      lcd.setCursor(0, 0); lcd.print(F("** KARTU BARU **    "));
      lcd.setCursor(0, 1); lcd.print(kelasDisplay);
      lcd.setCursor(0, 2); lcd.print(F("Belum terdaftar!    "));
      lcd.setCursor(0, 3); lcd.print(F("Daftarkan di Admin  "));
      playAudio(1, 5); // 005.MP3 : "Kartu tidak dikenali hubungi admin"
      indicatorNewCard();
    }
    else {
      lcd.setCursor(0, 2); lcd.print(message.substring(0, min((int)message.length(), 20)));
      lcd.setCursor(0, 3); lcd.print("Waktu: " + waktu);
      playAudio(1, 2); // Default = pola check-in
      indicatorCheckIn();  // Default = pola check-in
    }
  } else {
    String errMsg = (action_code == "ALREADY_ATTENDED") ? "Sudah Absen Lengkap" : message;
    if (action_code == "ALREADY_ATTENDED") {
      playAudio(1, 4); // 004.MP3 : "Absen Hadir dan Pulang Sudah Tercatat Terima Kasih"
    } else {
      playAudio(1, 6); // 006.MP3 : "Sistem ada gangguan hubungi admin"
    }
    showError(errMsg);
  }
}

// ============================================================
//  KONEKSI WIFI (Multi-AP dengan fallback otomatis)
// ============================================================
void connectWiFi() {
  WiFi.mode(WIFI_STA);
  WiFi.persistent(false);  // Jangan tulis ke flash setiap koneksi (hemat flash)
  int attempt = 0;

  lcd.clear();
  lcd.setCursor(0, 0); lcd.print(F("=== MENCARI WIFI ==="));
  lcd.setCursor(0, 1); lcd.print(F("Mencoba koneksi...  "));
  lcd.setCursor(0, 2); lcd.print(F("Hubungkan WiFi...   "));
  lcd.setCursor(0, 3); lcd.print(F("--------------------"));

  while (wifiMulti.run() != WL_CONNECTED && attempt < 20) {
    delay(500);
    Serial.print(".");
    String dots = "";
    for (int i = 0; i < (attempt % 6); i++) dots += ".";
    lcd.setCursor(0, 2); lcd.print("Menghubungkan" + dots + "    ");
    attempt++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nIP: " + WiFi.localIP().toString());
    Serial.println("SSID: " + WiFi.SSID());
    Serial.print("RSSI: "); Serial.print(WiFi.RSSI()); Serial.println(" dBm");

    lcd.clear();
    lcd.setCursor(0, 0); lcd.print(F("=== WIFI CONNECT ==="));
    lcd.setCursor(0, 1); lcd.print("SSID: " + WiFi.SSID().substring(0, 14));
    lcd.setCursor(0, 2); lcd.print("IP: " + WiFi.localIP().toString());
    lcd.setCursor(0, 3); lcd.print(F("--------------------"));
    beep(1, 200);
    delay(1500);
  } else {
    Serial.println(F("\nWiFi Gagal! Akan coba lagi di background."));
    lcd.clear();
    lcd.setCursor(0, 0); lcd.print(F("=== WIFI GAGAL ====="));
    lcd.setCursor(0, 1); lcd.print(F("Koneksi gagal!      "));
    lcd.setCursor(0, 2); lcd.print(F("Auto-retry di bg... "));
    lcd.setCursor(0, 3); lcd.print(F("--------------------"));
    beep(3, 100);
    delay(2000);
  }
}

// ============================================================
//  FUNGSI DISPLAY
// ============================================================

void showReady() {
  lcd.clear();
  lcd.setCursor(0, 0); lcd.print(F("=== PEMBDA HUB ====="));
  lcd.setCursor(0, 1); lcd.print(F("    Silakan Scan    "));
  lcd.setCursor(0, 2); lcd.print(F("  Kartu / QR Code   "));
  if (isOnline) {
    lcd.setCursor(0, 3); lcd.print(F("Status: READY       "));
  } else {
    lcd.setCursor(0, 3); lcd.print(F("Status: OFFLINE     "));
  }
}

void showError(String msg) {
  lcd.clear();
  lcd.setCursor(0, 0); lcd.print(F("=== !!! ERROR !!! =="));
  if (msg.length() > 20) {
    lcd.setCursor(0, 1); lcd.print(msg.substring(0, 20));
    lcd.setCursor(0, 2); lcd.print(msg.substring(20, min((int)msg.length(), 40)));
  } else {
    lcd.setCursor(0, 1); lcd.print(msg);
    lcd.setCursor(0, 2); lcd.print(F("Silakan coba lagi   "));
  }
  lcd.setCursor(0, 3); lcd.print(F("--------------------"));
  indicatorFail();
}

// ============================================================
//  INDIKATOR BUZZER (Opsi B - Pola Berbeda Tiap Status)
//
//  Tanpa LED fisik, pola buzzer menjadi pembeda utama:
//  ┌────────────┬──────────────────────────────────────────┐
//  │ Status     │ Pola Buzzer                              │
//  ├────────────┼──────────────────────────────────────────┤
//  │ CHECK_IN   │ ♪♪   (2x pendek 100ms, jeda 100ms)      │
//  │ CHECK_OUT  │ ♪♪♪  (3x cepat 80ms, jeda 80ms)         │
//  │ COOLDOWN   │ ♪─── (1x panjang 300ms)                  │
//  │ NEW_CARD   │ ♪♪♪♪ (4x sangat cepat 60ms, jeda 60ms)  │
//  │ ERROR/FAIL │ ♪──── (1x sangat panjang 600ms)          │
//  │ Beep umum  │ Configurable via beep(count, duration)   │
//  └────────────┴──────────────────────────────────────────┘
// ============================================================

// CHECK_IN: 2 beep pendek - "tit-tit" (berhasil masuk)
void indicatorCheckIn() {
  for (int i = 0; i < 2; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(100);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < 1) delay(100);
  }
}

// CHECK_OUT: 3 beep cepat - "tit-tit-tit" (berhasil pulang)
void indicatorCheckOut() {
  for (int i = 0; i < 3; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(80);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < 2) delay(80);
  }
}

// COOLDOWN: 1 beep panjang - "tiiiit" (sudah absen, sabar)
void indicatorCooldown() {
  digitalWrite(BUZZER_PIN, HIGH);
  delay(300);
  digitalWrite(BUZZER_PIN, LOW);
}

// NEW_CARD: 4 beep sangat cepat - "tititit" (kartu belum terdaftar)
void indicatorNewCard() {
  for (int i = 0; i < 4; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(60);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < 3) delay(60);
  }
}

// ERROR/FAIL: 1 beep sangat panjang - "tiiiiiiiit" (ada masalah)
void indicatorFail() {
  digitalWrite(BUZZER_PIN, HIGH);
  delay(600);
  digitalWrite(BUZZER_PIN, LOW);
}

// Beep generik (untuk boot, koneksi WiFi, dll.)
void beep(int count, int duration) {
  for (int i = 0; i < count; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(duration);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < count - 1) delay(100);
  }
}

// ============================================================
//  FUNGSI MP3 PLAYER RAW COMMANDS
// ============================================================

// Fungsi mengirim perintah byte mentah ke MP3 Player via kioskSerial TX
void sendMp3Command(uint8_t cmd, uint8_t para1, uint8_t para2) {
  uint8_t cmdBuffer[8] = { 0x7E, 0xFF, 0x06, cmd, 0x00, para1, para2, 0xEF };
  kioskSerial.write(cmdBuffer, 8);
  delay(100); // jeda aman pemrosesan chip clone
}

// Fungsi memutar track tertentu di folder tertentu (1-indexed)
void playAudio(uint8_t folder, uint8_t track) {
  sendMp3Command(0x0F, folder, track);
}

// Fungsi mengatur volume (0-30)
void setMp3Volume(uint8_t vol) {
  if (vol > 30) vol = 30;
  sendMp3Command(0x06, 0x00, vol);
}

// ============================================================
//  BACA UID RFID
// ============================================================
String getRfidUID() {
  String uid = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) uid += "0";
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  return uid;
}
