// ============================================================
//  I2C SCANNER - Untuk cari alamat LCD di NodeMCU V3
//  Upload ini dulu, buka Serial Monitor (115200 baud)
//  Catat alamat yang ditemukan (biasanya 0x27 atau 0x3F)
// ============================================================

#include <Wire.h>

void setup() {
  Serial.begin(115200);
  delay(1000);
  Serial.println("\n=== I2C Scanner NodeMCU V3 ===");
  
  // SDA = D2 (GPIO4), SCL = D1 (GPIO5)
  Wire.begin(4, 5);
  
  Serial.println("Scanning...\n");

  int found = 0;
  for (byte addr = 1; addr < 127; addr++) {
    Wire.beginTransmission(addr);
    byte error = Wire.endTransmission();
    
    if (error == 0) {
      Serial.print(">> Device ditemukan di alamat: 0x");
      if (addr < 16) Serial.print("0");
      Serial.print(addr, HEX);
      
      if (addr == 0x27 || addr == 0x3F) {
        Serial.print("  ← Ini LCD!");
      }
      Serial.println();
      found++;
    }
  }

  Serial.println();
  if (found == 0) {
    Serial.println("!!! TIDAK ADA device I2C ditemukan !!!");
    Serial.println("Cek wiring:");
    Serial.println("  LCD SDA → D2 (GPIO4)");
    Serial.println("  LCD SCL → D1 (GPIO5)");
    Serial.println("  LCD VCC → VIN (5V)");
    Serial.println("  LCD GND → GND");
  } else {
    Serial.print("Total: ");
    Serial.print(found);
    Serial.println(" device ditemukan.");
    Serial.println("\nGunakan alamat di atas untuk LCD_ADDRESS di firmware.");
  }
}

void loop() {
  delay(5000);
}
