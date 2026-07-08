# Cara Deploy Perubahan dari Localhost ke Hosting

## Alur Kerja Deployment (1-Klik via Script)

Karena fitur Git Deploy bawaan dari hPanel bermasalah dengan koneksi SSH, kita menggunakan script deployment custom yang jauh lebih cepat dan terpercaya.

Alur kerjanya sangat sederhana:
```
Local (PC) → git push → Akses URL Deploy → Selesai!
```

## Langkah-Langkah

### 1. Push ke GitHub (dari PC lokal)
Setiap kali selesai melakukan perubahan di lokal, lakukan komit dan push ke branch `main`:
```bash
git add .
git commit -m "deskripsi perubahan"
git push origin main
```

### 2. Jalankan Script Deploy
Buka browser dan akses URL berikut untuk menarik kode terbaru secara otomatis ke server hosting:
```text
https://perguruanpembda.com/git_pull_now.php?secret=pembda99
```

> [!TIP]
> Anda bisa mem-bookmark URL di atas di browser Anda agar proses deploy cukup dengan 1 kali klik saja!

**Script ini akan otomatis melakukan:**
1. `git fetch origin` (Mendownload commit terbaru dari GitHub)
2. `git reset --hard origin/main` (Memaksa server untuk 100% sama dengan GitHub)
3. Membersihkan seluruh cache Laravel (`php artisan cache:clear` versi PHP script)
4. Memverifikasi perbaikan-perbaikan kritis (Health check)

### 3. Selesai
Perhatikan output di layar. Jika tulisan `HEAD is now at ...` sesuai dengan commit terbaru Anda, maka deploy telah berhasil. Anda bisa langsung menguji fitur baru di aplikasi.

---

## Tool Permanen di Server

Jika terjadi kendala cache atau rute 404 tanpa ada perubahan kode, Anda bisa menggunakan tool diagnostik berikut:

| Tool | Akses | Fungsi |
|---|---|---|
| **Deploy Update** | `perguruanpembda.com/git_pull_now.php?secret=pembda99` | Deploy kode terbaru dari GitHub |
| **Clear Cache** | `perguruanpembda.com/clear-cache.php?secret=pembda99` | Clear cache menyeluruh + diagnostik + restore data |
| **Check Deploy** | `perguruanpembda.com/check_deploy.php?token=pembda2026check` | Cek status commit (versi ringan) |
