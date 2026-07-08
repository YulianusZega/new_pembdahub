# Phase C: Code Quality & Maintainability – Audit Report

**Generated:** 2026-02-12  
**Application:** PembdaHub (School Management System)  
**Framework:** Laravel 11

---

## Section 1: Test Coverage Gaps

### 1.1 Existing Tests (27 test files)

| File                                            | Lines | Coverage Area                             |
| ----------------------------------------------- | ----- | ----------------------------------------- |
| `tests/Feature/PSBRegistrationTest.php`         | 1270  | PSB (student admission) registration flow |
| `tests/Feature/LmsTest.php`                     | 1054  | Learning Management System                |
| `tests/Feature/GuruPortalTest.php`              | 536   | Teacher portal                            |
| `tests/Feature/OrangTuaPortalTest.php`          | 401   | Parent portal                             |
| `tests/Feature/SiswaPortalTest.php`             | 394   | Student portal                            |
| `tests/Feature/CriticalUserJourneysTest.php`    | 231   | Cross-cutting user journeys               |
| `tests/Feature/GradeAuthorizationTest.php`      | 181   | Grade access control                      |
| `tests/Feature/StudentAuthorizationTest.php`    | 177   | Student access control                    |
| `tests/Feature/UserAuthorizationTest.php`       | 176   | User access control                       |
| `tests/Unit/Policies/StudentPolicyTest.php`     | 169   | Student policy unit tests                 |
| `tests/Feature/ScheduleCrudTest.php`            | 158   | Schedule CRUD                             |
| `tests/Unit/Policies/UserPolicyTest.php`        | 152   | User policy unit tests                    |
| `tests/Unit/Policies/GradePolicyTest.php`       | 137   | Grade policy unit tests                   |
| `tests/Feature/SchoolCrudTest.php`              | 75    | School CRUD                               |
| `tests/Feature/AcademicYearCrudTest.php`        | 74    | Academic year CRUD                        |
| `tests/Feature/SemesterCrudTest.php`            | 65    | Semester CRUD                             |
| `tests/Feature/MajorCrudTest.php`               | 65    | Major CRUD                                |
| `tests/Feature/StudentImportTest.php`           | 64    | Student CSV import                        |
| `tests/Feature/AdminNavigationTest.php`         | 58    | Admin navigation                          |
| `tests/Feature/StudentControllerTest.php`       | 56    | Student controller                        |
| `tests/Feature/StudentValidationTest.php`       | 55    | Student validation                        |
| `tests/Feature/AcademicYearToggleTest.php`      | 46    | Academic year activation toggle           |
| `tests/Feature/StudentPhotoTest.php`            | 44    | Student photo upload                      |
| `tests/Feature/AdminMenuRoleVisibilityTest.php` | 41    | Role-based menu visibility                |
| `tests/Feature/ParentSeederTest.php`            | 38    | Parent seeder                             |
| `tests/Feature/SubjectImportTest.php`           | 31    | Subject CSV import                        |
| `tests/Feature/StudentLayoutTest.php`           | 23    | Student dashboard layout                  |
| `tests/Browser/AdminAcademicYearTest.php`       | 20    | Browser test (Dusk)                       |

### 1.2 Critical Coverage Gaps – Tests Needed

| Priority | Proposed Test File                                           | Coverage Target                                                            | Reason                                                                     |
| -------- | ------------------------------------------------------------ | -------------------------------------------------------------------------- | -------------------------------------------------------------------------- |
| **P0**   | `tests/Feature/PaymentControllerTest.php`                    | Admin & Treasurer payment CRUD, batch payments, receipt generation         | Financial module has **0 tests**. 391+358 lines of payment logic untested. |
| **P0**   | `tests/Feature/StudentBillControllerTest.php`                | Bill creation, bulk billing, late fees, export                             | Financial billing has **0 tests**. 476+410 lines untested.                 |
| **P0**   | `tests/Feature/ApplicantControllerTest.php`                  | PSB applicant verify/accept/reject flow, score input, payment verification | Largest controller (653 lines) has no dedicated CRUD test.                 |
| **P1**   | `tests/Feature/ReportCardControllerTest.php`                 | Report card generation, finalization, publishing, PDF print                | 469-line controller, no tests. Academic output.                            |
| **P1**   | `tests/Feature/TeacherControllerTest.php`                    | Teacher CRUD, photo upload, user account creation                          | 254 lines, no tests.                                                       |
| **P1**   | `tests/Feature/EmployeeControllerTest.php`                   | Employee CRUD, photo upload, user creation                                 | 196 lines, no tests.                                                       |
| **P1**   | `tests/Feature/ClassroomControllerTest.php`                  | Classroom CRUD, student assignment, homeroom assignment                    | 379 lines, no tests.                                                       |
| **P1**   | `tests/Feature/AttendanceControllerTest.php`                 | Attendance CRUD, bulk attendance entry                                     | 125 lines, no tests.                                                       |
| **P2**   | `tests/Feature/PositionAssignmentTest.php`                   | Position assignment CRUD with DB transactions                              | 441 lines, no tests.                                                       |
| **P2**   | `tests/Feature/TeachingAssignmentTest.php`                   | Teaching assignment CRUD                                                   | 334 lines, no tests.                                                       |
| **P2**   | `tests/Feature/ScheduleGridControllerTest.php`               | Schedule grid view, conflict detection, store/update/delete                | 480 lines, no tests.                                                       |
| **P2**   | `tests/Feature/SettingsControllerTest.php`                   | Settings management, late-fee configuration                                | 107 lines, no tests.                                                       |
| **P2**   | `tests/Feature/TimeSlotControllerTest.php`                   | Time slot CRUD with conflict detection                                     | 247 lines, no tests.                                                       |
| **P2**   | `tests/Feature/PSBNotificationTest.php`                      | WhatsApp/SMS/email notification sending                                    | 267 lines, no tests.                                                       |
| **P3**   | `tests/Feature/TreasurerDashboardTest.php`                   | Treasurer dashboard data accuracy                                          | 119 lines, no tests.                                                       |
| **P3**   | `tests/Feature/TreasurerReportTest.php`                      | Report generation and export                                               | 184 lines, no tests.                                                       |
| **P3**   | `tests/Unit/Services/GradeServiceTest.php`                   | Grade calculation logic                                                    | Service layer unit testing.                                                |
| **P3**   | `tests/Unit/Services/WhatsAppServiceTest.php`                | WhatsApp API integration                                                   | Service layer unit testing.                                                |
| **P3**   | `tests/Unit/Services/NotificationServiceTest.php`            | Notification dispatch logic                                                | Service layer unit testing.                                                |
| **P3**   | `tests/Unit/Services/AchievementFeeExemptionServiceTest.php` | Fee exemption calculation                                                  | Service layer unit testing.                                                |
| **P3**   | `tests/Unit/Models/StudentBillTest.php`                      | Bill calculation methods, scopes, accessors                                | Model unit testing.                                                        |
| **P3**   | `tests/Unit/Policies/EmployeePolicyTest.php`                 | Employee authorization                                                     | Existing policy, no test.                                                  |

**Summary:** ~22 controllers and 4 service classes have zero test coverage. The financial module (payments, bills) is the highest-risk gap.

---

## Section 2: Controllers Needing Form Requests

Only **2 controllers** use Form Request classes (`StudentController`, `GradeController`). All others use inline `$request->validate()`.

### 2.2 Controllers That Should Extract Form Requests

| Controller                            | File                                                          | validate() Calls                     | Proposed Form Requests                                                                     |
| ------------------------------------- | ------------------------------------------------------------- | ------------------------------------ | ------------------------------------------------------------------------------------------ |
| **ApplicantController**               | `app/Http/Controllers/Admin/ApplicantController.php`          | 0 inline (validation in sub-methods) | `VerifyApplicantPaymentRequest`, `SaveApplicantScoreRequest`, `AcceptApplicantRequest`     |
| **PaymentController (Admin)**         | `app/Http/Controllers/Admin/PaymentController.php`            | L97, L147, L225, L378                | `StorePaymentRequest`, `UpdatePaymentRequest`, `BulkPaymentRequest`, `BatchPaymentRequest` |
| **PaymentController (Treasurer)**     | `app/Http/Controllers/Treasurer/PaymentController.php`        | L86, L213, L305                      | Reuse Admin Form Requests                                                                  |
| **StudentBillController (Admin)**     | `app/Http/Controllers/Admin/StudentBillController.php`        | L199, L238, L296, L455               | `StoreBillRequest`, `UpdateBillRequest`, `BulkBillRequest`                                 |
| **StudentBillController (Treasurer)** | `app/Http/Controllers/Treasurer/StudentBillController.php`    | L184, L224, L426                     | Reuse Admin Form Requests                                                                  |
| **ScheduleGridController**            | `app/Http/Controllers/Admin/ScheduleGridController.php`       | L135, L270, L311                     | `StoreScheduleGridRequest`, `UpdateScheduleGridRequest`                                    |
| **ScheduleController**                | `app/Http/Controllers/Admin/ScheduleController.php`           | L129, L208                           | `StoreScheduleRequest`, `UpdateScheduleRequest`                                            |
| **TeacherController**                 | `app/Http/Controllers/Admin/TeacherController.php`            | L66, L178                            | `StoreTeacherRequest`, `UpdateTeacherRequest`                                              |
| **EmployeeController**                | `app/Http/Controllers/Admin/EmployeeController.php`           | L73, L141                            | `StoreEmployeeRequest`, `UpdateEmployeeRequest`                                            |
| **ClassroomController**               | `app/Http/Controllers/Admin/ClassroomController.php`          | L186, L247, L334                     | `StoreClassroomRequest`, `UpdateClassroomRequest`, `AssignStudentsRequest`                 |
| **PositionAssignmentController**      | `app/Http/Controllers/Admin/PositionAssignmentController.php` | L158, L311, L400, L425               | `StorePositionAssignmentRequest`, `UpdatePositionAssignmentRequest`                        |
| **TeachingAssignmentController**      | `app/Http/Controllers/Admin/TeachingAssignmentController.php` | L183, L309                           | `StoreTeachingAssignmentRequest`                                                           |
| **TimeSlotController**                | `app/Http/Controllers/Admin/TimeSlotController.php`           | L97, L184                            | `StoreTimeSlotRequest`, `UpdateTimeSlotRequest`                                            |
| **PositionController**                | `app/Http/Controllers/Admin/PositionController.php`           | L91, L148, L206                      | `StorePositionRequest`, `UpdatePositionRequest`                                            |
| **ReportCardController**              | `app/Http/Controllers/Admin/ReportCardController.php`         | L160, L392, L396                     | `GenerateReportCardRequest`, `UpdateReportCardRequest`                                     |
| **PSBNotificationController**         | `app/Http/Controllers/Admin/PSBNotificationController.php`    | L50, L76, L126, L151, L264           | `SendNotificationRequest`, `BulkNotificationRequest`                                       |
| **PublicRegistrationController**      | `app/Http/Controllers/PublicRegistrationController.php`       | L78, L259, L278                      | `PublicRegistrationRequest`, `UploadDocumentRequest`                                       |
| **AuthController**                    | `app/Http/Controllers/Auth/AuthController.php`                | L34, L96, L161                       | `LoginRequest`, `RegisterRequest`, `ForgotPasswordRequest`                                 |
| **NilaiController (Guru)**            | `app/Http/Controllers/Guru/NilaiController.php`               | L213, L330                           | `StoreBulkGradeRequest`, `UpdateGradeRequest`                                              |
| **LmsCourseController**               | `app/Http/Controllers/Guru/LmsCourseController.php`           | L138, L306, L350, L376, L415         | `StoreCourseRequest`, `UpdateCourseRequest`, `StoreModuleRequest`, `StoreMaterialRequest`  |
| **LmsQuizController**                 | `app/Http/Controllers/Guru/LmsQuizController.php`             | L49, L109, L163, L203                | `StoreQuizRequest`, `StoreQuizQuestionRequest`                                             |
| **LmsAssignmentController**           | `app/Http/Controllers/Guru/LmsAssignmentController.php`       | L49, L112, L163                      | `StoreAssignmentRequest`, `GradeSubmissionRequest`                                         |
| **LmsController (Siswa)**             | `app/Http/Controllers/Siswa/LmsController.php`                | L114, L147, L382, L433               | `SubmitAssignmentRequest`, `SubmitQuizRequest`                                             |

**Total: ~100+ inline validation blocks** that should be extracted into ~50 Form Request classes.

---

## Section 3: Large Controllers Needing Service/Repository Extraction

### 3.1 Controllers >200 Lines (Candidates for Refactoring)

| #   | Controller                               | Lines   | Existing Service?                     | Recommended Extraction                                                                                                   |
| --- | ---------------------------------------- | ------- | ------------------------------------- | ------------------------------------------------------------------------------------------------------------------------ |
| 1   | `Admin/ApplicantController.php`          | **653** | Uses `AchievementFeeExemptionService` | Create `ApplicantService` for: verify payment, verify/reject prestasi, verify document, score input, accept/reject logic |
| 2   | `Admin/ScheduleGridController.php`       | **480** | None                                  | Create `ScheduleService` for: schedule grid building, conflict detection, modal data preparation                         |
| 3   | `Admin/StudentBillController.php`        | **476** | None                                  | Create `BillingService` for: bulk bill creation, late fee calculation, bill status management                            |
| 4   | `Admin/ReportCardController.php`         | **469** | Uses `GradeService`                   | Create `ReportCardService` for: report card generation, finalization, PDF preparation                                    |
| 5   | `Admin/PositionAssignmentController.php` | **441** | None                                  | Create `PositionAssignmentService` for: position CRUD with complex DB transactions                                       |
| 6   | `Treasurer/StudentBillController.php`    | **410** | None                                  | **Shares ~70% logic with Admin variant.** Extract shared logic into `BillingService`                                     |
| 7   | `Guru/LmsCourseController.php`           | **402** | None                                  | Create `LmsCourseService` for: module/material management, file handling                                                 |
| 8   | `Admin/PaymentController.php`            | **391** | None                                  | Create `PaymentService` for: receipt generation, bill status updates, batch payments                                     |
| 9   | `Siswa/LmsController.php`                | **382** | None                                  | Create `LmsStudentService` for: quiz attempts, assignment submission, progress tracking                                  |
| 10  | `Admin/ClassroomController.php`          | **379** | None                                  | Create `ClassroomService` for: student assignment, homeroom assignment logic                                             |
| 11  | `PublicRegistrationController.php`       | **373** | None                                  | Create `RegistrationService` for: applicant creation, document upload, status check                                      |
| 12  | `Treasurer/PaymentController.php`        | **358** | None                                  | **Shares ~80% logic with Admin variant.** Extract shared logic into `PaymentService`                                     |
| 13  | `Admin/TeachingAssignmentController.php` | **334** | None                                  | Create `TeachingAssignmentService`                                                                                       |
| 14  | `Guru/NilaiController.php`               | **329** | None                                  | Should use existing `GradeService` more; create `NilaiService` for bulk operations                                       |
| 15  | `Guru/DashboardController.php`           | **285** | None                                  | Create `GuruDashboardService` for data aggregation                                                                       |
| 16  | `Admin/PSBNotificationController.php`    | **267** | Uses `NotificationService`?           | Delegate more to `NotificationService`                                                                                   |
| 17  | `Admin/TeacherController.php`            | **254** | None                                  | Create `TeacherService` for: user creation, photo handling                                                               |
| 18  | `Admin/ScheduleController.php`           | **250** | None                                  | Merge/reuse `ScheduleService` from ScheduleGridController                                                                |
| 19  | `Admin/TimeSlotController.php`           | **247** | None                                  | Create `TimeSlotService` for: conflict detection logic                                                                   |
| 20  | `Siswa/DashboardController.php`          | **237** | None                                  | Create `SiswaDashboardService` for data aggregation                                                                      |
| 21  | `Admin/PositionController.php`           | **233** | None                                  | Within acceptable range, but bulk update logic could move to service                                                     |
| 22  | `Guru/LmsQuizController.php`             | **223** | None                                  | Create `LmsQuizService`                                                                                                  |
| 23  | `Auth/AuthController.php`                | **220** | None                                  | Create `AuthService` for: login, registration, password reset logic                                                      |
| 24  | `PSBTestController.php`                  | **217** | None                                  | OK for test/simulation controller                                                                                        |
| 25  | `Guru/LmsDiscussionController.php`       | **206** | None                                  | Create `LmsDiscussionService`                                                                                            |

### 3.2 Code Duplication: Admin ↔ Treasurer

The most significant code smell is **near-identical controller pairs:**

| Admin Controller                              | Treasurer Controller                              | Duplicated Logic                                   |
| --------------------------------------------- | ------------------------------------------------- | -------------------------------------------------- |
| `Admin/PaymentController.php` (391 lines)     | `Treasurer/PaymentController.php` (358 lines)     | Payment CRUD, receipt generation, batch processing |
| `Admin/StudentBillController.php` (476 lines) | `Treasurer/StudentBillController.php` (410 lines) | Bill CRUD, bulk creation, export                   |

**Recommendation:** Extract shared logic into `PaymentService` and `BillingService`, then have both controllers use the same services with different authorization scopes.

---

## Section 4: Model Improvements Needed

### 4.1 Models Missing `$casts`

| Model         | File                                    | Issue                                                                                                              |
| ------------- | --------------------------------------- | ------------------------------------------------------------------------------------------------------------------ |
| `ParentModel` | `app/Models/ParentModel.php` (46 lines) | Has no `$casts`. Should cast `relation_type` if enum.                                                              |
| `Setting`     | `app/Models/Setting.php` (45 lines)     | Has no `$casts`. Though it manually casts in `getValue()`, the `type` and `group` fields could benefit from casts. |

### 4.2 Models Missing `HasFactory`

| Model                         | Has Relationships? | Action                                                            |
| ----------------------------- | ------------------ | ----------------------------------------------------------------- |
| `AchievementFeeExemptionRule` | Yes (3 rels)       | Add `HasFactory` + create factory                                 |
| `AdmissionDiscount`           | Yes (2 rels)       | Add `HasFactory` + create factory                                 |
| `AdmissionFee`                | Yes (2 rels)       | Add `HasFactory` + create factory                                 |
| `AdmissionTest`               | Yes (2 rels)       | Add `HasFactory` + create factory                                 |
| `Applicant`                   | Yes (15 rels)      | **High priority** – Add `HasFactory` + create factory for testing |
| `ApplicantAchievement`        | Yes (1 rel)        | Add `HasFactory` + create factory                                 |
| `ApplicantDiscount`           | Yes (3 rels)       | Add `HasFactory` + create factory                                 |
| `ApplicantDocument`           | Yes (2 rels)       | Add `HasFactory` + create factory                                 |
| `ApplicantFeeExemption`       | Yes (4 rels)       | Add `HasFactory` + create factory                                 |
| `ApplicantPayment`            | Yes (3 rels)       | Add `HasFactory` + create factory                                 |
| `ApplicantTestScore`          | Yes (2 rels)       | Add `HasFactory` + create factory                                 |
| `Parent.php`                  | Stub file          | Remove or convert to alias/facade only                            |
| `RegistrationWave`            | Yes (3 rels)       | Add `HasFactory` + create factory                                 |
| `Setting`                     | No relationships   | Add `HasFactory` for seeding tests                                |
| `TimeSlot`                    | Yes (2 rels)       | Add `HasFactory` + create factory                                 |

### 4.3 Missing Factories (for testability)

Existing factories: `AcademicYear`, `Classroom`, `Employee`, `Grade`, `Major`, `School`, `Semester`, `Student`, `Subject`, `Teacher`, `User` (11 total).

**Missing factories needed for testing:** `Applicant`, `ApplicantPayment`, `Attendance`, `Payment`, `StudentBill`, `PaymentType`, `Position`, `Schedule`, `TimeSlot`, `ReportCard`, `LmsCourse`, `LmsAssignment`, `LmsQuiz`, `ParentModel` (14+ needed).

### 4.4 Dead Model File

- `app/Models/Parent.php` (3 lines) – Contains only a comment saying "Legacy file placeholder." Should be removed or documented clearly.

---

## Section 5: Code Cleanup Items

### 5.1 Unused Imports

| File                                                     | Unused Import        | Line          |
| -------------------------------------------------------- | -------------------- | ------------- |
| `app/Http/Controllers/Admin/AcademicYearController.php`  | `School`             | use statement |
| `app/Http/Controllers/Admin/EmployeeController.php`      | `Hash`               | use statement |
| `app/Http/Controllers/Admin/GradeController.php`         | `AuthorizesRequests` | use statement |
| `app/Http/Controllers/Admin/StudentController.php`       | `AuthorizesRequests` | use statement |
| `app/Http/Controllers/Admin/TimeSlotController.php`      | `DB`                 | use statement |
| `app/Http/Controllers/Admin/UserController.php`          | `AuthorizesRequests` | use statement |
| `app/Http/Controllers/Guru/DashboardController.php`      | `StudentClass`       | use statement |
| `app/Http/Controllers/Siswa/LmsController.php`           | `LmsDiscussionReply` | use statement |
| `app/Http/Controllers/Treasurer/DashboardController.php` | `Request`            | use statement |
| `app/Http/Controllers/PSBTestController.php`             | `AcademicYear`       | use statement |
| `app/Http/Controllers/PublicRegistrationController.php`  | `Storage`            | use statement |

### 5.2 Inconsistent DB Facade Usage

Some controllers use `\DB::` (global namespace) while others properly import `use Illuminate\Support\Facades\DB`:

| File                                  | Lines                        | Issue                                          |
| ------------------------------------- | ---------------------------- | ---------------------------------------------- |
| `Admin/SubjectController.php`         | L73, L99, L101               | Uses `\DB::beginTransaction()` etc.            |
| `Admin/ClassroomController.php`       | L357, L369, L372, L390, L397 | Uses `\DB::table()`, `\DB::beginTransaction()` |
| `Admin/SchoolController.php`          | L149, L159                   | Uses `\DB::table()`                            |
| `Treasurer/StudentBillController.php` | L47, L106, L118, L128        | Uses `\DB::table()`                            |
| `Auth/AuthController.php`             | L173                         | Uses `\DB::table()`                            |

**Fix:** Add `use Illuminate\Support\Facades\DB;` to these files and replace `\DB::` with `DB::`.

### 5.3 Inline Fully-Qualified Class Names in Routes

In `routes/web.php`, the file uses `use App\Http\Controllers\Auth\AuthController` for the auth controller but all other controllers are referenced with full class paths like `App\Http\Controllers\Admin\DashboardController::class`. This works but is verbose. Not urgent but worth cleaning up with proper use statements.

### 5.4 Hardcoded School IDs

In `app/Http/Controllers/Admin/ApplicantController.php`, lines ~63-65:

```php
$smpCount = Cache::remember('psb_smp_count', 300, fn() => Applicant::where('school_id', 1)->count());
$smaCount = Cache::remember('psb_sma_count', 300, fn() => Applicant::where('school_id', 2)->count());
$smkCount = Cache::remember('psb_smk_count', 300, fn() => Applicant::where('school_id', 3)->count());
```

Hardcoded `school_id` values (1, 2, 3) should be replaced with dynamic lookups or constants.

### 5.5 Legacy/Stub File

- `app/Models/Parent.php` – 3-line placeholder file. Should be deleted.

---

## Section 6: Error Handling Improvements

### 6.1 Controllers Without Any try-catch (27 controllers)

These controllers have **no error handling** – database failures, missing records, or file operations will produce unhandled 500 errors:

**High Priority (>200 lines, data-mutating operations):**

| Controller                         | Lines | Risky Operations                                                  |
| ---------------------------------- | ----- | ----------------------------------------------------------------- |
| `Admin/ScheduleGridController.php` | 480   | Schedule CRUD, cache clearing, export                             |
| `Admin/ScheduleController.php`     | 250   | Schedule create/update with complex relations                     |
| `Admin/TeacherController.php`      | 254   | User+teacher creation, photo upload, `Storage::delete`            |
| `Admin/EmployeeController.php`     | 196   | User+employee creation, photo upload, `Storage::delete`           |
| `Admin/TimeSlotController.php`     | 247   | Timeslot CRUD with conflict detection                             |
| `Admin/PositionController.php`     | 233   | Position CRUD with bulk allowance update (has 1 try on bulk only) |
| `Admin/ParentController.php`       | 114   | Parent CRUD with user creation                                    |
| `Admin/UserController.php`         | 98    | User CRUD, password reset                                         |
| `Auth/AuthController.php`          | 220   | Login, registration, password reset – **critical auth flows**     |
| `Guru/LmsCourseController.php`     | 402   | File uploads, course/module/material creation                     |
| `Guru/NilaiController.php`         | 329   | Bulk grade entry – **data-critical**                              |
| `Guru/LmsQuizController.php`       | 223   | Quiz CRUD, question management                                    |
| `Guru/LmsDiscussionController.php` | 206   | Discussion CRUD                                                   |

**Medium Priority (read-heavy dashboards):**

| Controller                                | Lines | Risk Level                                 |
| ----------------------------------------- | ----- | ------------------------------------------ |
| `Admin/DashboardController.php`           | 116   | Dashboard queries can fail on missing data |
| `Admin/AcademicYearController.php`        | 94    | toggle active year should be wrapped       |
| `Admin/MajorController.php`               | 113   | Simple CRUD but still needs wrapping       |
| `Admin/SchoolController.php`              | 180   | School CRUD + `\DB::table` operations      |
| `Admin/SemesterController.php`            | 80    | Simple CRUD                                |
| `Admin/KonsentrasiKeahlianController.php` | 76    | Simple CRUD                                |
| `Admin/ProgramKeahlianController.php`     | 56    | Simple CRUD                                |
| `Admin/GradeWeightController.php`         | 84    | Grade weight update                        |
| `Admin/SettingsController.php`            | 107   | Settings update                            |
| `Guru/DashboardController.php`            | 285   | Data aggregation                           |
| `Siswa/DashboardController.php`           | 237   | Read-only queries                          |
| `OrangTua/DashboardController.php`        | 197   | Read-only queries                          |
| `Treasurer/DashboardController.php`       | 119   | Financial data queries                     |
| `Treasurer/ReportController.php`          | 184   | Report generation                          |
| `PSBTestController.php`                   | 217   | Test/simulation                            |

### 6.2 Controllers With Partial try-catch (Inconsistent)

These controllers use try-catch for **some** methods but not others:

| Controller                            | Methods WITH try-catch                                                                    | Methods WITHOUT try-catch                                |
| ------------------------------------- | ----------------------------------------------------------------------------------------- | -------------------------------------------------------- |
| `Admin/PaymentController.php`         | `batchStore`, `downloadReceipt`                                                           | `store` (L97), `bulkStore` (L225), `update`              |
| `Admin/StudentBillController.php`     | `bulkStore`, `bulkWaiveLateFee`                                                           | `store` (L199), `update` (L238)                          |
| `Admin/ApplicantController.php`       | `accept`, `reject`, `verifyPayment`, `verifyPrestasi`, `rejectPrestasi`, `verifyDocument` | `export`, `inputScore`, `saveScore`                      |
| `Admin/ReportCardController.php`      | `generate`                                                                                | `update`, `finalize`, `publish`, `print`                 |
| `Treasurer/PaymentController.php`     | `bulkStore`, `batchStore`                                                                 | `store`, `update`                                        |
| `Treasurer/StudentBillController.php` | `bulkStore`                                                                               | `store`, `update`                                        |
| `Admin/StudentController.php`         | `store`, `update`, `destroy`, `import`                                                    | (Good coverage)                                          |
| `Siswa/LmsController.php`             | `submitQuiz`                                                                              | `submitAssignment`, `storeDiscussion`, `replyDiscussion` |
| `Guru/LmsAssignmentController.php`    | `grade`                                                                                   | `store`, `update`, `destroy`                             |

### 6.3 Missing DB Transactions for Multi-Table Operations

These operations modify multiple tables but don't wrap in transactions:

| File                              | Method              | Operations                                       | Risk                                      |
| --------------------------------- | ------------------- | ------------------------------------------------ | ----------------------------------------- |
| `Admin/PaymentController.php`     | `store()` L97       | Creates Payment + updates StudentBill status     | Bill status could become inconsistent     |
| `Admin/PaymentController.php`     | `batchStore()` L147 | Creates Payment + updates multiple bills         | Multiple bills could be partially updated |
| `Admin/TeacherController.php`     | `store()` L66       | Creates User + Teacher                           | Orphaned user if teacher creation fails   |
| `Admin/EmployeeController.php`    | `store()` L73       | Creates User + Employee                          | Orphaned user if employee creation fails  |
| `Admin/ParentController.php`      | `store()` L48       | Creates User + Parent                            | Orphaned user if parent creation fails    |
| `Admin/SchoolController.php`      | ~L149               | Creates school + inserts into employee_positions | Inconsistent records                      |
| `Treasurer/PaymentController.php` | `store()` L86       | Creates Payment + updates bill                   | Same as admin variant                     |

---

## Summary: Prioritized Action Items

### Tier 1 – Critical (Do First)

1. **Add try-catch + DB transactions** to `AuthController`, `PaymentController.store()`, `TeacherController.store()`, `EmployeeController.store()`
2. **Create `PaymentService` and `BillingService`** to eliminate Admin↔Treasurer code duplication (~800 duplicated lines)
3. **Write tests for financial module** (`PaymentControllerTest`, `StudentBillControllerTest`) – highest business risk

### Tier 2 – High Value

4. **Extract Form Requests** for the top 10 controllers (start with Payment, StudentBill, Teacher, Employee, Classroom)
5. **Create factories** for `Applicant`, `Payment`, `StudentBill`, `PaymentType`, `Schedule`, `TimeSlot`
6. **Add try-catch** to all data-mutating controller methods (see Section 6.1 High Priority list)
7. **Remove unused imports** (11 files, quick cleanup)

### Tier 3 – Maintainability

8. **Refactor large controllers** into services (ApplicantController → ApplicantService, ReportCardController → ReportCardService)
9. **Standardize `\DB::` to `DB::`** with proper imports (5 files)
10. **Remove hardcoded school IDs** in ApplicantController
11. **Delete `app/Models/Parent.php`** (legacy stub)
12. **Add `HasFactory` to 15 models** without it
13. **Create remaining test suites** for controllers (Tier P1-P3)
