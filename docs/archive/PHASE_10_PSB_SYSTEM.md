# 📋 SISTEM PENERIMAAN SISWA BARU (PSB)

**Status**: ✅ Database & UI Framework Complete  
**Phase**: Phase 10 - Student Admission System  
**Date**: 2026-02-06  
**Version**: 1.0.0

---

## 🎯 BUSINESS REQUIREMENTS

### 1. Biaya Pendaftaran

#### Universal Registration Fee

- **Semua Jenjang**: Rp 50.000
- **Purpose**: Biaya administrasi pendaftaran

#### Equipment Fee (SMK Only)

- **Amount**: Rp 250.000
- **Purpose**: Uang alat praktikum dan bengkel
- **Applies To**: SMK saja

#### Re-registration Fee (Daftar Ulang)

- **SMP**: Rp 500.000
- **SMA**: Rp 750.000
- **SMK**: Rp 1.000.000
- **Timing**: Dibayar setelah diterima, sebelum jadi siswa aktif

---

### 2. Jenis Tes Masuk per Sekolah

#### SMP

- Matematika (weight: 1.0)
- IPA/Sains (weight: 1.0)
- Bahasa Indonesia (weight: 1.0)

#### SMA

- Matematika (weight: 1.2) ⭐ Priority
- IPA/Sains (weight: 1.0)
- IPS/Sosial (weight: 1.0)
- Bahasa Inggris (weight: 1.0)

#### SMK

- Matematika (weight: 1.0)
- Bahasa Indonesia (weight: 1.0)
- Tes Minat & Bakat (weight: 1.2) ⭐ Priority

---

### 3. Formula Penilaian

```
Nilai Akhir = (Raport × 40%) + (Tes × 30%) + (Interview × 20%) + (Prestasi × 10%)
```

**Breakdown**:

- **40% Raport**: Nilai rata-rata raport semester terakhir
- **30% Tes Masuk**: Rata-rata nilai tes (dengan weight)
- **20% Interview**: Penilaian wawancara oleh panitia
- **10% Prestasi**: Total point prestasi yang dimiliki

---

### 4. Sistem Diskon "Best 3" (Khusus SMP)

#### Rank 1

- **Diskon**: 50% SPP
- **Duration**: 12 bulan (1 tahun pertama)
- **Criteria**: Ranking tertinggi berdasarkan nilai akhir

#### Rank 2

- **Diskon**: 30% SPP
- **Duration**: 12 bulan
- **Criteria**: Ranking kedua

#### Rank 3

- **Diskon**: 20% SPP
- **Duration**: 12 bulan
- **Criteria**: Ranking ketiga

**Notes**:

- Diskon otomatis applied setelah ranking dihitung
- Hanya berlaku untuk SPP, tidak untuk biaya lain
- Renewal policy tergantung performa akademik tahun berikutnya

---

### 5. Point Prestasi

| Level         | Point | Description                     |
| ------------- | ----- | ------------------------------- |
| International | 100   | Prestasi tingkat internasional  |
| National      | 80    | Prestasi tingkat nasional       |
| Provincial    | 60    | Prestasi tingkat provinsi       |
| District      | 40    | Prestasi tingkat kabupaten/kota |
| School        | 20    | Prestasi tingkat sekolah        |

**Calculation**: Total semua point prestasi yang dimiliki siswa

---

## 🔄 WORKFLOW STATUS (10 Steps)

```
1. DRAFT → Pendaftar mulai mengisi form (saved)
   ↓
2. SUBMITTED → Pendaftar submit form lengkap
   ↓
3. PAYMENT_VERIFIED → Admin verifikasi pembayaran pendaftaran
   ↓
4. DOCUMENT_VERIFIED → Admin verifikasi dokumen (KK, Akta, Ijazah, Raport)
   ↓
5. TESTED → Pendaftar sudah mengikuti tes masuk
   ↓
6. SCORED → Admin input nilai tes + interview, sistem hitung final score
   ↓
7. ACCEPTED / REJECTED → Admin/Sistem putuskan diterima/ditolak
   ↓
8. REREGISTERED → Siswa diterima bayar daftar ulang
   ↓
9. REGISTERED → Admin klik "Aktifkan sebagai Siswa" (migrate to students table)
   ↓
10. SISWA AKTIF ✅ → Data masuk ke tabel students, auto-generate NIS & akun
```

---

## 🗄️ DATABASE SCHEMA

### 9 Tables Created

#### 1. `admission_fees`

Biaya pendaftaran per sekolah

- registration (Rp 50K universal)
- equipment (Rp 250K SMK only)
- reregistration (varies: SMP 500K, SMA 750K, SMK 1M)

#### 2. `admission_tests`

Konfigurasi tes masuk per sekolah

- Custom subjects per school
- Weighted scoring (1.0 - 1.2)
- Max score: 100

#### 3. `admission_discounts`

Sistem diskon

- Best 3: 50%, 30%, 20% (SMP only)
- Sibling discount: 10%
- Scholarship: 100% (approval required)

#### 4. `applicants` (Main Table)

Data pendaftar dengan lifecycle 10 status

- Dual major choice (for SMK)
- Scoring fields (raport, test, interview, achievement)
- Ranking & final_score
- Timeline timestamps

#### 5. `applicant_documents`

Manajemen dokumen

- KK, Akta, Ijazah, Raport, Certificates
- Upload path & verification status
- Verified_by & verified_at

#### 6. `applicant_test_scores`

Hasil tes per subject

- Link to admission_tests
- Score (0-100)
- Test date

#### 7. `applicant_achievements`

Prestasi pendaftar

- Achievement name & level
- Auto-calculated points
- Supporting documents

#### 8. `applicant_payments`

Tracking pembayaran

- Registration + Equipment (if SMK)
- Reregistration payment
- Verification workflow

#### 9. `applicant_discounts`

Applied discounts dengan audit

- Discount type & amount
- Auto-apply for Best 3
- Approved_by tracking

---

## 📊 VISUAL FLOW TRACKER UI

### Component: `flow-tracker.blade.php`

**Features**:

- 9-step horizontal progress bar
- Animated current step indicator
- Icon-based step representation
- Color-coded status (emerald = passed, gray = pending, red = rejected)
- Hover tooltips for each step
- Real-time progress percentage
- Additional info panel (registration number, submit date, score, ranking)

**Usage**:

```blade
<x-admin.psb.flow-tracker :applicant="$applicant" />
```

**Display**:

```
[🟢]━━━[🟢]━━━[🔵]━━━[⚪]━━━[⚪]━━━[⚪]━━━[⚪]━━━[⚪]━━━[⚪]
Draft  Submit  Pay   Doc   Test  Score Accept Rereg  Active
```

---

## 🎨 UI IMPLEMENTATION

### Dashboard (index.blade.php)

- **Business Rules Info Cards**: 4 cards showing key metrics (50K fee, 250K equipment, 50% best 3, total applicants)
- **Collapsible Rules Accordion**: Detailed business rules display
- **Filter System**: Academic Year → School → Status → Search
- **Table View**: Colorful badges, ranking indicators, status colors
- **Gradient Theme**: Emerald-teal consistent branding

### Detail Page (show.blade.php)

- **Visual Flow Tracker**: Prominent position at top
- **3-Column Layout**:
    - Left: Personal data + parents + scoring
    - Right: Quick actions + school info + timeline
- **Conditional Action Buttons**: Different buttons per status
- **Special Button**: "Aktifkan sebagai Siswa" (only if `canMigrateToStudent()`)

---

## 🔧 MODELS

### Applicant Model

**Relationships**:

- belongsTo: School, AcademicYear, Student, Major (×2 for dual choice)
- hasMany: Documents, TestScores, Achievements, Payments, Discounts

**Methods**:

- `calculateFinalScore()`: Auto-calculate weighted score
- `getStatusBadgeColor()`: Return Tailwind color for status
- `getStatusLabel()`: Human-readable status name
- `canMigrateToStudent()`: Check if ready for activation

### AdmissionFee Model

- Formatted amount display (Rp 50.000)
- School & AcademicYear relationships

### AdmissionTest Model

- Weight-based scoring
- Order for display sequence

### AdmissionDiscount Model

- JSON criteria storage
- Percentage/fixed amount support
- Duration tracking (months)

---

## 🛣️ ROUTES

```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('psb')->name('psb.')->group(function () {
        Route::resource('applicants', ApplicantController::class);
        Route::get('/', [ApplicantController::class, 'index'])->name('index');
    });
});
```

**Available Routes**:

- `GET /admin/psb` → Dashboard
- `GET /admin/psb/applicants/create` → New registration
- `POST /admin/psb/applicants` → Store
- `GET /admin/psb/applicants/{id}` → Detail with flow tracker
- `GET /admin/psb/applicants/{id}/edit` → Edit form
- `PUT /admin/psb/applicants/{id}` → Update
- `DELETE /admin/psb/applicants/{id}` → Delete

---

## 🌱 SEEDERS

### AdmissionDataSeeder

**Seeds**:

1. Admission fees for all 3 schools
2. Custom tests per school type (SMP/SMA/SMK)
3. Discount rules (Best 3 + sibling + scholarship)

**Run Command**:

```bash
php artisan db:seed --class=AdmissionDataSeeder
```

---

## 🚀 NEXT STEPS (To Complete PSB System)

### 1. Migration Service (HIGH PRIORITY) ⏳

**File**: `app/Services/StudentAdmissionService.php`

**Methods to Implement**:

```php
public function migrateToStudent(Applicant $applicant): Student
{
    // 1. Validate applicant is ready (status = reregistered)
    // 2. Start DB transaction
    // 3. Create student record
    // 4. Generate NIS (format: {school_code}-{year}-{sequential})
    // 5. Copy photo from applicants/ to students/
    // 6. Assign to classroom (based on major + grade + capacity)
    // 7. Migrate documents
    // 8. Create user account (username=NISN, password=NISN)
    // 9. Generate initial bills (SPP + apply discounts if Best 3)
    // 10. Update applicant: student_id, status='registered', registered_at
    // 11. Commit transaction
    // 12. Return Student model
}
```

### 2. Forms Implementation ⏳

- [ ] Create form (public-facing registration)
- [ ] Edit form (admin only)
- [ ] Document upload interface
- [ ] Test scoring input form
- [ ] Interview scoring form
- [ ] Achievement input form

### 3. Action Controllers ⏳

- [ ] Verify payment action
- [ ] Verify documents action
- [ ] Input test scores action
- [ ] Accept/Reject applicant action
- [ ] Activate student action (call StudentAdmissionService)

### 4. Public Registration Portal ⏳

- [ ] Public-facing registration page
- [ ] Multi-step wizard (personal → parents → school history → upload docs)
- [ ] Payment instructions page
- [ ] Applicant status check page (by registration number)

### 5. Reports & Export ⏳

- [ ] Applicant list export (Excel/PDF)
- [ ] Ranking report
- [ ] Acceptance letter generation
- [ ] Re-registration invoice

### 6. Notifications ⏳

- [ ] Email notification on submission
- [ ] SMS/Email for test schedule
- [ ] Acceptance/Rejection notification
- [ ] Payment reminder

---

## 📝 TECHNICAL NOTES

### Why Separate Tables (applicants vs students)?

✅ **Clean Separation**: Applicants have different lifecycle than active students
✅ **Data Integrity**: Prevent mixing PSB process with student operations
✅ **Historical Record**: Keep complete PSB history even after migration
✅ **Status Management**: 10 PSB statuses vs 4 student statuses
✅ **Flexible**: Can have multiple applications per NISN (different years)

### Migration Trigger Strategy

✅ **Manual Trigger**: Admin clicks "Aktifkan sebagai Siswa" button
✅ **Controlled**: Traceable action with audit trail
✅ **Safe**: Transaction-based, rollback on error
✅ **Validation**: Only allow if status = 'reregistered' && student_id = null

### Best 3 Discount Auto-Apply Logic

1. After all scoring complete, calculate final_score for all applicants
2. Order by final_score DESC
3. Assign ranking 1, 2, 3, 4, ...
4. For ranking 1-3 (SMP only), auto-create applicant_discount records
5. When migrated to student, these discounts transfer to student_bills

---

## ✅ COMPLETION STATUS

### Completed ✅

- [x] Database schema design (9 tables)
- [x] Migration file created & executed
- [x] Seeders for fees, tests, discounts
- [x] Models with relationships (Applicant, AdmissionFee, AdmissionTest, AdmissionDiscount)
- [x] Controller skeleton (ApplicantController)
- [x] Routes configuration
- [x] Visual flow tracker component
- [x] Dashboard UI with business rules display
- [x] Detail page UI with flow visualization
- [x] Menu integration in admin sidebar
- [x] Color-coded status system
- [x] Responsive layout

### Pending ⏳

- [ ] StudentAdmissionService implementation
- [ ] Registration forms (create/edit)
- [ ] Document upload handling
- [ ] Action buttons implementation (verify, score, accept, activate)
- [ ] Public registration portal
- [ ] Email/SMS notifications
- [ ] Reports & exports
- [ ] Testing with real data

---

## 🎓 CREDITS

**Developed by**: GitHub Copilot  
**System**: PembdaHub - School Management System  
**Client**: Yayasan Pembangunan Daerah  
**Schools**: 3 (SMP, SMA, SMK)

---

**Last Updated**: 2026-02-06 10:45 WIB  
**Documentation Version**: 1.0.0
