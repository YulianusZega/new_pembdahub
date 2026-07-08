# 🏆 SISTEM PRESTASI - LOGIKA OTOMATIS

## Overview

Sistem prestasi dirancang untuk **otomatis dan cerdas** berdasarkan sekolah tujuan siswa, mengeliminasi input manual yang tidak relevan.

---

## Aturan Jalur Prestasi

### A. TUJUAN: SMAS Pembda 1 / SMKS Pembda (ID: 2, 3)

**Level Asal:** SMP (Otomatis)  
**Kelas:** IX (Otomatis)

#### Pertanyaan: _"Apakah Anda lulusan SMPS Pembda 2?"_

**✅ JAWABAN: YA** (Dari SMPS Pembda 2)

- **Input Required:**
    - Pilih Juara: 1 / 2 / 3 (dropdown)
    - Tahun Ajaran: 2025/2026 (input text)
    - Upload Bukti: Raport/Piagam (file)
- **Auto-filled:**
    - Nama Sekolah: "SMPS Pembda 2 Gunungsitoli"
    - Kelas: IX
- **Pembebasan:**
    - Juara 1: Rp 50.000 (100%)
    - Juara 2/3: Eligible tapi perlu approval manual

**❌ JAWABAN: TIDAK** (Dari SMP Lain)

- **Input Required:**
    - Nama Sekolah Asal: (input text)
    - Tahun Ajaran: 2025/2026 (input text)
    - Upload Bukti: Raport/Piagam (file)
- **Auto-filled:**
    - Juara: 1 (hanya juara 1 yang eligible)
    - Kelas: IX
- **Pembebasan:**
    - Juara 1: Rp 50.000 (100%)

---

### B. TUJUAN: SMPS Pembda 2 (ID: 1)

**Level Asal:** SD (Otomatis)  
**Kelas:** 6 (Otomatis)  
**Juara:** 1 (Otomatis)

**Input Required:**

- Nama Sekolah Asal (SD): (input text)
- Tahun Ajaran: 2025/2026 (input text)
- Upload Bukti: Raport/Piagam (file)

**Auto-filled:**

- Juara: 1 (hanya juara 1 yang eligible)
- Kelas: 6

**Pembebasan:**

- Juara 1 Kelas 6: Rp 50.000 (100%)

---

## Poin Sistem

| Peringkat | Poin |
| --------- | ---- |
| Juara 1   | 20.0 |
| Juara 2   | 15.0 |
| Juara 3   | 10.0 |

---

## Flow Chart

```
User memilih Jalur Pendaftaran
    ↓
    Prestasi?
    ↓
Deteksi Sekolah Tujuan
    ↓
┌───────────────────────────────────────┬────────────────────────────┐
│ Tujuan: SMA/SMK (ID 2, 3)             │ Tujuan: SMP (ID 1)         │
├───────────────────────────────────────┼────────────────────────────┤
│ Pertanyaan: Lulusan SMPS Pembda 2?    │ (Otomatis dari SD)         │
│ ┌──────────────┬──────────────────┐   │                            │
│ │ YA           │ TIDAK            │   │ Input:                     │
│ ├──────────────┼──────────────────┤   │ - Nama Sekolah (SD)        │
│ │ Pilih Juara: │ Input:           │   │ - Tahun Ajaran             │
│ │ - 1 / 2 / 3  │ - Nama Sekolah   │   │ - Upload Raport            │
│ │ Tahun Ajaran │ - Tahun Ajaran   │   │                            │
│ │ Upload       │ - Upload         │   │ Auto-filled:               │
│ │              │                  │   │ - Juara: 1                 │
│ │ Auto:        │ Auto:            │   │ - Kelas: 6                 │
│ │ - Sekolah:   │ - Juara: 1       │   │                            │
│ │   SMPS P2    │ - Kelas: IX      │   │                            │
│ │ - Kelas: IX  │                  │   │                            │
│ └──────────────┴──────────────────┘   │                            │
└───────────────────────────────────────┴────────────────────────────┘
    ↓                                       ↓
Submit → Backend validates → Store achievement → Auto-check exemption
```

---

## Database Fields

### Hidden Fields (Auto-populated by JavaScript)

```html
<input type="hidden" name="achievement_rank" id="achievement_rank_hidden" />
<input type="hidden" name="achievement_grade" id="achievement_grade_hidden" />
<input type="hidden" name="achievement_school" id="achievement_school_final" />
<input type="hidden" name="achievement_year" id="achievement_year_final" />
```

### File Upload

```html
<input type="file" name="achievement_certificate" ... />
```

---

## JavaScript Logic

### 1. School Detection

```javascript
const targetSchoolId = schoolIdSelect.value;

if (targetSchoolId == "2" || targetSchoolId == "3") {
    // Show SMA/SMK form
} else if (targetSchoolId == "1") {
    // Show SMPS form
}
```

### 2. Conditional Display

```javascript
// For SMA/SMK targets
if (fromSmpsPembda === "ya") {
    // Show rank dropdown (1/2/3)
    // Auto-fill: school="SMPS Pembda 2", grade=9
} else if (fromSmpsPembda === "tidak") {
    // Show school input
    // Auto-fill: rank=1, grade=9
}

// For SMPS target
// Auto-fill: rank=1, grade=6
```

### 3. Before Submit

```javascript
// Consolidate all data to hidden fields
achievement_rank_hidden.value = selectedRank;
achievement_grade_hidden.value = autoGrade;
achievement_school_final.value = schoolName;
achievement_year_final.value = year;
```

---

## Backend Processing

### Controller: PublicRegistrationController@store

```php
// Achievement creation
$achievementName = "Juara {$rank} Kelas {$grade}";
$points = calculateClassRankPoints($rank); // 20/15/10

ApplicantAchievement::create([
    'applicant_id' => $applicant->id,
    'achievement_name' => $achievementName,
    'achievement_type' => 'academic',
    'achievement_level' => 'school',
    'rank' => $rank,
    'organizer' => $schoolName,
    'year' => (int) substr($year, 0, 4),
    'certificate_path' => $certificatePath,
    'points' => $points,
    'notes' => "Juara Kelas - {$schoolName}",
]);

// Auto-check exemption
$exemptionService->autoCheckAndApply($applicant);
```

---

## Validation Rules

```php
'achievement_rank' => 'required_if:admission_path,prestasi|nullable|in:1,2,3',
'achievement_grade' => 'required_if:admission_path,prestasi|nullable|in:6,7,8,9',
'achievement_year' => 'required_if:admission_path,prestasi|nullable|string',
'achievement_school' => 'required_if:admission_path,prestasi|nullable|string',
'achievement_certificate' => 'required_if:admission_path,prestasi|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
```

---

## Exemption Rules (Database Seeder)

### AchievementFeeExemptionSeeder

```php
// Juara 1 Kelas from SMPS Pembda 2 → SMA/SMK Pembda
[
    'source_school_id' => 1, // SMPS Pembda 2
    'target_school_id' => 2, // SMAS Pembda 1
    'achievement_criteria' => [
        'level' => 'school',
        'rank' => '1',
        'min_points' => 20
    ],
    'exemption_type' => 'registration_fee',
    'exemption_value' => 50000,
]
```

---

## Testing Checklist

### ✅ Test Case 1: SMA dari SMPS Pembda 2

- [ ] Pilih Tujuan: SMAS Pembda 1
- [ ] Pilih Jalur: Prestasi
- [ ] Pilih: Ya (Lulusan SMPS Pembda 2)
- [ ] Pilih Juara: 1
- [ ] Upload bukti
- [ ] Verify: Exemption Rp 50.000 applied

### ✅ Test Case 2: SMA dari SMP Lain

- [ ] Pilih Tujuan: SMAS Pembda 1
- [ ] Pilih Jalur: Prestasi
- [ ] Pilih: Tidak (SMP Lain)
- [ ] Input: Nama Sekolah Asal
- [ ] Upload bukti
- [ ] Verify: Juara 1 auto-filled, Exemption applied

### ✅ Test Case 3: SMP dari SD

- [ ] Pilih Tujuan: SMPS Pembda 2
- [ ] Pilih Jalur: Prestasi
- [ ] Input: Nama SD Asal
- [ ] Upload bukti
- [ ] Verify: Juara 1 Kelas 6 auto-filled, Exemption applied

---

## Key Benefits

1. **User-Friendly**: Hanya input yang relevan muncul
2. **Reduced Errors**: Auto-fill eliminasi kesalahan input
3. **Smart Validation**: Rules based on school context
4. **Auto-Exemption**: Langsung apply pembebasan biaya
5. **Scalable**: Mudah tambah aturan baru per sekolah

---

**Last Updated:** February 9, 2026  
**Version:** 2.0  
**Author:** GitHub Copilot
