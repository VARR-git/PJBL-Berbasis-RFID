#include <WiFi.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Adafruit_Fingerprint.h>

#define RST_PIN 4
#define SS_PIN 5
#define BUTTON_PIN 23 // Tombol untuk mode
#define RX_PIN 16   // RX untuk fingerprint
#define TX_PIN 17   // TX untuk fingerprint

HardwareSerial mySerial(2);
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial);
LiquidCrystal_I2C lcd(0x27, 16, 2);
MFRC522 mfrc522(SS_PIN, RST_PIN);
MFRC522::StatusCode status;
byte block = 4;
byte len = 18;
byte buffer1[18];
bool mode = false;
int lastState;

const char* ssid = "workshop electric";     // Ganti dengan nama WiFi Anda
const char* password = "tefalistrik";  // Ganti dengan password WiFi Anda

void setup() {
    Serial.begin(115200);
    mySerial.begin(57600, SERIAL_8N1, RX_PIN, TX_PIN);
    WiFi.begin(ssid, password);
    Wire.begin(21, 22);
    lcd.init();
    lcd.backlight();
    SPI.begin();
    mfrc522.PCD_Init();
    pinMode(BUTTON_PIN, INPUT_PULLUP);

    Serial.print("Menghubungkan ke WiFi");
    while (WiFi.status() != WL_CONNECTED) {
      delay(1000);
      Serial.print(".");
    }
    Serial.println("\nTerhubung ke WiFi!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());
    Serial.println(F("Sistem Siap! Tekan tombol untuk mendaftarkan sidik jari."));
    if (finger.verifyPassword()) {
        Serial.println("Sensor sidik jari ditemukan!");
    } else {
        Serial.println("Sensor sidik jari tidak ditemukan!");
        while (1) { delay(1); }
    }
}

void loop() {
    int state = digitalRead(BUTTON_PIN);
    if (state == LOW && lastState == HIGH) { 
        mode = !mode;
        lcd.clear();
    }
    lastState = state;
    lcd.setCursor(0, 0);
    if (!mode) {
        lcd.print("SCAN ID CARD");
        Serial.println("Mode: Scan ID Card + Verifikasi Sidik Jari");
        scanIDCard();
        delay(1000);
    } else {
        Serial.println("Mode: Pendaftaran Sidik Jari");
        enrollFingerprint();
    }
    delay(100);
}

void scanIDCard() {
  MFRC522::MIFARE_Key key;
  for (byte i = 0; i < 6; i++) key.keyByte[i] = 0xFF;

  if (!mfrc522.PICC_IsNewCardPresent()) return;
  if (!mfrc522.PICC_ReadCardSerial()) return;

  Serial.println(F("**Kartu Terdeteksi:**"));
  mfrc522.PICC_DumpDetailsToSerial(&(mfrc522.uid));

  status = mfrc522.PCD_Authenticate(MFRC522::PICC_CMD_MF_AUTH_KEY_A, 4, &key, &(mfrc522.uid));
  if (status != MFRC522::STATUS_OK) {
    Serial.print(F("Autentikasi gagal: "));
    Serial.println(mfrc522.GetStatusCodeName(status));
    return;
  }

  Read_Block();
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Scan Sidik Jari");
  if (buffer1[2] == 82) { 
    uint8_t id = getFingerprintID();
    if (id == 1) {
      Serial.println("Akses Diterima!");
      absenSiswa();
    } else if(id == 9) {
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("Absen Ditolak!");
      delay(2000);
      lcd.clear();
      lcd.setCursor(12, 0);
      lcd.print("Sidik jari tidak di temukan!");
      for(int i = 0; i < 28; i++) {
        lcd.scrollDisplayLeft();
        delay(300);
      }
      Serial.println("Sidik jari tidak di temukan!");
    } else {
      Serial.println("Akses Ditolak! Sidik Jari berbeda!");
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("Absen Ditolak!");
      delay(2000);
      lcd.clear();
      lcd.setCursor(12, 0);
      lcd.print("Sidik jari berbeda!");
      for(int i = 0; i < 30; i++) {
        lcd.scrollDisplayLeft();
        delay(300);
      }
    } 
  }

  if (buffer1[2] == 32) { 
    uint8_t id = getFingerprintID();
    if (id == 4) {
      Serial.println("Akses Diterima!");
    } else if(id == 0) {
      lcd.setCursor(0, 0);
      lcd.print("Akses Ditolak!");
      delay(2000);
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.rightToLeft();
      lcd.print("Sidik jari tidak di temukan!");
      Serial.println("Sidik jari tidak di temukan!");
    } else {
      Serial.println("Akses Ditolak! Sidik Jari berbeda!");
    }
  }

  if (buffer1[2] == 122) { 
    uint8_t id = getFingerprintID();
    if (id == 5) {
      Serial.println("Akses Diterima!");
      absenSiswa();
    } else if(id == 0) {
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("Akses Ditolak!");
      delay(2000);
      lcd.clear();
      lcd.setCursor(12, 0);
      lcd.print("Sidik jari tidak di temukan!");
      for(int i = 0; i < 28; i++) {
        lcd.scrollDisplayLeft();
        delay(300);
      }
      Serial.println("Sidik jari tidak di temukan!");
    } else {
      Serial.println("Akses Ditolak! Sidik Jari berbeda!");
    }
  }
  lcd.clear();
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
}


void enrollFingerprint() {
    Serial.println("Masukkan ID untuk sidik jari:");
    while (Serial.available() == 0);
    int id = Serial.parseInt();
    if (id == 0) return;
    Serial.print("Mendaftarkan sidik jari dengan ID: ");
    Serial.println(id);

    lcd.clear();
    lcd.print("Letakkan Jari");
    while (finger.getImage() != FINGERPRINT_OK);
    finger.image2Tz(1);
    lcd.clear();
    lcd.print("Lepaskan Jari");
    delay(2000);

    lcd.clear();
    lcd.print("Letakkan Kembali");
    while (finger.getImage() != FINGERPRINT_OK);
    finger.image2Tz(2);

    if (finger.createModel() == FINGERPRINT_OK && finger.storeModel(id) == FINGERPRINT_OK) {
        lcd.clear();
        lcd.print("Berhasil!");
        Serial.println("Sidik jari berhasil disimpan!");
    } else {
        lcd.clear();
        lcd.print("Gagal!");
        Serial.println("Gagal menyimpan sidik jari!");
    }
}

uint8_t getFingerprintID() {
    while (finger.getImage() != FINGERPRINT_OK);
    finger.image2Tz();
    if (finger.fingerSearch() == FINGERPRINT_OK) {
        Serial.print("Sidik Jari terverifikasi! ID: ");
        Serial.println(finger.fingerID);
        return finger.fingerID;
    }
    Serial.println("Sidik Jari tak ditemukan");
    return 0;
}

uint8_t Read_Block() {
    status = mfrc522.MIFARE_Read(block, buffer1, &len);
    if (status != MFRC522::STATUS_OK) {
        Serial.print(F("Pembacaan gagal: "));
        Serial.println(mfrc522.GetStatusCodeName(status));
        return 0;
    }
    return 1;
}

void absenSiswa() {
    lcd.clear();
    lcd.print("Absen Siswa:");
    Serial.print("Siswa atas nama: ");
    lcd.setCursor(0, 1);
    for (byte i = 0; i < 16; i++) {
        lcd.write(buffer1[i]);
        Serial.write(buffer1[i]);
    }
    Serial.println(" Sudah Masuk!");
    delay(5000);
    lcd.clear();
    lcd.print("Sudah Masuk!");
    Serial.println(F("\n**Proses Selesai**\n"));
    delay(3000);
    lcd.clear();
}
