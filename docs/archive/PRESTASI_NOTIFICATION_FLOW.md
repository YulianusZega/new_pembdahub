# Dokumentasi: Flow Notifikasi Jalur Prestasi vs Reguler

## 📋 Overview

Sistem pendaftaran kini memiliki dua jalur dengan **pesan konfirmasi yang berbeda**:

1. **Jalur Reguler** → Fokus pada pembayaran
2. **Jalur Prestasi** → Fokus pada verifikasi dokumen prestasi (BEBAS BIAYA)

---

## 🔄 Flow Comparison

### 📘 Jalur Reguler

```
1. Daftar
   ↓
2. Konfirmasi Pendaftaran + Instruksi Pembayaran 💳
   - WhatsApp: Template "psb.registration"
   - Email: Instruksi pembayaran
   - Success Page: Tampilkan info rekening & cara bayar
   ↓
3. Transfer Biaya Pendaftaran (Rp 50.000/300.000)
   ↓
4. Upload Bukti Bayar
   ↓
5. Verifikasi Pembayaran (1x24 jam)
   ↓
6. Konfirmasi Pembayaran Diterima + Link Upload Dokumen
   ↓
7. Upload Dokumen (KK, Akta, Ijazah, Raport)
   ↓
8. Verifikasi Dokumen
   ↓
9. Jadwal Tes Dikirim
   ↓
10. Ikuti Tes
    ↓
11. Pengumuman Hasil
```

### 🏆 Jalur Prestasi

```
1. Daftar + Upload Dokumen Prestasi (Raport/Piagam Juara Kelas)
   ↓
2. Konfirmasi Pendaftaran + Info Proses Verifikasi 🏆
   - WhatsApp: Template "psb.registration.prestasi"
   - Email: Info verifikasi prestasi (BEBAS BIAYA)
   - Success Page: Tampilkan proses verifikasi, TIDAK ada info pembayaran
   ↓
3. Verifikasi Dokumen Prestasi (2-3 hari kerja)
   ↓
   ├─→ ✅ DISETUJUI
   │   ↓
   │   Notifikasi: "psb.prestasi.approved"
   │   + Link Upload Dokumen
   │   ↓
   │   Upload Dokumen (KK, Akta, Ijazah, Raport)
   │   ↓
   │   Verifikasi Dokumen
   │   ↓
   │   Jadwal Tes Dikirim
   │   ↓
   │   Ikuti Tes
   │   ↓
   │   Pengumuman Hasil
   │
   └─→ ❌ DITOLAK
       ↓
       Notifikasi: "psb.prestasi.rejected"
       + Opsi lanjut ke Jalur Reguler (dengan bayar)
       ↓
       (Kembali ke flow reguler dari step 3)
```

---

## 📄 Files Modified

### 1. **resources/views/public/registration-success.blade.php**

#### Changes:

- ✅ Conditional "Langkah Selanjutnya" section
    - Jalur Prestasi: Purple badge, 4 steps fokus verifikasi
    - Jalur Reguler: Blue badge, 4 steps fokus pembayaran

- ✅ Conditional "Informasi Pembayaran" section
    - Hanya muncul untuk Jalur Reguler
    - Jalur Prestasi: Tidak ada section pembayaran, tampilkan "BEBAS BIAYA" di section langkah

- ✅ Conditional "Notification Timeline"
    - Step 2 berbeda:
        - Prestasi: "Hasil Verifikasi Dokumen Prestasi"
        - Reguler: "Konfirmasi Pembayaran"

#### Blade Conditions:

```php
@if($applicant->admission_path === 'prestasi')
    {{-- Tampilan khusus prestasi --}}
@else
    {{-- Tampilan reguler --}}
@endif
```

---

### 2. **app/Services/NotificationService.php**

#### Changes:

- ✅ Method `sendPSBRegistration()` updated
    - Mendeteksi jalur pendaftaran: `$applicant->admission_path`
    - Memilih template WhatsApp berbeda:
        - **Prestasi**: `'psb.registration.prestasi'`
        - **Reguler**: `'psb.registration'`

#### Code:

```php
$templateName = $applicant->admission_path === 'prestasi'
    ? 'psb.registration.prestasi'
    : 'psb.registration';

$waResult = $this->whatsappService->sendTemplate(
    $applicant->phone,
    $templateName,
    $variables
);
```

---

### 3. **config/whatsapp-templates.php**

#### New Templates Added:

1. **'psb.registration.prestasi'** (NEW)
    - 🏆 Header: "PENDAFTARAN JALUR PRESTASI BERHASIL!"
    - 💰 Highlight: "BEBAS BIAYA PENDAFTARAN!"
    - 📋 Info: Proses verifikasi 2-3 hari kerja
    - ✅/❌ Kemungkinan hasil: Disetujui atau Ditolak

2. **'psb.prestasi.approved'** (NEW)
    - ✅ Header: "DOKUMEN PRESTASI DISETUJUI!"
    - 📤 Action: Link upload dokumen lengkap
    - 💡 Reminder: Tetap BEBAS biaya pendaftaran

3. **'psb.prestasi.rejected'** (NEW)
    - ❌ Header: "DOKUMEN PRESTASI TIDAK DISETUJUI"
    - 📝 Info: Alasan penolakan
    - 🔄 Opsi: Lanjut ke Jalur Reguler dengan bayar

#### Template Variables:

- `{nama}` - Nama lengkap
- `{nomor_registrasi}` - Nomor registrasi
- `{sekolah}` - Nama sekolah tujuan
- `{tahun_ajaran}` - Tahun ajaran aktif
- `{prestasi_detail}` - Detail prestasi (e.g., "Juara 1 Kelas 9")
- `{upload_url}` - Link upload dokumen
- `{deadline}` - Deadline upload
- `{alasan_penolakan}` - Alasan jika ditolak
- `{biaya}` - Biaya pendaftaran (untuk opsi reguler)

---

## 🎨 UI/UX Differences

### Halaman Success - Jalur Reguler

- **Warna dominan**: Blue (🔵)
- **Icon utama**: 📝 (form/document)
- **Highlight**: Info rekening bank, cara transfer
- **CTA**: "Lakukan Pembayaran"
- **Timeline**: Pembayaran → Dokumen → Tes

### Halaman Success - Jalur Prestasi

- **Warna dominan**: Purple (🟣)
- **Icon utama**: 🏆 (trophy/achievement)
- **Highlight**: "BEBAS BIAYA PENDAFTARAN!" (green alert)
- **CTA**: "Menunggu Verifikasi"
- **Timeline**: Verifikasi Prestasi → Dokumen → Tes

---

## 📱 WhatsApp Message Preview

### Reguler:

```
✅ PENDAFTARAN BERHASIL!

Halo *John Doe*, pendaftaran Anda berhasil!

📋 No. Registrasi: *SMA-26-0001*
🏫 Sekolah: SMAS Pembda 1
📅 Tahun Ajaran: 2025/2026

🎯 LANGKAH BERIKUTNYA:
1. Transfer biaya pendaftaran Rp 50.000
   Bank: BCA
   No. Rek: 1234567890
   a.n. Yayasan Perguruan Pembangunan Daerah Nias (PEMBDA)

2. Kirim bukti transfer ke WA ini...
```

### Prestasi:

```
🏆 PENDAFTARAN JALUR PRESTASI BERHASIL!

Halo *John Doe*, pendaftaran Anda melalui Jalur Prestasi berhasil!

📋 No. Registrasi: *SMA-26-0001*
🏫 Sekolah: SMAS Pembda 1
📅 Tahun Ajaran: 2025/2026
🎖️ Jalur: *PRESTASI (Juara Kelas)*

💰 BEBAS BIAYA PENDAFTARAN!
Karena Anda mendaftar melalui jalur prestasi, Anda TIDAK PERLU membayar biaya pendaftaran.

🎯 PROSES VERIFIKASI:
1. ⏳ Tim panitia akan memverifikasi dokumen prestasi...
```

---

## 🧪 Testing Checklist

### Test Case 1: Pendaftaran Jalur Reguler

- [ ] Submit form dengan `admission_path = 'reguler'`
- [ ] Cek success page menampilkan:
    - [ ] Badge "Jalur Reguler" (blue)
    - [ ] Section "Informasi Pembayaran" muncul
    - [ ] Rekening bank terlihat
    - [ ] Timeline step 2 = "Konfirmasi Pembayaran"
- [ ] Cek notifikasi WhatsApp:
    - [ ] Menerima template `psb.registration`
    - [ ] Ada instruksi transfer dan nominal
- [ ] Cek database:
    - [ ] `applicants.admission_path = 'reguler'`
    - [ ] Tidak ada record di `applicant_achievements`

### Test Case 2: Pendaftaran Jalur Prestasi

- [ ] Submit form dengan `admission_path = 'prestasi'`
- [ ] Upload dokumen prestasi (JPG/PNG/PDF)
- [ ] Cek success page menampilkan:
    - [ ] Badge "Jalur Prestasi" (purple)
    - [ ] Alert hijau "BEBAS BIAYA PENDAFTARAN!"
    - [ ] Section "Informasi Pembayaran" TIDAK muncul
    - [ ] Timeline step 2 = "Hasil Verifikasi Dokumen Prestasi"
- [ ] Cek notifikasi WhatsApp:
    - [ ] Menerima template `psb.registration.prestasi`
    - [ ] Ada info "BEBAS BIAYA"
    - [ ] Ada info proses verifikasi 2-3 hari
- [ ] Cek database:
    - [ ] `applicants.admission_path = 'prestasi'`
    - [ ] Ada record di `applicant_achievements`
    - [ ] `applicant_fee_exemptions` ada (auto-applied)

### Test Case 3: Verifikasi Prestasi (Manual di Admin)

- [ ] Admin buka detail pendaftar jalur prestasi
- [ ] Admin lihat dokumen prestasi
- [ ] Admin approve/reject:
    - [ ] Jika approve:
        - [ ] Kirim notifikasi `psb.prestasi.approved`
        - [ ] Include link upload dokumen
    - [ ] Jika reject:
        - [ ] Kirim notifikasi `psb.prestasi.rejected`
        - [ ] Include alasan penolakan
        - [ ] Include opsi bayar untuk lanjut reguler

---

## 🔐 Admin Side (Future Development)

### Fitur yang Perlu Ditambahkan di Admin Panel:

1. **List Pendaftar Prestasi yang Perlu Diverifikasi**
    - Filter status: `pending_verification`, `approved`, `rejected`
    - Kolom: No. Reg, Nama, Prestasi, Dokumen, Status, Action

2. **Detail Dokumen Prestasi**
    - Preview image/PDF dokumen prestasi
    - Form input: Hasil Verifikasi (Approve/Reject)
    - Form input: Catatan/Alasan (jika reject)
    - Button: "Setujui", "Tolak"

3. **Action Buttons**
    - "Setujui" → Set status `approved` → Kirim notif `psb.prestasi.approved`
    - "Tolak" → Set status `rejected` → Kirim notif `psb.prestasi.rejected`

4. **Report/Dashboard**
    - Total pendaftar jalur prestasi
    - Pending verifikasi
    - Approved
    - Rejected

---

## 📊 Database Schema

### Table: `applicants`

- `admission_path` ENUM('reguler', 'prestasi')

### Table: `applicant_achievements`

- `applicant_id` (FK)
- `achievement_name` (e.g., "Juara 1 Kelas 9")
- `certificate_path` (storage path)
- `rank` (1, 2, 3)
- `verification_status` ENUM('pending', 'approved', 'rejected') - **TODO: Add this column**
- `verification_notes` TEXT - **TODO: Add this column**
- `verified_at` TIMESTAMP NULL - **TODO: Add this column**
- `verified_by` (FK to users) - **TODO: Add this column**

### Migration Needed:

```php
Schema::table('applicant_achievements', function (Blueprint $table) {
    $table->enum('verification_status', ['pending', 'approved', 'rejected'])
          ->default('pending')->after('points');
    $table->text('verification_notes')->nullable()->after('verification_status');
    $table->timestamp('verified_at')->nullable()->after('verification_notes');
    $table->foreignId('verified_by')->nullable()->constrained('users')->after('verified_at');
});
```

---

## 🚀 Deployment Steps

1. ✅ Update views (registration-success.blade.php)
2. ✅ Update NotificationService.php
3. ✅ Add WhatsApp templates
4. ⏳ Create migration for verification columns
5. ⏳ Build admin verification interface
6. ⏳ Create NotificationService methods:
    - `sendAchievementApproved()`
    - `sendAchievementRejected()`
7. ⏳ Test end-to-end flow
8. ✅ Clear cache: `php artisan optimize:clear`

---

## 📞 Support

Jika ada pertanyaan tentang sistem ini:

- **Email**: tech@pembdanias.sch.id
- **WhatsApp**: 0812-xxxx-xxxx

---

**Dokumentasi dibuat**: 2026-02-09
**Last updated**: 2026-02-09
**Version**: 1.0
