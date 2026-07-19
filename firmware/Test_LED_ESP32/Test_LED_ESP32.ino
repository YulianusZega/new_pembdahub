// Definisi PIN sesuai yang baru
#define LED_GREEN 13   // Coba nyalakan Hijau di pin 13
#define LED_RED   15   // Coba nyalakan Merah di pin 15

void setup() {
  Serial.begin(115200);
  Serial.println("\n========================================");
  Serial.println("       TEST MANDIRI LED (ESP32)         ");
  Serial.println("========================================");
  
  pinMode(LED_GREEN, OUTPUT);
  pinMode(LED_RED, OUTPUT);

  // Pastikan semua mati saat baru menyala
  digitalWrite(LED_GREEN, LOW);
  digitalWrite(LED_RED, LOW);
}

void loop() {
  Serial.println(">>> Menyalakan LED HIJAU (Pin 13) selama 3 detik...");
  digitalWrite(LED_GREEN, HIGH);
  digitalWrite(LED_RED, LOW);
  delay(3000);
  
  Serial.println(">>> Menyalakan LED MERAH (Pin 15) selama 3 detik...");
  digitalWrite(LED_GREEN, LOW);
  digitalWrite(LED_RED, HIGH);
  delay(3000);
  
  Serial.println(">>> Mematikan SEMUA LED selama 2 detik...");
  digitalWrite(LED_GREEN, LOW);
  digitalWrite(LED_RED, LOW);
  delay(2000);
}
