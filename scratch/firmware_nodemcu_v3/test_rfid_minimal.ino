// ============================================================
//  DIAGNOSTIC RFID - NodeMCU V3
//  Sketch minimal untuk test RFID RC522 tanpa komponen lain.
//  Upload ini dulu sebelum firmware utama!
//  
//  Wiring:
//  RFID SDA/SS → D0 (GPIO16)   ← BUKAN D8!
//  RFID SCK    → D5 (GPIO14)
//  RFID MOSI   → D7 (GPIO13)
//  RFID MISO   → D6 (GPIO12)
//  RFID RST    → D4 (GPIO2)
//  RFID VCC    → 3V3 (BUKAN 5V!)
//  RFID GND    → GND
// ============================================================

#include <SPI.h>
#include <MFRC522.h>

#define RFID_SS_PIN   16  // D0 = GPIO16
#define RFID_RST_PIN   2  // D4 = GPIO2

MFRC522 rfid(RFID_SS_PIN, RFID_RST_PIN);

void setup() {
  Serial.begin(115200);
  delay(2000);

  Serial.println(F("\n==================================="));
  Serial.println(F("  RFID DIAGNOSTIC - NodeMCU V3"));
  Serial.println(F("==================================="));
  
  // ── INIT SPI ──
  SPI.begin();
  // FIX: Turunkan SPI ke 1MHz (clone chip 0xB2 sering gagal di 4MHz default!)
  SPI.setFrequency(1000000);
  delay(50);

  // ── INIT RFID ──
  rfid.PCD_Init();
  delay(100);

  // ── CEK VERSION ──
  byte version = rfid.PCD_ReadRegister(rfid.VersionReg);
  Serial.print(F("Version Register: 0x")); Serial.println(version, HEX);

  if (version == 0x00) {
    Serial.println(F("!! GAGAL: 0x00 = tidak ada respon SPI"));
    Serial.println(F(">> Cek wiring! Terutama MISO (D6) dan SS (D0)"));
    while(1) delay(1000);
  } else if (version == 0xFF) {
    Serial.println(F("!! GAGAL: 0xFF = bus floating"));
    Serial.println(F(">> Cek MISO tidak terhubung atau short"));
    while(1) delay(1000);
  } else {
    if (version == 0x91) Serial.println(F(">> MFRC522 v1.0 (original)"));
    else if (version == 0x92) Serial.println(F(">> MFRC522 v2.0 (original)"));
    else if (version == 0x88) Serial.println(F(">> FM17522 clone"));
    else if (version == 0xB2) Serial.println(F(">> Clone 0xB2 - SPI OK!"));
    else { Serial.print(F(">> Unknown: 0x")); Serial.println(version, HEX); }
    Serial.println(F("SPI komunikasi OK!"));
  }

  // ── FIX UTAMA: NAIKKAN ANTENNA GAIN KE MAXIMUM ──
  // Clone chip 0xB2 default gain sangat rendah (~23dB)
  // RxGain_max = 0x70 = 48dB (maximum)
  rfid.PCD_SetAntennaGain(rfid.RxGain_max);
  delay(10);

  byte gain = rfid.PCD_GetAntennaGain();
  Serial.print(F("Antenna Gain: 0x")); Serial.print(gain, HEX);
  if (gain == rfid.RxGain_max) {
    Serial.println(F(" = MAX 48dB ✓"));
  } else {
    Serial.println(F(" = GAGAL set max gain! Coba turunkan SPI lebih lagi."));
  }

  // ── CEK ANTENNA ON ──
  rfid.PCD_AntennaOn();
  byte txCtrl = rfid.PCD_ReadRegister(rfid.TxControlReg);
  Serial.print(F("TxControlReg: 0x")); Serial.print(txCtrl, HEX);
  if (txCtrl & 0x03) Serial.println(F(" = Antenna ON ✓"));
  else               Serial.println(F(" = !! Antenna OFF!"));

  // ── CEK SPI READ/WRITE ──
  byte orig = rfid.PCD_ReadRegister(rfid.TModeReg);
  rfid.PCD_WriteRegister(rfid.TModeReg, 0x80);
  byte rb   = rfid.PCD_ReadRegister(rfid.TModeReg);
  rfid.PCD_WriteRegister(rfid.TModeReg, orig);
  if (rb == 0x80) Serial.println(F("SPI Read/Write Test: PASS ✓"));
  else            Serial.println(F("SPI Read/Write Test: FAIL !!"));

  Serial.println(F("==================================="));
  Serial.println(F("Tempel kartu RFID sekarang..."));
  Serial.println(F("===================================\n"));
}

void loop() {
  // Coba PICC_IsNewCardPresent setiap 100ms
  static unsigned long lastPrint = 0;
  if (millis() - lastPrint >= 2000) {
    lastPrint = millis();
    Serial.println(F("(menunggu kartu...)"));
  }

  // Cek kartu
  if (!rfid.PICC_IsNewCardPresent()) return;

  Serial.println(F(">> PICC_IsNewCardPresent() = TRUE!"));

  if (!rfid.PICC_ReadCardSerial()) {
    Serial.println(F("!! ReadCardSerial GAGAL - coba tempel lebih lama/tepat"));
    return;
  }

  // Berhasil baca
  Serial.print(F("KARTU TERDETEKSI! UID = "));
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) Serial.print("0");
    Serial.print(rfid.uid.uidByte[i], HEX);
  }
  Serial.print(F(" ("));
  Serial.print(rfid.uid.size);
  Serial.println(F(" bytes)"));

  Serial.print(F("PICC Type: "));
  MFRC522::PICC_Type piccType = rfid.PICC_GetType(rfid.uid.sak);
  Serial.println(rfid.PICC_GetTypeName(piccType));

  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
  delay(1000);  // Cooldown 1 detik
}
