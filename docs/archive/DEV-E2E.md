# DEV E2E TESTING

---

## [UPDATE 2026-02-01] CATATAN PERUBAHAN

- Menu "Absensi" (biasa) dihapus, hanya "Absensi Kelas (Bulk)" yang tampil di sidebar (grup Akademik)
- Admin hanya dapat melihat data nilai, tidak dapat menambah/mengedit/menghapus nilai siswa
- Tampilan nilai: satu baris per siswa, kolom nilai per jenis (Tugas, UTS, UAS, Sikap), kolom NISN, dan rata-rata nilai
  E2E Test Scaffold & Setup

This project includes example E2E test stubs for both Laravel Dusk and Cypress.

Laravel Dusk (PHP)

- To install: run `composer require --dev laravel/dusk` and follow the official Laravel Dusk setup steps.
- Start chrome driver or let Dusk manage it via `php artisan dusk:install`.
- Example test: `tests/Browser/AdminAcademicYearTest.php` (simple smoke test).

Cypress (JS)

- To install: run `npm install --save-dev cypress`.
- Run with `npm run cypress:open` or `npm run cypress:run` (scripts added to `package.json`).
- Example test: `cypress/integration/admin_academic_year.spec.js` (requires a seeded superadmin account matching credentials).

Notes

- The E2E tests are scaffolds only; run/install steps are required on dev/CI environments.
- For CI integration, prefer headless mode: `cypress run` or `php artisan dusk --headless` with appropriate setup.
