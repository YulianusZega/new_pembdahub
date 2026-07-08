# [UPDATE 2026-02-01] CATATAN PERUBAHAN

- Menu "Absensi" (biasa) dihapus, hanya "Absensi Kelas (Bulk)" yang tampil di sidebar (grup Akademik)
- Admin hanya dapat melihat data nilai, tidak dapat menambah/mengedit/menghapus nilai siswa
- Tampilan nilai: satu baris per siswa, kolom nilai per jenis (Tugas, UTS, UAS, Sikap), kolom NISN, dan rata-rata nilai

# Phase 3 Quick Start Guide

## [UPDATE 2026-02-01] CATATAN PENTING

- Menu "Absensi" (biasa) dihapus, hanya "Absensi Kelas (Bulk)" yang tampil di sidebar dan dipindah ke grup Akademik.
- Hak akses admin: hanya bisa melihat data nilai, tidak bisa tambah/edit/hapus nilai siswa.
- Tampilan nilai: satu baris per siswa, kolom nilai per jenis (Tugas, UTS, UAS, Sikap), ada kolom NISN dan rata-rata nilai.

## Langkah-Langkah Menjalankan Sistem Authentication

### 1. Generate Test Data

Jalankan command berikut di terminal untuk membuat test users dan data awal:

```bash
cd c:\xampp\htdocs\pembdahub
php artisan db:seed --class=DatabaseSeeder
```

Setelah perintah ini berhasil, database akan terisi dengan:

- 3 Sekolah
- 1 SuperAdmin
- 3 Admin Sekolah (1 per sekolah)
- 1 Guru
- 1 Siswa
- 1 Orang Tua

### 2. Start Development Server

```bash
php artisan serve
```

Server akan berjalan di `http://127.0.0.1:8000`

### 3. Testing Authentication

#### Test 1: SuperAdmin Login

1. Buka `http://127.0.0.1:8000/login`
2. Email: `superadmin@pembdahub.com`
3. Password: `password`
4. Click "Masuk"
5. Harusnya redirect ke `/admin/dashboard` (warna indigo)

#### Test 2: Admin Sekolah Login

1. Buka `http://127.0.0.1:8000/login`
2. Email: `admin@sma1bandung.sch.id`
3. Password: `password`
4. Click "Masuk"
5. Harusnya redirect ke `/sekolah/dashboard` (warna biru)

#### Test 3: Guru Login

1. Buka `http://127.0.0.1:8000/login`
2. Email: `adi.kusuma@sma1bandung.sch.id`
3. Password: `password`
4. Click "Masuk"
5. Harusnya redirect ke `/guru/dashboard` (warna hijau)

#### Test 4: Siswa Login

1. Buka `http://127.0.0.1:8000/login`
2. Email: `andi@student.sch.id`
3. Password: `password`
4. Click "Masuk"
5. Harusnya redirect ke `/siswa/dashboard` (warna kuning)

#### Test 5: Orang Tua Login

1. Buka `http://127.0.0.1:8000/login`
2. Email: `wijaya.parent@mail.com`
3. Password: `password`
4. Click "Masuk"
5. Harusnya redirect ke `/orang-tua/dashboard` (warna pink)

### 4. Test Role-Based Access Control

Setelah login dengan suatu role, coba akses dashboard role lain:

```
Sebagai Guru, coba akses: /admin/dashboard
Harusnya muncul error 403 Forbidden
```

### 5. Test Logout

Klik tombol "Logout" di dashboard. User akan di-logout dan kembali ke home page.

### 6. Test Login History

Setiap kali login, akan tercatat di tabel `login_histories` dengan:

- User ID
- IP Address
- User Agent
- Login time
- Status (active/logout)

### 7. Test Activity Logging

Setiap aksi dicatat di tabel `activity_logs` dengan:

- User ID
- Action (login/logout/create/update/etc)
- Description
- IP Address
- User Agent
- Timestamp

---

## File Structure Penting

```
app/
├── Models/ (24 files)
│   └── Semua model dengan relationships
├── Http/Controllers/Auth/
│   └── AuthController.php (Login, register, logout logic)
├── Http/Middleware/
│   ├── CheckRole.php (Role verification)
│   ├── CheckPermission.php (Permission checking)
│   └── SessionTimeout.php (Auto logout)
└── Http/Kernel.php (Middleware registration)

routes/
└── web.php (Auth routes & dashboards)

resources/views/
├── index.blade.php (Homepage)
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   └── forgot-password.blade.php
└── [admin/sekolah/guru/siswa/orangtua]/
    └── dashboard.blade.php (Role-specific)

database/seeders/
└── DatabaseSeeder.php (Test data)
```

---

## Common Issues & Solutions

### Issue: "Class 'App\Models\User' not found"

**Solution:** Pastikan menggunakan `php artisan` bukan `php` langsung

### Issue: CSRF token mismatch

**Solution:** Pastikan form menggunakan `@csrf` blade directive

### Issue: Migration errors

**Solution:** Pastikan database sudah ada dan koneksi MySQL aktif

### Issue: Seeder tidak berjalan

**Solution:** Jalankan `php artisan db:seed` dengan seeder class name spesifik

---

## Database Queries untuk Testing

```sql
-- Lihat semua users
SELECT * FROM users;

-- Lihat login history
SELECT * FROM login_histories ORDER BY id DESC;

-- Lihat activity logs
SELECT * FROM activity_logs ORDER BY id DESC;

-- Lihat users aktif
SELECT * FROM users WHERE is_active = 1;

-- Lihat users by role
SELECT * FROM users WHERE role = 'guru';
```

---

## Fitur Authentication yang Sudah Implemented

### ✅ Login

- Email validation
- Password hashing verification
- Remember me (30 hari)
- Auto login history record
- Auto activity logging
- Role-based redirect

### ✅ Registration

- Hanya untuk Siswa dan Orang Tua
- Email validation & uniqueness
- Password confirmation
- Strong password requirement
- Account activation pending

### ✅ Logout

- Session invalidation
- CSRF token regeneration
- Activity logging
- Login history update

### ✅ Session Management

- 120 minute timeout
- Last activity tracking
- Auto logout on timeout
- Secure cookies

### ✅ RBAC

- 5 distinct roles
- Role-based middleware
- Permission matrix
- Helper methods for checking roles

---

## Persiapan untuk Phase 4

Phase 4 akan membangun:

- CRUD controllers untuk Master Data
- Data validation rules
- Business logic implementation
- Admin panel untuk data management

Pastikan Phase 3 berjalan dengan lancar sebelum melanjutkan ke Phase 4!

---

**Status: Ready for Phase 4 ✅**
