import os
import shutil
import re

# Base paths
SCRATCH_DIR = "scratch"
OUT_DIR = "firmware"

# Hardware sources
NODE_V3 = os.path.join(SCRATCH_DIR, "firmware_nodemcu_v3", "firmware_nodemcu_v3.ino")
NODE_V4 = os.path.join(SCRATCH_DIR, "firmware_nodemcu_v4", "firmware_nodemcu_v4.ino")
ESP_16X2 = os.path.join(SCRATCH_DIR, "firmware_esp32_rfid_lcd16x2", "firmware_esp32_rfid_lcd16x2.ino")
ESP_FULL = os.path.join(SCRATCH_DIR, "firmware_esp32_full", "firmware_esp32_full.ino")

targets = [
    {"src": NODE_V3, "dest": "Station_NodeMCU_16x2/Station_NodeMCU_16x2.ino", "type": "16x2"},
    {"src": NODE_V4, "dest": "Station_NodeMCU_20x4/Station_NodeMCU_20x4.ino", "type": "20x4"},
    {"src": ESP_16X2, "dest": "Station_ESP32_16x2/Station_ESP32_16x2.ino", "type": "16x2"},
    {"src": ESP_FULL, "dest": "Station_ESP32_20x4/Station_ESP32_20x4.ino", "type": "20x4"},
    {"src": ESP_16X2, "dest": "Station_ESP32C3_16x2/Station_ESP32C3_16x2.ino", "type": "16x2_c3"}
]

def standardize_code(code, t_type):
    # 1. Standardize UI Strings for 16x2
    if t_type in ["16x2", "16x2_c3"]:
        code = re.sub(r'lcd\.print\(F\(" STATION ABSENSI"\)\);', 'lcd.print(F("   PEMBDA HUB   "));', code)
        code = re.sub(r'lcd\.print\(F\(" PEMBDAHUB v2\.0 "\)\);', 'lcd.print(F("  Silakan Scan  "));', code)
        
    # 2. Standardize UI Strings for 20x4
    if t_type == "20x4":
        code = re.sub(r'lcd\.print\(F\("   STATION ABSENI   "\)\);', 'lcd.print(F("   PEMBDA HUB v2    "));', code)
        code = re.sub(r'lcd\.print\(F\(" PEMBDAHUB NodeMCU  "\)\);', 'lcd.print(F("  Silakan Scan UID  "));', code)
        code = re.sub(r'lcd\.print\(F\(" PEMBDAHUB ESP32    "\)\);', 'lcd.print(F("  Silakan Scan UID  "));', code)

    # 3. Handle ESP32-C3 Specifics
    if t_type == "16x2_c3":
        # Modify Pinouts
        code = re.sub(r'#define RFID_SS_PIN\s+5', '#define RFID_SS_PIN    7', code)
        code = re.sub(r'#define RFID_RST_PIN\s+4', '#define RFID_RST_PIN   10', code)
        code = re.sub(r'#define BUZZER_PIN\s+2', '#define BUZZER_PIN     0', code)
        code = re.sub(r'#define LED_GREEN\s+15', '#define LED_GREEN      1', code)
        code = re.sub(r'#define LED_RED\s+13', '#define LED_RED        -1 // Tidak ada', code)
        
        # ESP32-C3 SPI requires specific pins for VSPI alternative
        code = re.sub(r'SPI\.begin\(\);', 'SPI.begin(4, 5, 6, 7); // SCK, MISO, MOSI, SS', code)
        
        # Modify I2C
        code = re.sub(r'Wire\.begin\(\);', 'Wire.begin(8, 9); // SDA=8, SCL=9', code)
        
        # Modify MP3 Serial to Serial1
        code = re.sub(r'HardwareSerial mp3Serial\(2\);', 'HardwareSerial mp3Serial(1);', code)
        code = re.sub(r'mp3Serial\.begin\(9600, SERIAL_8N1, 16, 17\);', 'mp3Serial.begin(9600, SERIAL_8N1, 3, 2); // RX=3, TX=2', code)
        
        # Replace comments
        code = code.replace("ESP32 DevKit", "ESP32-C3")
        
    # Replace Device ID to something generic
    code = re.sub(r'const char\* DEVICE_ID\s*=\s*"[^"]+";', 'const char* DEVICE_ID         = "KIOSK-NEW-01"; // GANTI UNTUK SETIAP STATION!', code)
        
    return code

for t in targets:
    src_path = t["src"]
    dest_path = os.path.join(OUT_DIR, t["dest"])
    
    with open(src_path, "r", encoding="utf-8") as f:
        code = f.read()
        
    code = standardize_code(code, t["type"])
    
    with open(dest_path, "w", encoding="utf-8") as f:
        f.write(code)
        
print("Firmware generated successfully!")
