#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <WiFiClientSecure.h>

// ============================================================
//  KONFIGURASI
// ============================================================

const char* WIFI_SSID        = "VistaHotLine";
const char* WIFI_PASSWORD    = "pelita31";
const char* SERVER_URL       = "https://perguruanpembda.com/api/attendance/rfid-scan";
const char* KIOSK_API_KEY    = "RAHASIA-PEMBDAHUB-12345";
const char* DEVICE_ID        = "KIOSK-GATE-MAIN";

// Pin Definitions
#define RFID_SS_PIN    5
#define RFID_RST_PIN   4
#define BUZZER_PIN     2
#define LED_GREEN      15
#define LED_RED        13

// I2C LCD 20x4
#define LCD_ADDRESS    0x27
#define LCD_COLS       20
#define LCD_ROWS       4

// Hardware Serial 2 untuk GM65/GM50 (hanya RX)
#define GM65_RX_PIN    16

// Timeouts & Cooldowns
#define HTTP_TIMEOUT        8000
#define DISPLAY_RESULT_MS   3500
#define SCAN_COOLDOWN_MS    2500
#define QR_MIN_LENGTH       3

// States
String        lastUID          = "";
unsigned long lastTapTime      = 0;
String        qrBuffer         = "";
unsigned long qrPauseUntil     = 0;  // Waktu pause jika terlalu banyak noise

MFRC522 rfid(RFID_SS_PIN, RFID_RST_PIN);
LiquidCrystal_I2C lcd(LCD_ADDRESS, LCD_COLS, LCD_ROWS);

// ============================================================
//  SETUP
// ============================================================

void setup() {
  Serial.begin(115200);
  delay(1000);
  Serial.println("\n=== SYSTEM BOOTING ===");

  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(LED_GREEN,  OUTPUT);
  pinMode(LED_RED,    OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);
  digitalWrite(LED_GREEN,  LOW);
  digitalWrite(LED_RED,    LOW);

  // Inisialisasi LCD
  Wire.begin(21, 22);
  lcd.init();
  lcd.backlight();
  lcd.setCursor(0, 0); lcd.print("====================");
  lcd.setCursor(0, 1); lcd.print("   STATION ABSENI   ");
  lcd.setCursor(0, 2); lcd.print("   PEMBDAHUB V2.0   ");
  lcd.setCursor(0, 3); lcd.print("====================");
  Serial.println("LCD OK.");
  delay(2000);

  // Inisialisasi RFID
  SPI.begin();
  rfid.PCD_Init();
  Serial.println("Mengecek modul RFID RC522...");
  rfid.PCD_DumpVersionToSerial();

  // Koneksi WiFi
  connectWiFi();
  showReady();

  // Inisialisasi Serial2 untuk GM65 (RX only, pin TX tidak dihubungkan)
  Serial2.begin(9600, SERIAL_8N1, GM65_RX_PIN, -1);
  // Buang data noise awal
  delay(100);
  while (Serial2.available()) Serial2.read();
  qrBuffer = "";

  Serial.println("System Ready. Silakan scan kartu RFID atau QR Code.");
}

// ============================================================
//  LOOP UTAMA
// ============================================================

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    lcd.clear();
    lcd.setCursor(0, 0); lcd.print("!!! WIFI PUTUS !!!  ");
    lcd.setCursor(0, 1); lcd.print("Menghubungkan ulang ");
    lcd.setCursor(0, 2); lcd.print("Mohon tunggu...     ");
    lcd.setCursor(0, 3); lcd.print("--------------------");
    connectWiFi();
    showReady();
  }

  // 1. Cek RFID
  handleRfidScan();

  // 2. Cek QR Code (dengan perlindungan noise ketat)
  handleQrScan();

  delay(30);
}

// ============================================================
//  HANDLER RFID SCAN
// ============================================================

void handleRfidScan() {
  if (!rfid.PICC_IsNewCardPresent()) return;
  if (!rfid.PICC_ReadCardSerial()) return;

  Serial.println("Kartu RFID terdeteksi!");
  String uid = getRfidUID();
  Serial.println("RFID UID: " + uid);

  unsigned long now = millis();
  if (uid == lastUID && (now - lastTapTime) < SCAN_COOLDOWN_MS) {
    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();
    return;
  }
  lastUID = uid;
  lastTapTime = now;

  lcd.clear();
  lcd.setCursor(0, 0); lcd.print("=== MEMPROSES ======");
  lcd.setCursor(0, 1); lcd.print("Membaca kartu RFID  ");
  lcd.setCursor(0, 2); lcd.print("UID: " + uid);
  lcd.setCursor(0, 3); lcd.print("Mohon tunggu...     ");
  beep(1, 100);

  sendToServer(uid, "rfid");

  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
}

// ============================================================
//  HANDLER QR CODE - ANTI NOISE
//  Membaca MAKSIMAL 5 byte per loop.
//  Hanya menerima karakter printable ASCII.
//  Jika noise terdeteksi berlebihan, pause 1 detik.
// ============================================================

void handleQrScan() {
  // Jika sedang dalam masa pause karena noise, skip
  if (millis() < qrPauseUntil) {
    // Buang semua data selama pause
    while (Serial2.available()) Serial2.read();
    return;
  }

  int noiseCount = 0;
  int maxRead = 5; // Hanya baca MAKSIMAL 5 byte per putaran loop

  for (int i = 0; i < maxRead && Serial2.available() > 0; i++) {
    char c = Serial2.read();

    // Karakter akhir baris = data QR selesai
    if (c == '\r' || c == '\n') {
      if (qrBuffer.length() >= QR_MIN_LENGTH) {
        String qrData = qrBuffer;
        qrData.trim();
        qrBuffer = "";

        Serial.println("=================================");
        Serial.println("QR Code Terdeteksi: " + qrData);
        Serial.println("=================================");

        unsigned long now = millis();
        if (qrData == lastUID && (now - lastTapTime) < SCAN_COOLDOWN_MS) {
          Serial.println("QR Cooldown. Abaikan.");
          return;
        }
        lastUID = qrData;
        lastTapTime = now;

        lcd.clear();
        lcd.setCursor(0, 0); lcd.print("=== MEMPROSES ======");
        lcd.setCursor(0, 1); lcd.print("Kode QR Terbaca     ");
        lcd.setCursor(0, 2); lcd.print("ID: " + qrData.substring(0, min((int)qrData.length(), 16)));
        lcd.setCursor(0, 3); lcd.print("Menghubungi server..");
        beep(1, 150);

        sendToServer(qrData, "qr");
        return;
      }
      qrBuffer = "";
    }
    // Karakter printable valid (spasi sampai tilde)
    else if (c >= 32 && c <= 126) {
      noiseCount = 0; // Reset noise counter
      if (qrBuffer.length() < 128) {
        qrBuffer += c;
      } else {
        qrBuffer = "";
      }
    }
    // Karakter noise (non-printable)
    else {
      noiseCount++;
      if (noiseCount >= 3) {
        // Terlalu banyak noise, pause 1 detik dan buang semua
        qrPauseUntil = millis() + 1000;
        qrBuffer = "";
        while (Serial2.available()) Serial2.read();
        Serial.println("[QR] Noise terdeteksi, pause 1 detik.");
        return;
      }
    }
  }
}

// ============================================================
//  KIRIM DATA KE SERVER
// ============================================================

void sendToServer(String uid, String type) {
  Serial.println("Mengirim ke server: " + uid + " (" + type + ")");

  if (WiFi.status() != WL_CONNECTED) {
    showError("Koneksi Internet Off");
    return;
  }

  WiFiClientSecure client;
  client.setInsecure();

  HTTPClient http;
  http.begin(client, SERVER_URL);
  http.addHeader("Content-Type",    "application/json");
  http.addHeader("X-Kiosk-API-Key", KIOSK_API_KEY);
  http.addHeader("Accept",          "application/json");
  http.setTimeout(HTTP_TIMEOUT);

  StaticJsonDocument<128> doc;
  doc["uid"]       = uid;
  doc["type"]      = type;
  doc["device_id"] = DEVICE_ID;
  String body;
  serializeJson(doc, body);

  int code = http.POST(body);
  Serial.println("HTTP Response Code: " + String(code));

  if (code == 200 || code == 201) {
    String payload = http.getString();
    Serial.println("Server Response: " + payload);
    parseAndDisplay(payload);
  } else if (code == 401) {
    showError("API Key Tidak Valid!");
  } else if (code == 404) {
    showError("Kartu/QR Tdk Terdaftar");
  } else if (code > 0) {
    showError("Gagal Server: " + String(code));
  } else {
    showError("Koneksi Server Gagal");
    Serial.println("HTTP Error: " + http.errorToString(code));
  }

  http.end();

  // Buang noise yang masuk selama proses kirim data
  while (Serial2.available()) Serial2.read();
  qrBuffer = "";

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

    if (action_code == "CHECK_IN") {
      lcd.setCursor(0, 2); lcd.print("MASUK PADA: " + waktu);
      if (kelasDisplay.indexOf("Guru") >= 0 || kelasDisplay.indexOf("Staff") >= 0) {
        lcd.setCursor(0, 3); lcd.print("Selamat Bertugas!   ");
      } else {
        lcd.setCursor(0, 3); lcd.print("Selamat Belajar!    ");
      }
      indicatorSuccess();
    }
    else if (action_code == "CHECK_OUT") {
      lcd.setCursor(0, 2); lcd.print("PULANG PADA: " + waktu);
      lcd.setCursor(0, 3); lcd.print("Hati-hati di jalan! ");
      indicatorSuccess();
    }
    else if (action_code == "COOLDOWN") {
      lcd.setCursor(0, 2); lcd.print("Sudah Absen Masuk!  ");
      lcd.setCursor(0, 3); lcd.print("Jam Masuk: " + waktu);
      beep(1, 300);
    }
    else if (action_code == "NEW_CARD") {
      lcd.clear();
      lcd.setCursor(0, 0); lcd.print("** KARTU BARU **    ");
      lcd.setCursor(0, 1); lcd.print(kelasDisplay);
      lcd.setCursor(0, 2); lcd.print("Belum terdaftar!    ");
      lcd.setCursor(0, 3); lcd.print("Daftarkan di Admin  ");
      // 3 beep pendek = kartu baru
      beep(3, 80);
    }
    else {
      lcd.setCursor(0, 2); lcd.print(message.substring(0, min((int)message.length(), 20)));
      lcd.setCursor(0, 3); lcd.print("Waktu: " + waktu);
      indicatorSuccess();
    }
  } else {
    String errMsg = (action_code == "ALREADY_ATTENDED") ? "Sudah Absen Lengkap" : message;
    showError(errMsg);
  }
}

// ============================================================
//  KONEKSI WIFI
// ============================================================

void connectWiFi() {
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  int attempt = 0;
  while (WiFi.status() != WL_CONNECTED && attempt < 20) {
    delay(500);
    Serial.print(".");
    String dots = "";
    for (int i = 0; i < (attempt % 6); i++) dots += ".";
    lcd.setCursor(0, 2); lcd.print("Menghubungkan" + dots + "    ");
    attempt++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nIP: " + WiFi.localIP().toString());
    lcd.clear();
    lcd.setCursor(0, 0); lcd.print("=== WIFI CONNECT ===");
    lcd.setCursor(0, 1); lcd.print("Jaringan Terhubung  ");
    lcd.setCursor(0, 2); lcd.print("IP: " + WiFi.localIP().toString());
    lcd.setCursor(0, 3); lcd.print("--------------------");
    beep(1, 200);
    delay(1500);
  } else {
    lcd.clear();
    lcd.setCursor(0, 0); lcd.print("=== WIFI GAGAL =====");
    lcd.setCursor(0, 1); lcd.print("Koneksi gagal!      ");
    lcd.setCursor(0, 2); lcd.print("Periksa SSID & Sandi");
    lcd.setCursor(0, 3); lcd.print("--------------------");
    beep(3, 300);
    delay(2000);
  }
}

// ============================================================
//  FUNGSI DISPLAY & INDIKATOR
// ============================================================

void showReady() {
  lcd.clear();
  lcd.setCursor(0, 0); lcd.print("=== PEMBDA HUB =====");
  lcd.setCursor(0, 1); lcd.print("    Silakan Scan    ");
  lcd.setCursor(0, 2); lcd.print("  Kartu / QR Code   ");
  lcd.setCursor(0, 3); lcd.print("Status: READY       ");
}

void showError(String msg) {
  lcd.clear();
  lcd.setCursor(0, 0); lcd.print("=== !!! ERROR !!! ==");
  if (msg.length() > 20) {
    lcd.setCursor(0, 1); lcd.print(msg.substring(0, 20));
    lcd.setCursor(0, 2); lcd.print(msg.substring(20, min((int)msg.length(), 40)));
  } else {
    lcd.setCursor(0, 1); lcd.print(msg);
    lcd.setCursor(0, 2); lcd.print("Silakan coba lagi   ");
  }
  lcd.setCursor(0, 3); lcd.print("--------------------");
  indicatorFail();
}

void indicatorSuccess() {
  digitalWrite(LED_RED, LOW);
  digitalWrite(LED_GREEN, HIGH);
  beep(2, 100);
  delay(200);
  digitalWrite(LED_GREEN, LOW);
}

void indicatorFail() {
  digitalWrite(LED_GREEN, LOW);
  digitalWrite(LED_RED, HIGH);
  beep(1, 500);
  delay(200);
  digitalWrite(LED_RED, LOW);
}

void beep(int count, int duration) {
  for (int i = 0; i < count; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(duration);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < count - 1) delay(100);
  }
}

String getRfidUID() {
  String uid = "0000";
  for (byte i = 0; i < 4; i++) {
    if (rfid.uid.uidByte[i] < 0x10) uid += "0";
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  return uid;
}
