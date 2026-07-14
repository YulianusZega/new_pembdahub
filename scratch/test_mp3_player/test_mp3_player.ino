// ============================================================
//  TEST MP3 PLAYER - DFPlayer Mini via ESP32 Serial2
//  
//  Sketch khusus untuk menguji DFPlayer Mini dan file MP3
//  di microSD card TANPA komponen lain (tanpa RFID, LCD, WiFi).
//
//  WIRING (hanya 4 kabel + speaker):
//  ┌──────────────────────────────────────────────────────┐
//  │  ESP32 GPIO17 (TX2) ──[1KΩ]──→ DFPlayer RX          │
//  │  ESP32 GPIO16 (RX2) ←──────── DFPlayer TX            │
//  │  5V (dari ESP32 / Eksternal) ─→ DFPlayer VCC         │
//  │  GND ─────────────────────────→ DFPlayer GND         │
//  │  DFPlayer SPK_1 ──────────────→ Speaker (+)          │
//  │  DFPlayer SPK_2 ──────────────→ Speaker (-)          │
//  └──────────────────────────────────────────────────────┘
//  ⚠️ PENTING: Resistor 1KΩ antara GPIO17 (TX2) dan DFPlayer RX!
//  ⚠️ DFPlayer Mini butuh 5V! (3.3V tidak cukup)
//
//  FILE MP3 DI SD CARD (wajib ada):
//  SD Card/
//  └── 01/              ← Folder bernama "01"
//      ├── 001.mp3      → "Selamat Pagi Silahkan Absen"        (Boot)
//      ├── 002.mp3      → "Akses Diterima Selamat Belajar"     (CHECK_IN Siswa)
//      ├── 003.mp3      → "Absen Pulang Sampai Jumpa"          (CHECK_OUT)
//      ├── 004.mp3      → "Absen Sudah Tercatat Terima Kasih"  (COOLDOWN)
//      ├── 005.mp3      → "Selamat Pagi Selamat Bekerja"       (CHECK_IN Guru)
//      ├── 006.mp3      → "Kartu Tidak Dikenali Hubungi Admin" (NEW_CARD)
//      └── 007.mp3      → "Sistem Ada Gangguan Hubungi Admin"  (ERROR)
//
//  CARA PAKAI:
//  1. Upload sketch ini ke ESP32
//  2. Buka Serial Monitor (115200 baud)
//  3. Ikuti menu yang muncul, ketik angka 1-7 untuk putar track
//     atau ketik perintah lain (lihat menu)
//
//  BOARD SETTING DI ARDUINO IDE:
//  - Board         : "ESP32 Dev Module"
//  - Upload Speed  : 115200
//  - Serial Monitor: 115200 baud, NL & CR
// ============================================================

#include <HardwareSerial.h>

// ============================================================
//  PIN DEFINITIONS
// ============================================================
#define MP3_TX_PIN     17   // GPIO 17 → TX ke RX DFPlayer (via R 1KΩ)
#define MP3_RX_PIN     16   // GPIO 16 ← RX dari TX DFPlayer
#define MP3_VOLUME     22   // Volume default (0-30)

// ============================================================
//  GLOBAL
// ============================================================
HardwareSerial mp3Serial(2);  // UART2 ESP32

uint8_t  currentVolume = MP3_VOLUME;
uint8_t  currentFolder = 1;
bool     autoPlayMode  = false;
uint8_t  autoPlayTrack = 1;
unsigned long autoPlayTimer = 0;
unsigned long lastResponseCheck = 0;

// Deskripsi track untuk tampilan
const char* trackDesc[] = {
  "",  // index 0 tidak dipakai
  "Selamat Pagi Silahkan Absen (Boot)",
  "Akses Diterima Selamat Belajar (CHECK_IN Siswa)",
  "Absen Pulang Sampai Jumpa (CHECK_OUT)",
  "Absen Sudah Tercatat Terima Kasih (COOLDOWN)",
  "Selamat Pagi Selamat Bekerja (CHECK_IN Guru)",
  "Kartu Tidak Dikenali Hubungi Admin (NEW_CARD)",
  "Sistem Ada Gangguan Hubungi Admin (ERROR)"
};
const int TOTAL_TRACKS = 7;

// ============================================================
//  SETUP
// ============================================================
void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println();
  Serial.println(F("╔══════════════════════════════════════════════════╗"));
  Serial.println(F("║    TEST MP3 PLAYER - DFPlayer Mini + ESP32      ║"));
  Serial.println(F("║    PembdaHUB Attendance Station Sound Test      ║"));
  Serial.println(F("╚══════════════════════════════════════════════════╝"));
  Serial.println();

  // Inisialisasi Serial2 untuk DFPlayer Mini
  Serial.println(F("[INIT] Menginisialisasi DFPlayer Mini..."));
  Serial.println(F("       TX2 = GPIO17 → DFPlayer RX (via R 1KΩ)"));
  Serial.println(F("       RX2 = GPIO16 ← DFPlayer TX"));

  mp3Serial.begin(9600, SERIAL_8N1, MP3_RX_PIN, MP3_TX_PIN);
  delay(1000);  // DFPlayer butuh waktu boot (~500ms, kita kasih lebih)

  // Buang data noise awal
  int noiseCount = 0;
  while (mp3Serial.available()) {
    mp3Serial.read();
    noiseCount++;
  }
  if (noiseCount > 0) {
    Serial.print(F("[INIT] Buang noise awal: "));
    Serial.print(noiseCount);
    Serial.println(F(" byte"));
  }

  Serial.println(F("[INIT] DFPlayer Mini siap."));
  Serial.println();

  // Reset DFPlayer
  Serial.println(F("[CMD] Reset DFPlayer..."));
  sendMp3Command(0x0C, 0x00, 0x00);  // Reset module
  delay(2000);  // Tunggu reset selesai (butuh ~1.5s)

  // Buang response reset
  while (mp3Serial.available()) mp3Serial.read();

  // Set volume
  Serial.print(F("[CMD] Set volume: "));
  Serial.print(currentVolume);
  Serial.println(F("/30"));
  setMp3Volume(currentVolume);
  delay(200);

  // Set EQ ke Normal
  Serial.println(F("[CMD] Set EQ: Normal"));
  sendMp3Command(0x07, 0x00, 0x00);  // EQ Normal
  delay(200);

  // Query status
  Serial.println(F("[CMD] Query jumlah file di SD Card..."));
  queryFileCount();
  delay(500);

  // Cek response dari DFPlayer
  checkDfPlayerResponse();

  Serial.println();
  Serial.println(F("═══════════════════════════════════════════════════"));
  Serial.println(F("  DFPlayer Mini SIAP! Putar test track pertama..."));
  Serial.println(F("═══════════════════════════════════════════════════"));
  Serial.println();

  // Auto-putar track 1 untuk tes langsung
  Serial.println(F("[PLAY] ▶ Track 001.mp3 - \"Selamat Pagi Silahkan Absen\""));
  playAudio(1, 1);
  delay(500);

  // Tampilkan menu
  showMenu();
}

// ============================================================
//  LOOP UTAMA
// ============================================================
void loop() {
  // Cek input dari Serial Monitor
  if (Serial.available()) {
    String input = Serial.readStringUntil('\n');
    input.trim();
    
    if (input.length() > 0) {
      handleCommand(input);
    }
  }

  // Cek response dari DFPlayer (setiap 500ms)
  if (millis() - lastResponseCheck >= 500) {
    lastResponseCheck = millis();
    checkDfPlayerResponse();
  }

  // Auto-play mode: putar track berikutnya setiap 5 detik
  if (autoPlayMode && millis() - autoPlayTimer >= 5000) {
    autoPlayTimer = millis();
    Serial.println();
    Serial.print(F("[AUTO] ▶ Track "));
    Serial.print(autoPlayTrack);
    Serial.print(F(" - "));
    if (autoPlayTrack <= TOTAL_TRACKS) {
      Serial.println(trackDesc[autoPlayTrack]);
    } else {
      Serial.println(F("(Track tambahan)"));
    }
    
    playAudio(currentFolder, autoPlayTrack);
    
    autoPlayTrack++;
    if (autoPlayTrack > TOTAL_TRACKS) {
      autoPlayTrack = 1;
      autoPlayMode = false;
      Serial.println();
      Serial.println(F("[AUTO] ✔ Semua track sudah diputar. Auto-play selesai."));
      Serial.println(F("       Ketik 'a' untuk mulai lagi, atau pilih track manual."));
      showMenu();
    }
  }

  delay(10);
}

// ============================================================
//  HANDLE COMMAND DARI SERIAL MONITOR
// ============================================================
void handleCommand(String cmd) {
  Serial.println();
  Serial.print(F(">> Perintah: "));
  Serial.println(cmd);

  // ── Angka 1-7: Putar track langsung ──
  if (cmd.length() == 1 && cmd[0] >= '1' && cmd[0] <= '7') {
    uint8_t track = cmd[0] - '0';
    Serial.print(F("[PLAY] ▶ Track 00"));
    Serial.print(track);
    Serial.print(F(".mp3 - "));
    Serial.println(trackDesc[track]);
    playAudio(currentFolder, track);
  }

  // ── 'a' atau 'A': Auto-play semua track berurutan ──
  else if (cmd == "a" || cmd == "A") {
    Serial.println(F("[AUTO] ▶ Memulai auto-play semua track (jeda 5 detik)..."));
    autoPlayMode  = true;
    autoPlayTrack = 1;
    autoPlayTimer = millis() - 5000;  // Langsung mulai
  }

  // ── 's' atau 'S': Stop / Pause ──
  else if (cmd == "s" || cmd == "S") {
    Serial.println(F("[CMD] ⏹ Stop playback"));
    autoPlayMode = false;
    sendMp3Command(0x16, 0x00, 0x00);  // Stop
  }

  // ── 'p' atau 'P': Pause / Resume ──
  else if (cmd == "p" || cmd == "P") {
    Serial.println(F("[CMD] ⏸ Pause/Resume"));
    sendMp3Command(0x0E, 0x00, 0x00);  // Pause
  }

  // ── '+': Volume naik ──
  else if (cmd == "+") {
    if (currentVolume < 30) {
      currentVolume++;
      setMp3Volume(currentVolume);
      Serial.print(F("[VOL] 🔊 Volume naik: "));
      Serial.print(currentVolume);
      Serial.println(F("/30"));
      printVolumeBar();
    } else {
      Serial.println(F("[VOL] ⚠ Volume sudah MAX (30/30)"));
    }
  }

  // ── '-': Volume turun ──
  else if (cmd == "-") {
    if (currentVolume > 0) {
      currentVolume--;
      setMp3Volume(currentVolume);
      Serial.print(F("[VOL] 🔉 Volume turun: "));
      Serial.print(currentVolume);
      Serial.println(F("/30"));
      printVolumeBar();
    } else {
      Serial.println(F("[VOL] ⚠ Volume sudah MIN (0/30)"));
    }
  }

  // ── 'v' + angka: Set volume langsung (contoh: v15) ──
  else if (cmd.startsWith("v") || cmd.startsWith("V")) {
    int vol = cmd.substring(1).toInt();
    if (vol >= 0 && vol <= 30) {
      currentVolume = vol;
      setMp3Volume(currentVolume);
      Serial.print(F("[VOL] Volume diset ke: "));
      Serial.print(currentVolume);
      Serial.println(F("/30"));
      printVolumeBar();
    } else {
      Serial.println(F("[VOL] ⚠ Volume harus 0-30! Contoh: v15"));
    }
  }

  // ── 'r' atau 'R': Reset DFPlayer ──
  else if (cmd == "r" || cmd == "R") {
    Serial.println(F("[CMD] 🔄 Reset DFPlayer Mini..."));
    sendMp3Command(0x0C, 0x00, 0x00);
    delay(2000);
    while (mp3Serial.available()) mp3Serial.read();
    setMp3Volume(currentVolume);
    delay(200);
    Serial.println(F("[CMD] ✔ Reset selesai. Volume dikembalikan."));
  }

  // ── 'q' atau 'Q': Query status ──
  else if (cmd == "q" || cmd == "Q") {
    Serial.println(F("[CMD] 📊 Query status DFPlayer..."));
    Serial.println(F("       Menghitung file di SD Card..."));
    queryFileCount();
    delay(500);
    checkDfPlayerResponse();
    Serial.println(F("       Query status playback..."));
    sendMp3Command(0x42, 0x00, 0x00);  // Query status
    delay(500);
    checkDfPlayerResponse();
  }

  // ── 'n' atau 'N': Next track ──
  else if (cmd == "n" || cmd == "N") {
    Serial.println(F("[CMD] ⏭ Next track"));
    sendMp3Command(0x01, 0x00, 0x00);
  }

  // ── 'b' atau 'B': Previous track ──
  else if (cmd == "b" || cmd == "B") {
    Serial.println(F("[CMD] ⏮ Previous track"));
    sendMp3Command(0x02, 0x00, 0x00);
  }

  // ── 'e' + angka: Set EQ (0-5) ──
  else if (cmd.startsWith("e") || cmd.startsWith("E")) {
    int eq = cmd.substring(1).toInt();
    if (eq >= 0 && eq <= 5) {
      const char* eqNames[] = {"Normal", "Pop", "Rock", "Jazz", "Classic", "Bass"};
      sendMp3Command(0x07, 0x00, (uint8_t)eq);
      Serial.print(F("[EQ] Equalizer diset ke: "));
      Serial.println(eqNames[eq]);
    } else {
      Serial.println(F("[EQ] ⚠ EQ harus 0-5! (0=Normal, 1=Pop, 2=Rock, 3=Jazz, 4=Classic, 5=Bass)"));
    }
  }

  // ── 'l' atau 'L': Loop track saat ini ──
  else if (cmd == "l" || cmd == "L") {
    Serial.println(F("[CMD] 🔁 Loop: putar ulang track terakhir"));
    sendMp3Command(0x08, 0x00, 0x02);  // Repeat single
  }

  // ── 't' atau 'T': Test semua track cepat (2 detik per track) ──
  else if (cmd == "t" || cmd == "T") {
    Serial.println(F("[TEST] 🧪 Test cepat semua track (2 detik per track)..."));
    Serial.println(F("──────────────────────────────────────────────────"));
    for (int i = 1; i <= TOTAL_TRACKS; i++) {
      Serial.print(F("  ▶ Track "));
      Serial.print(i);
      Serial.print(F("/"));
      Serial.print(TOTAL_TRACKS);
      Serial.print(F(" - "));
      Serial.println(trackDesc[i]);
      playAudio(currentFolder, i);
      delay(2500);  // Putar 2.5 detik lalu pindah
    }
    Serial.println(F("──────────────────────────────────────────────────"));
    Serial.println(F("[TEST] ✔ Test cepat selesai!"));
    showMenu();
  }

  // ── 'f' + angka: Ganti folder (contoh: f2 untuk folder 02) ──
  else if (cmd.startsWith("f") || cmd.startsWith("F")) {
    int folder = cmd.substring(1).toInt();
    if (folder >= 1 && folder <= 99) {
      currentFolder = folder;
      Serial.print(F("[FOLDER] 📁 Folder aktif diubah ke: "));
      if (folder < 10) Serial.print("0");
      Serial.println(folder);
    } else {
      Serial.println(F("[FOLDER] ⚠ Folder harus 1-99! Contoh: f2"));
    }
  }

  // ── 'd' atau 'D': Diagnostik wiring ──
  else if (cmd == "d" || cmd == "D") {
    runDiagnostic();
  }

  // ── 'm' atau 'M' atau 'h' atau 'H' atau '?': Tampilkan menu ──
  else if (cmd == "m" || cmd == "M" || cmd == "h" || cmd == "H" || cmd == "?") {
    showMenu();
  }

  else {
    Serial.println(F("[?] Perintah tidak dikenal. Ketik 'm' untuk lihat menu."));
  }

  Serial.println();
}

// ============================================================
//  MENU TAMPILAN
// ============================================================
void showMenu() {
  Serial.println();
  Serial.println(F("╔══════════════════════════════════════════════════╗"));
  Serial.println(F("║              MENU TEST MP3 PLAYER               ║"));
  Serial.println(F("╠══════════════════════════════════════════════════╣"));
  Serial.println(F("║                                                  ║"));
  Serial.println(F("║  PUTAR TRACK (ketik angka):                      ║"));
  Serial.println(F("║  1 = 001.mp3 \"Selamat Pagi Silahkan Absen\"       ║"));
  Serial.println(F("║  2 = 002.mp3 \"Akses Diterima Selamat Belajar\"    ║"));
  Serial.println(F("║  3 = 003.mp3 \"Absen Pulang Sampai Jumpa\"         ║"));
  Serial.println(F("║  4 = 004.mp3 \"Absen Sudah Tercatat\"              ║"));
  Serial.println(F("║  5 = 005.mp3 \"Selamat Pagi Selamat Bekerja\"      ║"));
  Serial.println(F("║  6 = 006.mp3 \"Kartu Tidak Dikenali\"              ║"));
  Serial.println(F("║  7 = 007.mp3 \"Sistem Ada Gangguan\"               ║"));
  Serial.println(F("║                                                  ║"));
  Serial.println(F("║  KONTROL:                                        ║"));
  Serial.println(F("║  a = Auto-play semua track (jeda 5 dtk)          ║"));
  Serial.println(F("║  t = Test cepat semua track (2 dtk/track)        ║"));
  Serial.println(F("║  s = Stop playback                               ║"));
  Serial.println(F("║  p = Pause / Resume                              ║"));
  Serial.println(F("║  n = Next track                                  ║"));
  Serial.println(F("║  b = Back / Previous track                       ║"));
  Serial.println(F("║  l = Loop track saat ini                         ║"));
  Serial.println(F("║                                                  ║"));
  Serial.println(F("║  VOLUME:                                         ║"));
  Serial.println(F("║  + = Volume naik (+1)                            ║"));
  Serial.println(F("║  - = Volume turun (-1)                           ║"));
  Serial.println(F("║  v15 = Set volume ke 15 (range 0-30)             ║"));
  Serial.println(F("║                                                  ║"));
  Serial.println(F("║  LAINNYA:                                        ║"));
  Serial.println(F("║  e0-e5 = EQ (Normal/Pop/Rock/Jazz/Classic/Bass)  ║"));
  Serial.println(F("║  f1-f99 = Ganti folder aktif (default: 01)       ║"));
  Serial.println(F("║  q = Query status DFPlayer                       ║"));
  Serial.println(F("║  d = Diagnostik wiring & SD Card                 ║"));
  Serial.println(F("║  r = Reset DFPlayer                              ║"));
  Serial.println(F("║  m = Tampilkan menu ini                          ║"));
  Serial.println(F("║                                                  ║"));
  Serial.print(  F("║  Volume saat ini: "));
  Serial.print(currentVolume);
  Serial.print(F("/30  |  Folder: "));
  if (currentFolder < 10) Serial.print("0");
  Serial.print(currentFolder);
  Serial.println(F("              ║"));
  Serial.println(F("║                                                  ║"));
  Serial.println(F("╚══════════════════════════════════════════════════╝"));
  Serial.println();
  Serial.print(F(">> Ketik perintah: "));
}

// ============================================================
//  VOLUME BAR VISUAL
// ============================================================
void printVolumeBar() {
  Serial.print(F("       ["));
  int bars = currentVolume;
  for (int i = 0; i < 30; i++) {
    if (i < bars) Serial.print("█");
    else          Serial.print("░");
  }
  Serial.print(F("] "));
  Serial.print(currentVolume);
  Serial.println(F("/30"));
}

// ============================================================
//  DIAGNOSTIK WIRING & SD CARD
// ============================================================
void runDiagnostic() {
  Serial.println();
  Serial.println(F("╔══════════════════════════════════════════════════╗"));
  Serial.println(F("║           DIAGNOSTIK DFPlayer Mini               ║"));
  Serial.println(F("╚══════════════════════════════════════════════════╝"));
  Serial.println();

  // 1. Cek komunikasi serial
  Serial.println(F("[DIAG] 1/4 Cek komunikasi serial..."));
  Serial.print(F("       TX2 Pin: GPIO")); Serial.println(MP3_TX_PIN);
  Serial.print(F("       RX2 Pin: GPIO")); Serial.println(MP3_RX_PIN);
  Serial.print(F("       Baud: 9600"));
  Serial.println();

  // 2. Reset dan tunggu response
  Serial.println(F("[DIAG] 2/4 Reset DFPlayer dan cek response..."));
  // Buang buffer lama
  while (mp3Serial.available()) mp3Serial.read();
  
  sendMp3Command(0x0C, 0x00, 0x00);  // Reset
  delay(2000);

  bool gotResponse = false;
  int bytesReceived = 0;
  Serial.print(F("       Response bytes: "));
  
  unsigned long timeout = millis() + 1000;
  while (millis() < timeout) {
    if (mp3Serial.available()) {
      uint8_t b = mp3Serial.read();
      Serial.print(F("0x"));
      if (b < 0x10) Serial.print("0");
      Serial.print(b, HEX);
      Serial.print(" ");
      bytesReceived++;
      gotResponse = true;
    }
  }
  Serial.println();

  if (gotResponse) {
    Serial.print(F("       ✔ DFPlayer MERESPON! ("));
    Serial.print(bytesReceived);
    Serial.println(F(" byte diterima)"));
    Serial.println(F("       Komunikasi serial OK."));
  } else {
    Serial.println(F("       ✘ TIDAK ADA RESPON dari DFPlayer!"));
    Serial.println(F("       Kemungkinan masalah:"));
    Serial.println(F("       - Wiring TX/RX terbalik"));
    Serial.println(F("       - Resistor 1KΩ belum dipasang"));
    Serial.println(F("       - DFPlayer tidak dapat daya 5V"));
    Serial.println(F("       - DFPlayer rusak / mati"));
    Serial.println(F("       - SD Card tidak terpasang"));
  }

  // 3. Query jumlah file
  Serial.println();
  Serial.println(F("[DIAG] 3/4 Query jumlah file di SD Card..."));
  queryFileCount();
  delay(500);
  checkDfPlayerResponse();

  // 4. Test putar track 1
  Serial.println();
  Serial.println(F("[DIAG] 4/4 Test putar track 001.mp3..."));
  setMp3Volume(currentVolume);
  delay(200);
  playAudio(1, 1);
  delay(500);
  
  bool playResponse = false;
  timeout = millis() + 1000;
  while (millis() < timeout) {
    if (mp3Serial.available()) {
      uint8_t b = mp3Serial.read();
      // Response 0x7E berarti DFPlayer memproses perintah
      if (b == 0x7E) playResponse = true;
    }
  }

  Serial.println();
  Serial.println(F("═══════════════════════════════════════════════════"));
  Serial.println(F("  HASIL DIAGNOSTIK:"));
  Serial.println(F("═══════════════════════════════════════════════════"));
  Serial.print(  F("  Komunikasi Serial : "));
  Serial.println(gotResponse ? "✔ OK" : "✘ GAGAL");
  Serial.print(  F("  Volume            : "));
  Serial.print(currentVolume);
  Serial.println(F("/30"));
  Serial.print(  F("  Folder aktif      : "));
  if (currentFolder < 10) Serial.print("0");
  Serial.println(currentFolder);
  Serial.println();
  
  if (!gotResponse) {
    Serial.println(F("  ⚠ SARAN TROUBLESHOOTING:"));
    Serial.println(F("  ─────────────────────────────────────────────"));
    Serial.println(F("  1. Cek apakah kabel VCC terhubung ke 5V"));
    Serial.println(F("  2. Cek apakah GND terhubung (common ground)"));
    Serial.println(F("  3. Cek apakah GPIO17 → DFPlayer RX (via 1KΩ)"));
    Serial.println(F("  4. Cek apakah GPIO16 ← DFPlayer TX"));
    Serial.println(F("  5. Cek apakah SD Card terpasang dengan benar"));
    Serial.println(F("  6. SD Card harus format FAT16 atau FAT32"));
    Serial.println(F("  7. File harus di folder '01' dgn nama '001.mp3'"));
  } else {
    Serial.println(F("  ✔ DFPlayer terdeteksi dan bisa berkomunikasi!"));
    Serial.println(F("  Jika tidak ada suara, periksa:"));
    Serial.println(F("  - Speaker terhubung ke SPK_1 dan SPK_2"));
    Serial.println(F("  - File MP3 ada di folder '01' pada SD Card"));
    Serial.println(F("  - SD Card format FAT32, kapasitas ≤ 32GB"));
  }
  Serial.println(F("═══════════════════════════════════════════════════"));
}

// ============================================================
//  FUNGSI MP3 PLAYER (DFPlayer Mini Protocol)
//
//  Protokol: [0x7E][0xFF][0x06][CMD][0x00][PAR1][PAR2][0xEF]
//  Total 8 byte per perintah (tanpa checksum untuk kompatibilitas clone)
// ============================================================

// Kirim perintah raw byte ke DFPlayer Mini via Serial2
void sendMp3Command(uint8_t cmd, uint8_t para1, uint8_t para2) {
  uint8_t cmdBuffer[8] = { 0x7E, 0xFF, 0x06, cmd, 0x00, para1, para2, 0xEF };

  // Debug: tampilkan byte yang dikirim
  Serial.print(F("       [TX] "));
  for (int i = 0; i < 8; i++) {
    Serial.print(F("0x"));
    if (cmdBuffer[i] < 0x10) Serial.print("0");
    Serial.print(cmdBuffer[i], HEX);
    if (i < 7) Serial.print(" ");
  }
  Serial.print(F("  (CMD=0x"));
  if (cmd < 0x10) Serial.print("0");
  Serial.print(cmd, HEX);
  Serial.println(F(")"));

  mp3Serial.write(cmdBuffer, 8);
  delay(100);  // Jeda aman untuk DFPlayer memproses
}

// Putar track di folder tertentu (folder & track 1-indexed)
// Contoh: playAudio(1, 2) = putar folder 01, file 002.mp3
void playAudio(uint8_t folder, uint8_t track) {
  sendMp3Command(0x0F, folder, track);
}

// Atur volume (0-30)
void setMp3Volume(uint8_t vol) {
  if (vol > 30) vol = 30;
  sendMp3Command(0x06, 0x00, vol);
}

// Query jumlah file di SD Card
void queryFileCount() {
  sendMp3Command(0x48, 0x00, 0x00);  // Query file count pada SD
}

// ============================================================
//  CEK RESPONSE DARI DFPlayer
// ============================================================
void checkDfPlayerResponse() {
  while (mp3Serial.available()) {
    uint8_t startByte = mp3Serial.read();

    if (startByte == 0x7E) {
      // Baca sisa frame (harap 9 byte total, sudah baca 1)
      uint8_t frame[10];
      frame[0] = startByte;
      int idx = 1;

      unsigned long timeout = millis() + 200;
      while (idx < 10 && millis() < timeout) {
        if (mp3Serial.available()) {
          frame[idx++] = mp3Serial.read();
          if (frame[idx - 1] == 0xEF) break;  // End byte
        }
      }

      // Parse response
      Serial.print(F("       [RX] "));
      for (int i = 0; i < idx; i++) {
        Serial.print(F("0x"));
        if (frame[i] < 0x10) Serial.print("0");
        Serial.print(frame[i], HEX);
        if (i < idx - 1) Serial.print(" ");
      }

      // Decode pesan yang dikenal
      if (idx >= 7) {
        uint8_t cmd = frame[3];
        uint16_t param = ((uint16_t)frame[5] << 8) | frame[6];

        switch (cmd) {
          case 0x3A:
            Serial.println(F("  → Media inserted (SD Card terdeteksi)"));
            break;
          case 0x3B:
            Serial.println(F("  → Media removed (SD Card dicabut!)"));
            break;
          case 0x3C:
            Serial.print(F("  → Track selesai diputar: "));
            Serial.println(param);
            break;
          case 0x3D:
            Serial.print(F("  → Track selesai (SD): "));
            Serial.println(param);
            break;
          case 0x3F:
            Serial.print(F("  → Init complete. Media online: "));
            if (param & 0x01) Serial.print("USB ");
            if (param & 0x02) Serial.print("SD ");
            Serial.println();
            break;
          case 0x40:
            Serial.print(F("  → Error! Code: "));
            switch (param) {
              case 1: Serial.println(F("Module busy")); break;
              case 2: Serial.println(F("Frame incomplete")); break;
              case 3: Serial.println(F("Checksum error")); break;
              case 5: Serial.println(F("Folder/track not found!")); break;
              case 6: Serial.println(F("No matching track in folder!")); break;
              case 7: Serial.println(F("SD Card error!")); break;
              default: Serial.println(param); break;
            }
            break;
          case 0x41:
            Serial.print(F("  → ACK (perintah diterima): 0x"));
            Serial.println(param, HEX);
            break;
          case 0x42:
            Serial.print(F("  → Status: "));
            switch (param) {
              case 0: Serial.println(F("Stopped")); break;
              case 1: Serial.println(F("Playing")); break;
              case 2: Serial.println(F("Paused")); break;
              default: Serial.println(param); break;
            }
            break;
          case 0x43:
            Serial.print(F("  → Volume saat ini: "));
            Serial.print(param);
            Serial.println(F("/30"));
            break;
          case 0x48:
            Serial.print(F("  → Jumlah file di SD Card: "));
            Serial.println(param);
            break;
          case 0x4E:
            Serial.print(F("  → Jumlah file di folder: "));
            Serial.println(param);
            break;
          default:
            Serial.print(F("  → Response CMD=0x"));
            if (cmd < 0x10) Serial.print("0");
            Serial.print(cmd, HEX);
            Serial.print(F(" PARAM="));
            Serial.println(param);
            break;
        }
      } else {
        Serial.println(F("  → (frame tidak lengkap)"));
      }
    }
  }
}
