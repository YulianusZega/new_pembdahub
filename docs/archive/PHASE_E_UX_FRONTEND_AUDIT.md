# Phase E: UX & Frontend Improvements — Audit Report

**Date:** 2026-02-12  
**Scope:** Blade templates, controllers, JavaScript, error pages, validation, accessibility, navigation

---

## Summary

| Category                                         | Issues Found  | Severity   |
| ------------------------------------------------ | ------------- | ---------- |
| 1. Forms Missing Per-Field `@error` Directives   | 12 form views | Medium     |
| 2. Images Missing `alt` Text                     | 8 instances   | Medium     |
| 3. Labels Missing `for` Attribute                | ~50+ labels   | Medium     |
| 4. `console.log` Left in Production              | 9 instances   | Low-Medium |
| 5. Fetch Calls with Weak Error Handling          | 6 locations   | Medium     |
| 6. No Loading/Disabled State on Form Submit      | ~20 forms     | Medium     |
| 7. Indonesian Locale/Validation Messages Missing | System-wide   | High       |
| 8. Flash Message Display Inconsistency           | Various       | Low        |
| 9. Accessibility (ARIA, Focus, Keyboard)         | Various       | Medium     |
| 10. Copyright Year Hardcoded                     | 1 file        | Low        |

---

## 1. Forms Missing Per-Field `@error` Directives

These form views use `$errors->any()` at the top (general error banner) but **lack per-field `@error` directives** next to individual inputs. Users cannot see which field has the error:

| File                                                    | Form Purpose      |
| ------------------------------------------------------- | ----------------- |
| `resources/views/admin/subjects/create.blade.php`       | Create Subject    |
| `resources/views/admin/subjects/edit.blade.php`         | Edit Subject      |
| `resources/views/admin/schools/create.blade.php`        | Create School     |
| `resources/views/admin/schools/edit.blade.php`          | Edit School       |
| `resources/views/admin/users/create.blade.php`          | Create User       |
| `resources/views/admin/users/edit.blade.php`            | Edit User         |
| `resources/views/admin/schedules/create.blade.php`      | Create Schedule   |
| `resources/views/admin/schedules/edit.blade.php`        | Edit Schedule     |
| `resources/views/admin/classrooms/create.blade.php`     | Create Classroom  |
| `resources/views/admin/classrooms/edit.blade.php`       | Edit Classroom    |
| `resources/views/admin/students/create.blade.php`       | Create Student    |
| `resources/views/admin/students/edit.blade.php`         | Edit Student      |
| `resources/views/treasurer/payments/create.blade.php`   | Record Payment    |
| `resources/views/treasurer/bills/create.blade.php`      | Create Bill       |
| `resources/views/treasurer/bills/bulk-create.blade.php` | Bulk Create Bills |
| `resources/views/admin/positions/create.blade.php`      | Create Position   |
| `resources/views/admin/positions/edit.blade.php`        | Edit Position     |

**Good examples to follow** (already have `@error`):

- `resources/views/admin/teachers/create.blade.php` — per-field `@error` on every input
- `resources/views/admin/employees/create.blade.php` — per-field `@error` on every input
- `resources/views/admin/semesters/create.blade.php` — per-field `@error` on every input
- `resources/views/admin/majors/create.blade.php` — per-field `@error` on every input
- `resources/views/admin/academic_years/create.blade.php` — per-field `@error` on every input

**Action:** Add `@error('field_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror` after every input in the listed forms.

---

## 2. Images Missing `alt` Text

| File                                                         | Line | Element                               |
| ------------------------------------------------------------ | ---- | ------------------------------------- |
| `resources/views/siswa/dashboard.blade.php`                  | L13  | Student photo `<img>` — missing `alt` |
| `resources/views/siswa/profil.blade.php`                     | L20  | Student photo — missing `alt`         |
| `resources/views/guru/dashboard.blade.php`                   | L13  | Teacher photo — missing `alt`         |
| `resources/views/guru/profil.blade.php`                      | L19  | Teacher photo — missing `alt`         |
| `resources/views/orangtua/partials/child-header.blade.php`   | L6   | Student photo — missing `alt`         |
| `resources/views/orangtua/dashboard.blade.php`               | L27  | Student photo — missing `alt`         |
| `resources/views/public/registration.blade.php`              | L406 | Photo preview — missing `alt`         |
| `resources/views/admin/classrooms/assign-homeroom.blade.php` | L86  | Teacher preview — `alt=""` (empty)    |

**Action:** Add descriptive `alt` attributes like `alt="Foto {{ $student->full_name }}"`.

---

## 3. Labels Missing `for` Attribute (Accessibility)

Approximately **50+ `<label>` elements across the codebase lack the `for` attribute** to associate with their input. This breaks screen reader accessibility and click-to-focus behavior.

**Worst offenders (by volume):**

- `resources/views/treasurer/payments/create.blade.php` — 9 labels without `for`
- `resources/views/treasurer/bills/create.blade.php` — labels without `for`
- `resources/views/treasurer/reports/index.blade.php` — 6 labels without `for`
- `resources/views/guru/nilai-input.blade.php` — 4 labels without `for`
- `resources/views/admin/schools/create.blade.php` — labels without `for`
- `resources/views/admin/users/create.blade.php` — labels without `for`

**Note:** Many labels use the pattern `<label class="...">Label Text</label>` followed by `<input>` or `<select>` but no `for="input_id"`.

**Action:** Add `for` attributes to all `<label>` elements and ensure corresponding `id` attributes on inputs.

---

## 4. `console.log` Statements Left in Production Code

All in `resources/views/admin/psb/create.blade.php`:

| Line | Statement                                                     |
| ---- | ------------------------------------------------------------- |
| L628 | `console.log('DOM Content Loaded - Form PSB initialized');`   |
| L651 | `console.log('Admission path select:', admissionPathSelect);` |
| L652 | `console.log('Prestasi section:', prestasiSection);`          |
| L656 | `console.log('Admission path changed to:', this.value);`      |
| L660 | `console.log('Prestasi section shown');`                      |
| L663 | `console.log('Prestasi section hidden');`                     |
| L670 | `console.log('Initial value is prestasi, showing section');`  |
| L710 | `console.log('Program Keahlian loaded:', data);`              |
| L758 | `console.log('Konsentrasi Keahlian loaded:', data);`          |

Also `console.error()` calls that expose internals:

- L672: `console.error('Elements not found!', {...})`
- L729: `console.error('Error loading program keahlian:', error)`
- L768: `console.error('Error loading konsentrasi keahlian:', error)`

**Action:** Remove all `console.log` and replace `console.error` with user-visible error messages.

---

## 5. Fetch/AJAX Calls with Weak Error Handling

Several `fetch()` calls in Blade templates have poor error UX — they silently fail or only log to console:

| File                                                      | Line     | Issue                                                       |
| --------------------------------------------------------- | -------- | ----------------------------------------------------------- |
| `resources/views/admin/psb/create.blade.php`              | L707-730 | `.catch` only logs to `console.error`, no user notification |
| `resources/views/admin/psb/create.blade.php`              | L755-770 | `.catch` only logs to `console.error`, no user notification |
| `resources/views/siswa/lms/show.blade.php`                | L271-278 | Material tracking `.catch(() => {})` — completely silent    |
| `resources/views/admin/teachers/competencies.blade.php`   | L151-165 | `.catch` shows alert but inconsistent with app style        |
| `resources/views/admin/psb/notifications/index.blade.php` | L317-340 | No `.catch` handler on notification test/preview fetches    |
| `resources/views/admin/academic_years/index.blade.php`    | L175     | Toggle active — no error handling visible                   |
| `resources/views/admin/bills/index.blade.php`             | L946     | Bulk waive — missing user feedback on failure               |
| `resources/views/public/registration.blade.php`           | L565-605 | `.catch(() => {})` — completely silent on API failures      |

**Action:** Add visible error toasts/alerts on fetch failures, show loading indicators, and display retry options.

---

## 6. No Loading/Disabled State on Form Submit

Almost **none of the forms** disable the submit button or show a loading spinner on submission. This allows:

- Double-submit of payment forms (critical for financial data)
- Multiple teacher/student creates
- Double quiz submissions

**Critical forms needing submit protection:**

- `resources/views/treasurer/payments/create.blade.php` — Financial payment
- `resources/views/treasurer/payments/bulk-create.blade.php` — Bulk payment
- `resources/views/treasurer/bills/create.blade.php` — Bill creation
- `resources/views/treasurer/bills/bulk-create.blade.php` — Bulk bill creation
- `resources/views/admin/payments/create.blade.php` — Admin payment
- `resources/views/admin/students/create.blade.php` — Student creation
- `resources/views/admin/teachers/create.blade.php` — Teacher creation
- `resources/views/public/registration.blade.php` — Public PSB registration
- `resources/views/guru/nilai-input.blade.php` — Bulk grade input
- `resources/views/siswa/lms/quiz.blade.php` — Quiz submission
- `resources/views/siswa/lms/show.blade.php` — Assignment submission

Only `resources/views/admin/schedules/grid.blade.php` has a loading state pattern.

**Action:** Add `onclick="this.disabled=true; this.form.submit();"` or Alpine.js `x-data` loading pattern to all submit buttons, especially financial forms.

---

## 7. Missing Indonesian Locale for Validation

**Finding:** There is **no `lang/` or `resources/lang/` directory** with Indonesian locale files.

- The `FormRequest` classes (`StoreStudentRequest`, `StoreGradeRequest`) have custom Indonesian messages — **good**.
- But controllers using inline `$request->validate([...])` (e.g., `SubjectController`, `SemesterController`, `MajorController`, `ScheduleController`, `TimeSlotController`) do **not** provide custom messages, so validation errors default to **English**.
- There is no `lang/id/` or `lang/id.json` file for system-wide Indonesian translations.

**Affected controllers with English-only validation:**

| Controller                            | Method                |
| ------------------------------------- | --------------------- |
| `Admin/SubjectController`             | `store()`, `update()` |
| `Admin/SemesterController`            | `store()`, `update()` |
| `Admin/MajorController`               | `store()`, `update()` |
| `Admin/ScheduleController`            | `store()`, `update()` |
| `Admin/TimeSlotController`            | `store()`, `update()` |
| `Admin/SchoolController`              | `store()`, `update()` |
| `Admin/ClassroomController`           | `store()`, `update()` |
| `Admin/AcademicYearController`        | `store()`, `update()` |
| `Admin/PositionController`            | `store()`, `update()` |
| `Admin/KonsentrasiKeahlianController` | `store()`, `update()` |
| `Admin/ProgramKeahlianController`     | `store()`, `update()` |
| `Admin/UserController`                | `store()`, `update()` |
| `Admin/GradeWeightController`         | `update()`            |
| `Guru/NilaiController`                | `store()`, `update()` |
| `PublicRegistrationController`        | `store()`             |

**Action:** Create `lang/id/validation.php` with full Indonesian translations, or add Indonesian `messages()` to all inline validations.

---

## 8. Flash Message Display Inconsistency

Flash messages ARE displayed in all layout files — **good**. But there are inconsistencies:

| Layout                        | Has `success`? | Has `error`? | Has `warning`? | Has `info`? | Auto-dismiss? |
| ----------------------------- | -------------- | ------------ | -------------- | ----------- | ------------- |
| `layouts/admin.blade.php`     | ✅             | ✅           | ❌             | ❌          | ❌            |
| `layouts/guru.blade.php`      | ✅             | ✅           | ❌             | ❌          | ❌            |
| `layouts/siswa.blade.php`     | ✅             | ✅           | ❌             | ❌          | ❌            |
| `layouts/orangtua.blade.php`  | ✅             | ✅           | ❌             | ❌          | ❌            |
| `layouts/treasurer.blade.php` | ✅             | ✅           | ❌             | ❌          | ✅ (JS)       |

**Issues:**

- No `warning` or `info` flash support in any layout
- Only treasurer layout has auto-dismiss JS
- Admin layout has `$errors->any()` block but other layouts handle it differently
- Some pages have duplicate flash handling (layout + page-level), e.g., `treasurer/payments/index.blade.php` has its own success check AND the layout has one

**Action:** Standardize flash message component across all layouts. Add `warning`/`info` support. Add auto-dismiss to all layouts.

---

## 9. Accessibility Issues

### 9.1 Missing `<label for="">` Attributes

See Section 3 — approximately 50+ labels lack `for` attribute.

### 9.2 Forms Without `aria-describedby` for Error Messages

No form inputs use `aria-describedby` to link to error messages, making errors invisible to screen readers.

### 9.3 Inline Forms in JavaScript (guru/nilai.blade.php)

- Lines 333-340: Forms built via string concatenation in JavaScript (`html += '<form...'`) do not include `aria-label` or accessible structure
- Delete confirmation uses `onsubmit="return confirm()"` — not accessible

### 9.4 Modal Dialogs Without Proper ARIA

- `resources/views/admin/schedules/grid.blade.php` — Schedule modal lacks `role="dialog"`, `aria-modal="true"`, `aria-labelledby`
- Focus trapping not implemented in any modal

### 9.5 Interactive Elements Missing Focus Styles

Most custom buttons/links rely on hover effects only, no visible `:focus` ring for keyboard navigation.

### 9.6 Color Contrast

- Several `text-gray-400` and `text-[10px]` elements may fail WCAG AA contrast requirements
- Grade badges use color-only to convey information without text alternatives

---

## 10. Error Pages — Status: GOOD ✅

Custom error pages exist and are well-implemented in Indonesian:

- `resources/views/errors/403.blade.php` — "Akses Ditolak"
- `resources/views/errors/404.blade.php` — "Halaman Tidak Ditemukan"
- `resources/views/errors/419.blade.php` — Session expired
- `resources/views/errors/429.blade.php` — Rate limit
- `resources/views/errors/500.blade.php` — "Kesalahan Server Internal"
- `resources/views/errors/503.blade.php` — Maintenance
- `resources/views/errors/generic.blade.php` — Fallback
- `resources/views/errors/layout.blade.php` — Shared layout

All use consistent layout, Indonesian text, and proper navigation buttons. **No action needed.**

---

## 11. Other Findings

### 11.1 Hardcoded Copyright Year

- `resources/views/auth/login.blade.php` L113: `&copy; 2024 Pembda Hub` — should be `{{ date('Y') }}`
- The error layout already uses `{{ date('Y') }}` — good.

### 11.2 JS-Generated Forms Without CSRF Meta Tag Fallback

- `resources/views/guru/nilai.blade.php` L333-350: JavaScript-built forms use `csrfToken` variable from Blade — works but fragile.

### 11.3 Inconsistent Error Exposure in Controllers

Some controllers expose raw `$e->getMessage()` to users in flash messages:

- `Admin/UserController` L38, L74, L106: `'Gagal mereset password: ' . $e->getMessage()`
- `Admin/TeacherController` L141: `'Gagal menambahkan guru: ' . $e->getMessage()`
- `Admin/EmployeeController` L104, L165: `'Gagal menambahkan/memperbarui pegawai: ' . $e->getMessage()`
- `Treasurer/PaymentController` L139: `'Gagal menyimpan pembayaran: ' . $e->getMessage()`
- `Treasurer/StudentBillController` L377: `'Terjadi kesalahan: ' . $e->getMessage()`
- `Admin/ApplicantController` L338, L738: `'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()`

These may expose SQL errors, internal paths, or sensitive info to end users.

**Action:** Log `$e->getMessage()` and show generic Indonesian user message instead.

### 11.4 No Breadcrumbs

No pages in the application have breadcrumb navigation. Given the deep nesting (Admin > Teachers > Create, Guru > LMS > Course > Quiz > Questions), breadcrumbs would significantly improve navigation.

### 11.5 Delete Confirmations Inconsistent

- Some use `onsubmit="return confirm('...')"` — native browser dialogs
- No styled confirmation modal component exists
- Confirmation text sometimes in Indonesian, sometimes mixed

---

## Recommended Phase E Implementation Plan

### Priority 1 — High Impact, Quick Wins

1. **E-1:** Remove all `console.log` from `admin/psb/create.blade.php` (9 lines)
2. **E-2:** Fix hardcoded copyright year in `auth/login.blade.php`
3. **E-3:** Add `alt` attributes to 8 images missing them
4. **E-4:** Add submit button disable/loading on financial forms (payments, bills)

### Priority 2 — Validation & Error UX

5. **E-5:** Create `lang/id/validation.php` for Indonesian validation messages
6. **E-6:** Add `@error` directives to all 17 form views missing them
7. **E-7:** Replace raw `$e->getMessage()` exposure with safe user messages in ~10 controllers
8. **E-8:** Improve fetch() error handling with visible user notifications

### Priority 3 — Accessibility

9. **E-9:** Add `for` attributes to all labels across forms
10. **E-10:** Add ARIA attributes to modals (`role="dialog"`, `aria-modal`, focus trap)
11. **E-11:** Add `aria-describedby` to inputs linked to error messages

### Priority 4 — Polish

12. **E-12:** Standardize flash message component + add auto-dismiss + warning/info types
13. **E-13:** Create reusable confirmation modal component (replace native `confirm()`)
14. **E-14:** Add breadcrumb component to admin, guru, and treasurer pages
15. **E-15:** Create reusable `<x-form-input>` Blade component with built-in label, error, and accessibility

---

## Files Audited

**Templates:** 80+ Blade files across `admin/`, `guru/`, `siswa/`, `orangtua/`, `treasurer/`, `public/`, `auth/`, `errors/`, `layouts/`  
**Controllers:** 30+ controllers across `Admin/`, `Auth/`, `Guru/`, `OrangTua/`, `Siswa/`, `Treasurer/`  
**JavaScript:** `resources/js/app.js`, `resources/js/bootstrap.js`, plus inline `<script>` blocks in 18+ templates  
**Form Requests:** `StoreStudentRequest`, `StoreGradeRequest`, `UpdateStudentRequest`, `UpdateGradeRequest`, `StoreTeacherRequest`
