#include <HardwareSerial.h>

// Gunakan Serial2 pada ESP32
HardwareSerial mp3Serial(2);

// Definisi PIN
#define MP3_TX_PIN     17   // Sambungkan ke RX DFPlayer
#define MP3_RX_PIN     16   // Sambungkan ke TX DFPlayer (Opsional)

// Fungsi untuk mengirim perintah ke DFPlayer (Mode 8-byte tanpa checksum)
void sendMp3Command(uint8_t cmd, uint8_t para1, uint8_t para2) {
  uint8_t cmdBuffer[8] = { 0x7E, 0xFF, 0x06, cmd, 0x00, para1, para2, 0xEF };
  mp3Serial.write(cmdBuffer, 8);
  delay(100);
}

void setup() {
  Serial.begin(115200);
  delay(1000);
  Serial.println("\n========================================");
  Serial.println("   TEST MANDIRI MP3 DFPLAYER (ESP32)    ");
  Serial.println("========================================");
  
  Serial.println("Inisialisasi Serial2...");
  // Baudrate 9600, RX=16, TX=17
  mp3Serial.begin(9600, SERIAL_8N1, MP3_RX_PIN, MP3_TX_PIN);
  delay(1500); // Tunggu DFPlayer menyala penuh

  Serial.println("Mengatur Volume ke 25...");
  sendMp3Command(0x06, 0x00, 25); // Command 0x06 = Set Volume
  delay(500);

  Serial.println("Memutar lagu 01/001.mp3 ...");
  sendMp3Command(0x0F, 1, 1);     // Command 0x0F = Play folder 1, track 1
}

void loop() {
  // Setiap 8 detik, akan bergantian memutar lagu 1 dan 2
  delay(8000);
  Serial.println("Memutar lagu 01/002.mp3 ...");
  sendMp3Command(0x0F, 1, 2);
  
  delay(8000);
  Serial.println("Memutar lagu 01/001.mp3 ...");
  sendMp3Command(0x0F, 1, 1);
}
