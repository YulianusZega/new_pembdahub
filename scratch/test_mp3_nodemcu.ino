// ============================================================
//  FIRMWARE TEST STANDALONE - MP3-TF-16P (MH2024K-16SS) & NODEMCU (ESP8266)
// ============================================================
//
//  Program ini dirancang khusus untuk menguji modul pemutar MP3
//  tiruan (clone) secara interaktif melalui Serial Monitor menggunakan NodeMCU.
//  Menggunakan SoftwareSerial untuk komunikasi ke modul MP3, dan 
//  metode pengiriman perintah serial mentah (Raw Serial) tanpa library.
//
//  WIRING DIAGRAM NODEMCU:
//  ┌──────────────────┬──────────────┬───────────────────────────────┐
//  │ MP3-TF-16P Pin   │ Pin NodeMCU  │ Catatan                       │
//  ├──────────────────┼──────────────┼───────────────────────────────┤
//  │ 1. VCC           │ 5V (VIN / 5V)│ Tegangan 5V dari NodeMCU      │
//  │ 2. RX            │ D2 (GPIO 4)  │ Pasang RESISTOR 1K secara seri│
//  │ 3. TX            │ D1 (GPIO 5)  │ Hubungkan langsung            │
//  │ 6. SPK1          │ Speaker (+)  │ Hubungkan ke Speaker 8 ohm    │
//  │ 7. GND           │ GND          │ Common Ground                 │
//  │ 8. SPK2          │ Speaker (-)  │ Hubungkan ke Speaker 8 ohm    │
//  └──────────────────┴──────────────┴───────────────────────────────┘
//
//  STRUKTUR FILE MICROSD CARD (Wajib FAT32):
//  - Buat folder bernama "01" (tanpa kutip) di root MicroSD.
//  - Masukkan file audio MP3 Anda dan namakan sebagai:
//    - /01/001.mp3
//    - /01/002.mp3
//    - /01/003.mp3
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
#include <SoftwareSerial.h>

// Definisi Pin SoftwareSerial pada NodeMCU
// RX Pin = D1 (GPIO 5), dihubungkan ke TX Modul MP3
// TX Pin = D2 (GPIO 4), dihubungkan ke Resistor 1K -> RX Modul MP3
#define MP3_RX_PIN D1
#define MP3_TX_PIN D2

SoftwareSerial mp3Serial(MP3_RX_PIN, MP3_TX_PIN);

int currentVolume = 20; // Default Volume (Range 0 - 30)

// Fungsi untuk mengirim byte perintah mentah ke modul MP3
void sendMp3Command(uint8_t cmd, uint8_t para1, uint8_t para2) {
  // Protokol standar DFPlayer: 
  // 7E FF 06 [CMD] [Feedback] [Parameter 1] [Parameter 2] EF
  // Feedback = 0x00
  uint8_t cmdBuffer[8] = { 0x7E, 0xFF, 0x06, cmd, 0x00, para1, para2, 0xEF };
  
  Serial.print("Kirim Perintah RAW: ");
  for (int i = 0; i < 8; i++) {
    if (cmdBuffer[i] < 0x10) Serial.print("0");
    Serial.print(cmdBuffer[i], HEX);
    Serial.print(" ");
  }
  Serial.println();

  // Kirim data ke modul MP3
  mp3Serial.write(cmdBuffer, 8);
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
  // Inisialisasi Serial Komputer (USB Debugging)
  Serial.begin(115200);
  delay(1000);
  Serial.println("\n=== PENGUJIAN MANDIRI MP3-TF-16P (MH2024K-16SS) DENGAN NODEMCU ===");
  Serial.println("Menggunakan SoftwareSerial (RX=Pin D1, TX=Pin D2)");
  
  // Inisialisasi SoftwareSerial ke modul MP3 pada baud rate 9600
  mp3Serial.begin(9600);
  delay(500);

  // Set volume awal
  setVolume(currentVolume);

  Serial.println("\n--- Sistem Siap ---");
  Serial.println("Ketik di Serial Monitor:");
  Serial.println("  1, 2, atau 3 -> Untuk memutar file");
  Serial.println("  +            ->/ Meniakkan Volume");
  Serial.println("  -            ->/ Menurunkan Volume");
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
