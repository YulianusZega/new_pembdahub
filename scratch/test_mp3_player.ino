// ============================================================
//  FIRMWARE TEST STANDALONE - MP3-TF-16P (MH2024K-16SS) & ESP32
// ============================================================
//
//  Program ini dirancang khusus untuk menguji modul pemutar MP3
//  tiruan (clone) secara interaktif melalui Serial Monitor.
//  Menggunakan metode pengiriman perintah komunikasi serial mentah
//  (Raw Serial) tanpa membutuhkan library eksternal agar terhindar
//  dari bug jabat tangan (handshake/begin) pada chip clone.
//
//  WIRING DIAGRAM:
//  ┌──────────────────┬──────────────┬───────────────────────────────┐
//  │ MP3-TF-16P Pin   │ Pin ESP32    │ Catatan                       │
//  ├──────────────────┼──────────────┼───────────────────────────────┤
//  │ 1. VCC           │ 5V (VIN)     │ Tegangan 5V dari ESP32        │
//  │ 2. RX            │ GPIO 17 (TX2)│ Pasang RESISTOR 1K secara seri│
//  │ 3. TX            │ GPIO 16 (RX2)│ Hubungkan langsung            │
//  │ 6. SPK1          │ Speaker (+)  │ Hubungkan ke Speaker 8 ohm    │
//  │ 7. GND           │ GND          │ Common Ground                 │
//  │ 8. SPK2          │ Speaker (-)  │ Hubungkan ke Speaker 8 ohm    │
//  └──────────────────┴──────────────┴───────────────────────────────┘
//
//  STRUKTUR FILE MICROSD CARD (Wajib FAT32):
//  - Buat folder bernama "01" (tanpa kutip) di root MicroSD.
//  - Masukkan file audio MP3 Anda dan namakan sebagai:
//    - /01/001.mp3 (misalnya: suara absen masuk)
//    - /01/002.mp3 (misalnya: suara absen pulang)
//    - /01/003.mp3 (misalnya: suara cooldown)
//
//  CARA MENGGUNAKAN SERIAL MONITOR:
//  - Buka Serial Monitor di Arduino IDE pada baud rate 115200.
//  - Pilih opsi "Both NL & CR" atau "Newline".
//  - Ketik perintah berikut lalu tekan Enter:
//    - '1' : Putar folder 01, file 001.mp3
//    - '2' : Putar folder 01, file 002.mp3
//    - '3' : Putar folder 01, file 003.mp3
//    - '+' : Naikkan volume
//    - '-' : Turunkan volume
//    - 's' : Hentikan suara (Stop)
// ============================================================

#include <Arduino.h>

// Definisi Pin Serial2 ESP32
#define RXD2 16  // Hubungkan ke TX Modul MP3
#define TXD2 17  // Hubungkan ke Resistor 1K -> RX Modul MP3

int currentVolume = 20; // Default Volume (Range 0 - 30)

// Fungsi untuk mengirim byte perintah mentah ke modul MP3
void sendMp3Command(uint8_t cmd, uint8_t para1, uint8_t para2) {
  // Protokol standar DFPlayer: 
  // 7E FF 06 [CMD] [Feedback] [Parameter 1] [Parameter 2] EF
  // Feedback = 0x00 (Tidak meminta respon balik agar tidak macet)
  uint8_t cmdBuffer[8] = { 0x7E, 0xFF, 0x06, cmd, 0x00, para1, para2, 0xEF };
  
  Serial.print("Kirim Perintah RAW: ");
  for (int i = 0; i < 8; i++) {
    if (cmdBuffer[i] < 0x10) Serial.print("0");
    Serial.print(cmdBuffer[i], HEX);
    Serial.print(" ");
  }
  Serial.println();

  // Kirim data ke UART2
  Serial2.write(cmdBuffer, 8);
  delay(150); // Jeda aman agar chip clone memproses data
}

// Fungsi memutar track tertentu di folder tertentu
void playTrack(uint8_t folder, uint8_t file) {
  Serial.print("Memutar Folder "); Serial.print(folder);
  Serial.print(", File "); Serial.println(file);
  // CMD 0x0F = Play folder & file
  sendMp3Command(0x0F, folder, file);
}

// Fungsi set volume
void setVolume(uint8_t vol) {
  if (vol > 30) vol = 30;
  currentVolume = vol;
  Serial.print("Set Volume ke: "); Serial.println(currentVolume);
  // CMD 0x06 = Set Volume
  sendMp3Command(0x06, 0x00, currentVolume);
}

// Fungsi stop playback
void stopPlayback() {
  Serial.println("Menghentikan Pemutaran...");
  // CMD 0x16 = Stop
  sendMp3Command(0x16, 0x00, 0x00);
}

void setup() {
  // Inisialisasi Serial untuk USB Debugging
  Serial.begin(115200);
  delay(1000);
  Serial.println("\n=== PENGUJIAN MANDIRI MP3-TF-16P (MH2024K-16SS) ===");
  Serial.println("Menggunakan Hardware Serial 2 ESP32 (RX=GPIO16, TX=GPIO17)");
  
  // Inisialisasi Serial2 untuk modul MP3 pada baud rate 9600
  Serial2.begin(9600, SERIAL_8N1, RXD2, TXD2);
  delay(500);

  // Set volume awal
  setVolume(currentVolume);

  Serial.println("\n--- Sistem Siap ---");
  Serial.println("Ketik di Serial Monitor:");
  Serial.println("  1, 2, atau 3 -> Untuk memutar file");
  Serial.println("  +            -> Menaikkan Volume");
  Serial.println("  -            -> Menurunkan Volume");
  Serial.println("  s            -> Berhenti (Stop)");
}

void loop() {
  // Baca masukan dari Serial Monitor komputer
  if (Serial.available()) {
    char input = Serial.read();
    
    // Abaikan spasi, newline, carriage return
    if (input == '\n' || input == '\r' || input == ' ') return;

    Serial.print("Menerima Input: "); Serial.println(input);

    if (input == '1') {
      playTrack(1, 1); // Memutar folder 01, file 001.mp3
    } 
    else if (input == '2') {
      playTrack(1, 2); // Memutar folder 01, file 002.mp3
    } 
    else if (input == '3') {
      playTrack(1, 3); // Memutar folder 01, file 003.mp3
    } 
    else if (input == '+') {
      if (currentVolume < 30) {
        setVolume(currentVolume + 2);
      } else {
        Serial.println("Volume sudah Maksimal (30)");
      }
    } 
    else if (input == '-') {
      if (currentVolume > 0) {
        setVolume(currentVolume - 2);
      } else {
        Serial.println("Volume sudah Mute (0)");
      }
    } 
    else if (input == 's' || input == 'S') {
      stopPlayback();
    }
  }
}
