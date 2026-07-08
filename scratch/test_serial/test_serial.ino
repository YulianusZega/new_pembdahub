// ============================================================
//  DIAGNOSTIC SKETCH FOR GM50/GM65 SCANNER SERIAL COMMUNICATION
// ============================================================
// Upload skrip ini ke ESP32 untuk mendeteksi apakah ada data 
// yang masuk dari scanner GM50/GM65 ke pin GPIO 16 (RX2).

#define GM65_RX_PIN    16  // Terhubung ke TX GM65
#define GM65_TX_PIN    17  // Terhubung ke RX GM65

unsigned long lastHeartbeat = 0;

void setup() {
  Serial.begin(115200);
  delay(1000);
  Serial.println("\n==========================================");
  Serial.println("   DIAGNOSIS KOMUNIKASI SERIAL GM50/GM65  ");
  Serial.println("==========================================");
  Serial.println("ESP32 siap menerima data...");
  Serial.printf("Pin RX2 (ESP32) -> GPIO %d\n", GM65_RX_PIN);
  Serial.printf("Pin TX2 (ESP32) -> GPIO %d\n", GM65_TX_PIN);
  Serial.println("Menunggu scan QR Code...");
  Serial.println("------------------------------------------");

  // Kita mulai dengan baudrate default 9600
  Serial2.begin(9600, SERIAL_8N1, GM65_RX_PIN, GM65_TX_PIN);
}

void loop() {
  // 1. Tampilkan detak jantung (Heartbeat) setiap 3 detik 
  //    untuk memastikan ESP32 tidak hang/freeze.
  if (millis() - lastHeartbeat > 3000) {
    Serial.println("[System Running] Menunggu input serial dari GM50/GM65...");
    lastHeartbeat = millis();
  }

  // 2. Baca data dari GM50/GM65 dan langsung teruskan ke Serial Monitor
  if (Serial2.available() > 0) {
    Serial.println("\n>>> ADA DATA MASUK DARI SERIAL2! <<<");
    Serial.print("Data (Teks): ");
    
    while (Serial2.available() > 0) {
      char c = Serial2.read();
      
      // Cetak karakter biasa
      if (c >= 32 && c <= 126) {
        Serial.print(c);
      } else {
        // Cetak representasi HEX untuk karakter kontrol seperti \r atau \n
        Serial.printf("[0x%02X]", c);
      }
    }
    Serial.println("\n------------------------------------------");
  }
}
