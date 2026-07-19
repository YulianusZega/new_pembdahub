// ============================================================
//  RFID UID SCANNER - Arduino Nano
//  Alat khusus untuk membaca UID kartu RFID/NFC
//  dan mengirimkan ke PC via USB Serial (COM Port)
//
//  WIRING DIAGRAM (Arduino Nano + RC522):
//  ┌─────────────┬────────────┬───────────────────────────┐
//  │ RC522       │ Arduino    │ Catatan                   │
//  ├─────────────┼────────────┼───────────────────────────┤
//  │ SDA (SS)    │ Pin 10     │ SPI Chip Select           │
//  │ SCK         │ Pin 13     │ SPI Clock                 │
//  │ MOSI        │ Pin 11     │ SPI MOSI                  │
//  │ MISO        │ Pin 12     │ SPI MISO                  │
//  │ RST         │ Pin 9      │ Reset                     │
//  │ 3.3V        │ 3.3V       │ ⚠️ JANGAN 5V! Rusak chip │
//  │ GND         │ GND        │ Ground                    │
//  └─────────────┴────────────┴───────────────────────────┘
//
//  BUZZER (Opsional):
//  ┌─────────────┬────────────┐
//  │ Buzzer (+)  │ Pin 7      │
//  │ Buzzer (-)  │ GND        │
//  └─────────────┴────────────┘
//
//  LED (Opsional):
//  ┌─────────────┬────────────┐
//  │ LED Hijau   │ Pin 5      │
//  │ LED Merah   │ Pin 6      │
//  └─────────────┴────────────┘
//
//  OUTPUT FORMAT via USB Serial (115200 baud):
//  Setiap kartu di-tap, Nano akan mengirim:
//  UID:A3B2C1D4
//
//  CARA PAKAI:
//  1. Upload firmware ini ke Arduino Nano
//  2. Colok ke laptop via USB
//  3. Buka halaman "Registrasi RFID Massal" di PembdaHub
//  4. Klik "Hubungkan Scanner"
//  5. Tempelkan kartu siswa → UID langsung muncul di browser!
//
//  LIBRARY YANG DIBUTUHKAN:
//  - MFRC522 by GithubCommunity (Install via Library Manager)
// ============================================================

#include <SPI.h>
#include <MFRC522.h>

// === PIN DEFINITIONS ===
#define RFID_SS_PIN   10   // Pin 10 - SPI Chip Select
#define RFID_RST_PIN   9   // Pin 9  - Reset
#define BUZZER_PIN     7   // Pin 7  - Buzzer (Opsional)
#define LED_GREEN      5   // Pin 5  - LED Hijau (Opsional)
#define LED_RED        6   // Pin 6  - LED Merah (Opsional)

// === COOLDOWN (Anti Double Tap) ===
#define SCAN_COOLDOWN_MS 2000  // 2 detik sebelum kartu yang sama bisa di-scan lagi

MFRC522 rfid(RFID_SS_PIN, RFID_RST_PIN);

String  lastUID     = "";
unsigned long lastTap = 0;
unsigned long scanCount = 0;

void setup() {
  Serial.begin(115200);
  
  // Inisialisasi pin LED & Buzzer
  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(LED_GREEN,  OUTPUT);
  pinMode(LED_RED,    OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);
  digitalWrite(LED_GREEN,  LOW);
  digitalWrite(LED_RED,    LOW);

  // Inisialisasi SPI & RFID
  SPI.begin();
  rfid.PCD_Init();
  delay(100);

  // Naikkan antenna gain ke MAX (untuk chip clone)
  rfid.PCD_SetAntennaGain(rfid.RxGain_max);

  // Cek apakah RFID terdeteksi
  byte version = rfid.PCD_ReadRegister(rfid.VersionReg);
  if (version == 0x00 || version == 0xFF) {
    Serial.println(F("ERROR:RFID_NOT_FOUND"));
    // Kedipkan LED Merah cepat sebagai tanda error
    for (int i = 0; i < 10; i++) {
      digitalWrite(LED_RED, HIGH); delay(100);
      digitalWrite(LED_RED, LOW);  delay(100);
    }
  } else {
    Serial.println(F("READY:RFID_UID_SCANNER_NANO"));
    Serial.print(F("INFO:RFID_VERSION_0x"));
    Serial.println(version, HEX);
    // Beep 1x tanda siap
    beep(1, 200);
    ledGreen(300);
  }

  Serial.println(F("INFO:Tempelkan kartu RFID untuk membaca UID..."));
}

void loop() {
  // Cek ada kartu baru
  if (!rfid.PICC_IsNewCardPresent()) return;

  // Coba baca serial (retry 1x untuk chip clone)
  if (!rfid.PICC_ReadCardSerial()) {
    delay(10);
    if (!rfid.PICC_ReadCardSerial()) return;
  }

  // Baca UID
  String uid = getUID();
  unsigned long now = millis();

  // Anti double-tap: abaikan UID sama dalam cooldown
  if (uid == lastUID && (now - lastTap) < SCAN_COOLDOWN_MS) {
    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();
    return;
  }

  lastUID = uid;
  lastTap = now;
  scanCount++;

  // Kirim UID ke PC via Serial
  Serial.println("UID:" + uid);
  Serial.println("COUNT:" + String(scanCount));

  // Indikator: Beep + LED Hijau
  beep(1, 80);
  ledGreen(400);

  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
}

// Baca UID dalam format HEX uppercase (sama persis dengan firmware station)
String getUID() {
  if (rfid.uid.size < 4) return "";
  String hex = "";
  for (int i = 3; i >= 0; i--) {
    if (rfid.uid.uidByte[i] < 0x10) hex += "0";
    hex += String(rfid.uid.uidByte[i], HEX);
  }
  hex.toUpperCase();
  return hex;
}

// Nyalakan LED Hijau selama durasi ms
void ledGreen(int durasi) {
  digitalWrite(LED_GREEN, HIGH);
  delay(durasi);
  digitalWrite(LED_GREEN, LOW);
}

// Nyalakan LED Merah selama durasi ms
void ledRed(int durasi) {
  digitalWrite(LED_RED, HIGH);
  delay(durasi);
  digitalWrite(LED_RED, LOW);
}

// Beep n kali dengan durasi ms
void beep(int kali, int durasi) {
  for (int i = 0; i < kali; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(durasi);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < kali - 1) delay(80);
  }
}
