---
description: Cara sinkronisasi kode dan database saat bekerja di 2 PC
---

# Workflow Sinkronisasi 2 PC

## Prinsip Utama
> **Selalu PULL sebelum mulai kerja, selalu PUSH setelah selesai kerja.**

---

## 🟢 MULAI KERJA (Wajib dilakukan PERTAMA KALI setiap sesi)

### Langkah 1: Pull kode terbaru dari GitHub
// turbo
```powershell
git pull origin main
```
> Jika ada konflik, pilih versi yang benar atau hubungi Gemini untuk bantuan.

### Langkah 2: Jalankan migration (jika ada migration baru)
// turbo
```powershell
php artisan migrate
```
> Ini akan membuat tabel baru yang mungkin ditambahkan di PC lain.

### Langkah 3: Mulai bekerja seperti biasa
Lakukan perubahan kode, testing di localhost, dsb.

---

## 🔴 SELESAI KERJA (Wajib dilakukan SEBELUM pindah ke PC lain)

### Langkah 1: Pastikan semua perubahan sudah di-test

### Langkah 2: Build aset (jika ada perubahan CSS/JS)
// turbo
```powershell
npm run build
```

### Langkah 3: Commit dan Push ke GitHub
// turbo
```powershell
git add .
git commit -m "Deskripsi perubahan"
git push origin main
```

---

## ⚠️ MASALAH UMUM & SOLUSI

### 1. "Lupa push di PC lain"
- Tidak bisa pull karena kode belum di-push dari PC lain
- **Solusi:** Kembali ke PC lain, push perubahannya dulu

### 2. Konflik saat pull
```powershell
# Simpan perubahan lokal sementara
git stash

# Pull kode terbaru
git pull origin main

# Kembalikan perubahan lokal
git stash pop
```
Jika ada konflik setelah `stash pop`, resolve manual atau minta bantuan Gemini.

### 3. Database tidak sinkron (tabel/kolom berbeda)
Jalankan migration:
```powershell
php artisan migrate
```
Jika data di server lebih lengkap dan ingin import:
1. Push `export_db.php` ke server
2. Deploy di Hostinger
3. Buka: `https://www.perguruanpembda.com/pembdahub/export_db.php?token=pembda2026export`
4. Download file .sql
5. Import via phpMyAdmin (localhost/phpmyadmin) → pilih database `pembda_hub` → Import
6. **Hapus `export_db.php` dari server** setelah selesai

### 4. Lupa apakah sudah push atau belum
```powershell
# Cek status - apakah ada perubahan yang belum di-commit
git status

# Cek apakah lokal sudah sinkron dengan remote
git fetch origin
git status
```
Jika output menunjukkan "Your branch is ahead", berarti belum push.
Jika output menunjukkan "Your branch is behind", berarti belum pull.

---

## 📋 CHECKLIST CEPAT

### Mulai kerja di PC (manapun):
- [ ] `git pull origin main`
- [ ] `php artisan migrate`
- [ ] Mulai coding

### Selesai kerja:
- [ ] Test perubahan di browser
- [ ] `npm run build` (jika ubah CSS/JS)
- [ ] `git add .`
- [ ] `git commit -m "pesan"`
- [ ] `git push origin main`

### Deploy ke hosting (opsional):
- [ ] Ikuti workflow `/deploy`
