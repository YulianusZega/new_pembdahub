# Cara Kerja Fungsi Hapus Penugasan Jabatan

## Overview

Sistem sekarang memiliki **2 jenis hapus** yang berbeda:

### 1. ❌ Hapus Jabatan Tertentu (Per Item)

**Lokasi:** Tombol (X) kecil di setiap card jabatan (muncul saat hover)

**Fungsi:**

- Menghapus **1 jabatan saja** dari guru tersebut
- Jabatan lain tetap aktif
- Contoh: Hapus jabatan "Wali Kelas" tapi "Koordinator Mata Pelajaran" tetap

**Cara Pakai:**

1. Hover mouse di atas card jabatan yang ingin dihapus
2. Tombol (X) merah akan muncul di kanan
3. Klik tombol (X)
4. Konfirmasi: "Yakin ingin menghapus jabatan [Nama Jabatan] dari [Nama Guru]?"
5. Klik OK → Jabatan dihapus

**Route:** `DELETE /admin/assignments/positions/{employee}/position/{position}`

**Controller Method:** `destroySinglePosition()`

**What Happens:**

```php
// Set end_date untuk 1 posisi tertentu saja
$employee->employeePositions()
    ->where('position_id', $positionId)
    ->where('academic_year_id', $academicYearId)
    ->where('semester', $semester)
    ->whereNull('end_date')
    ->update(['end_date' => now()]);
```

**Data di Database:**

- Record di `employee_positions` TIDAK dihapus fisik
- Hanya kolom `end_date` di-set ke waktu sekarang
- Data historis tetap tersimpan untuk audit

---

### 2. 🗑️ Hapus Semua Jabatan

**Lokasi:** Tombol merah "Hapus Semua" di kolom Aksi

**Fungsi:**

- Menghapus **SEMUA jabatan** dari guru tersebut
- Untuk tahun ajaran dan semester yang sedang dipilih (di filter)
- Contoh: Menghapus semua 3 jabatan sekaligus (Wali Kelas, Koordinator, Bendahara)

**Cara Pakai:**

1. Klik tombol merah "Hapus Semua" di kolom Aksi
2. Konfirmasi muncul:

    ```
    ⚠️ PERHATIAN!

    Anda akan menghapus SEMUA jabatan (3 jabatan) dari
    [Nama Guru] untuk tahun ajaran dan semester ini.

    Yakin ingin melanjutkan?
    ```

3. Klik OK → Semua jabatan dihapus

**Route:** `DELETE /admin/assignments/positions/{employee}`

**Controller Method:** `destroy()`

**What Happens:**

```php
// Set end_date untuk SEMUA posisi guru tersebut
$employee->employeePositions()
    ->where('academic_year_id', $academicYearId)
    ->where('semester', $semester)
    ->whereNull('end_date')
    ->update(['end_date' => now()]);
```

**Data di Database:**

- Semua record di `employee_positions` untuk guru tersebut
- Kolom `end_date` di-set ke waktu sekarang
- Data historis tetap tersimpan

---

## Perbedaan Visual

### Hapus Jabatan Tertentu

```
┌─────────────────────────────────────────────────┐
│ ⭐ Wali Kelas                           [X] ←── │  Tombol kecil di sini
│    Ganjil  🏫 VII-A                         200k│  (muncul saat hover)
└─────────────────────────────────────────────────┘
```

### Hapus Semua

```
┌──────────────────────────────────────────┐
│ [Edit]  [Hapus Semua] ←──────────────────│  Tombol besar merah
└──────────────────────────────────────────┘
```

---

## Use Cases

### Scenario 1: Guru Pindah Wali Kelas

**Problem:** Guru A wali kelas VII-A, sekarang harus pindah ke VII-B

**Solution:**

- Gunakan **Edit** (bukan Hapus)
- Update classroom_id ke VII-B
- Data historis tetap utuh

### Scenario 2: Guru Turun dari 1 Jabatan

**Problem:** Guru B punya 3 jabatan (Wali Kelas, Koordinator, Bendahara). Sekarang berhenti jadi Bendahara.

**Solution:**

- Gunakan **Hapus Jabatan Tertentu**
- Hover di card "Bendahara" → Klik (X)
- Wali Kelas dan Koordinator tetap aktif

### Scenario 3: Guru Resign/Mutasi

**Problem:** Guru C keluar dari sekolah, semua jabatan harus dicabut

**Solution:**

- Gunakan **Hapus Semua Jabatan**
- Klik tombol "Hapus Semua"
- Semua jabatan di-end sekaligus

### Scenario 4: Update Jabatan (Ganti Periode)

**Problem:** Tahun ajaran baru, jabatan sama tapi SK baru

**Solution:**

- Jangan gunakan Hapus!
- Buat penugasan baru via menu "Tambah Penugasan"
- System otomatis close penugasan lama dan buat yang baru

---

## Technical Details

### Database Schema: `employee_positions`

```sql
CREATE TABLE employee_positions (
    id BIGINT PRIMARY KEY,
    employee_id BIGINT,
    position_id BIGINT,
    academic_year_id BIGINT,
    semester ENUM('ganjil', 'genap', 'full_year'),
    start_date DATE,
    end_date DATE NULL,  ← NULL = masih aktif
    is_primary BOOLEAN,
    classroom_id BIGINT NULL,
    sk_number VARCHAR(100),
    sk_date DATE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Filtering Active Positions

```php
// Query untuk ambil jabatan aktif
$activePositions = $employee->employeePositions()
    ->where('academic_year_id', $currentYearId)
    ->where('semester', 'ganjil')
    ->whereNull('end_date')  // ← Ini yang bikin "aktif"
    ->get();
```

### Authorization

Kedua fungsi hapus memiliki authorization check:

```php
// Non-superadmin hanya bisa hapus di sekolahnya sendiri
if (!$user->isSuperAdmin() && $employee->school_id !== $user->school_id) {
    abort(403, 'Unauthorized');
}
```

---

## Error Handling

### Case 1: Jabatan Sudah Dihapus

**Symptom:** Klik hapus tapi muncul error "Jabatan tidak ditemukan"

**Cause:** Jabatan tersebut sudah punya `end_date` (sudah dihapus sebelumnya)

**Solution:** Refresh halaman, jabatan tidak akan muncul lagi

### Case 2: Tidak Ada Jabatan

**Symptom:** Tombol "Hapus Semua" tidak muncul

**Cause:** Guru belum punya penugasan untuk tahun ajaran/semester yang dipilih

**Solution:** Normal, tambahkan penugasan dulu

### Case 3: Primary Position Dihapus

**Symptom:** Hapus jabatan yang is_primary = 1

**Result:** Berhasil dihapus. Jika ada jabatan lain, tidak otomatis jadi primary

**Best Practice:** Set jabatan lain jadi primary dulu sebelum hapus

---

## Testing Checklist

### Test Hapus Jabatan Tertentu

- [ ] Hover di card jabatan → Tombol (X) muncul
- [ ] Klik (X) → Konfirmasi muncul dengan nama jabatan yang benar
- [ ] Klik OK → Success message: "Jabatan berhasil dihapus"
- [ ] Refresh → Jabatan hilang dari list
- [ ] Jabatan lain masih ada (tidak ikut terhapus)
- [ ] Total tunjangan berkurang sesuai tunjangan jabatan yang dihapus

### Test Hapus Semua Jabatan

- [ ] Tombol "Hapus Semua" muncul jika ada jabatan
- [ ] Klik "Hapus Semua" → Konfirmasi muncul dengan jumlah jabatan
- [ ] Klik OK → Success message: "Semua penugasan jabatan berhasil dihapus"
- [ ] Refresh → Semua jabatan hilang
- [ ] Row guru masih muncul dengan status "Belum ada penugasan"
- [ ] Total tunjangan = Rp 0

### Test Authorization

- [ ] Login sebagai admin sekolah A
- [ ] Coba hapus jabatan guru dari sekolah B → Error 403
- [ ] Login sebagai superadmin → Bisa hapus semua sekolah

### Test Data Integrity

- [ ] Cek database: `SELECT * FROM employee_positions WHERE end_date IS NOT NULL`
- [ ] Record masih ada (soft delete, bukan hard delete)
- [ ] end_date terisi dengan timestamp hapus
- [ ] Bisa query history jabatan per guru

---

## Best Practices

### 1. Jangan Hard Delete

❌ **JANGAN:**

```php
$employee->employeePositions()->delete(); // Hard delete
```

✅ **LAKUKAN:**

```php
$employee->employeePositions()->update(['end_date' => now()]); // Soft delete
```

**Alasan:** Perlu data historis untuk:

- Audit
- Laporan keuangan retroaktif
- Verifikasi masa kerja

### 2. Backup Sebelum Hapus Bulk

Jika akan hapus banyak data:

```bash
mysqldump -u root pembda_hub employee_positions > backup_before_cleanup.sql
```

### 3. Gunakan Edit, Bukan Hapus+Tambah

Untuk update data (ganti kelas, ganti SK):

- Gunakan tombol **Edit**
- Jangan hapus lalu tambah baru

**Alasan:** Preserve continuity, SK history, dan relasi data

### 4. Confirm Message Harus Jelas

```javascript
// BAD
confirm("Hapus?");

// GOOD
confirm("⚠️ PERHATIAN!\n\nAnda akan menghapus SEMUA jabatan (3 jabatan)...");
```

---

## API Reference

### DELETE Single Position

```
DELETE /admin/assignments/positions/{employee}/position/{position}

Request Body:
{
    "academic_year_id": 1,
    "semester": "ganjil"
}

Response Success (200):
{
    "message": "Jabatan berhasil dihapus."
}

Response Error (404):
{
    "message": "Jabatan tidak ditemukan atau sudah dihapus."
}
```

### DELETE All Positions

```
DELETE /admin/assignments/positions/{employee}

Request Body:
{
    "academic_year_id": 1,
    "semester": "ganjil"
}

Response Success (200):
{
    "message": "Semua penugasan jabatan berhasil dihapus."
}
```

---

## Changelog

### Version 1.1 (Current)

- ✅ Added individual position delete (per item)
- ✅ Enhanced delete all confirmation message
- ✅ Added hover effect for delete button
- ✅ Improved UX with separate delete options

### Version 1.0 (Previous)

- ❌ Only had "delete all" option
- ❌ No way to delete single position
- ❌ Confusing delete behavior

---

## Support

Jika ada pertanyaan atau bug, dokumentasikan:

1. User yang melakukan aksi
2. Data guru yang di-hapus
3. Screenshot error message
4. Query database untuk verifikasi

Untuk recovery data yang terhapus:

```sql
-- Restore jabatan yang terhapus (batalkan end_date)
UPDATE employee_positions
SET end_date = NULL
WHERE id = [ID_RECORD]
AND end_date IS NOT NULL;
```
