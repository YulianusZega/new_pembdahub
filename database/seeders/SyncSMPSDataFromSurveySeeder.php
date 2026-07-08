<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Employee;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Sync SMPS Pembda 2 data from survey_kepuasan_guru database.
 * 
 * Data source: survey_kepuasan_guru.sql
 * Target school: SMPS Pembda 2 Gunungsitoli (school_id = 1)
 * 
 * This seeder will:
 * 1. Sync 11 subjects (mata pelajaran)
 * 2. Sync 16 teachers with user accounts
 * 3. Sync 10 SMP classrooms (skip SMA test class)
 * 4. Sync 294 students with user accounts
 * 5. Assign students to classrooms (student_classes)
 * 6. Set teacher competencies (subject_teacher)
 */
class SyncSMPSDataFromSurveySeeder extends Seeder
{
    private int $schoolId = 1;
    private string $defaultTeacherPassword = 'Guru@Pembda2026!';
    private string $defaultStudentPassword = 'siswasmpp2';

    public function run(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            $this->command->error('❌ Tahun akademik aktif tidak ditemukan!');
            return;
        }

        $this->command->info("📅 Tahun Akademik: {$activeYear->year} (ID: {$activeYear->id})");
        $this->command->info("🏫 School ID: {$this->schoolId}");
        $this->command->newLine();

        DB::beginTransaction();
        try {
            // Disable FK checks and strict mode to allow clean delete/recreate
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::statement("SET SESSION sql_mode=''");

            $subjectMap = $this->syncSubjects();
            $teacherMap = $this->syncTeachers();
            $classroomMap = $this->syncClassrooms($activeYear->id);
            $this->syncStudents($classroomMap, $activeYear->id);
            $this->syncTeacherCompetencies($teacherMap, $subjectMap);

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();
            $this->command->newLine();
            $this->command->info('✅ Semua data berhasil di-sync!');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::rollBack();
            $errorMsg = "Error: " . $e->getMessage() . "\nFile: " . $e->getFile() . ":" . $e->getLine();
            file_put_contents(base_path('sync_error.log'), $errorMsg);
            $this->command->error("❌ " . $errorMsg);
        }
    }

    /**
     * Step 1: Sync subjects (mata pelajaran)
     * Returns mapping: survey_mapel_id => pembdahub_subject_id
     */
    private function syncSubjects(): array
    {
        $this->command->info('📚 Syncing mata pelajaran...');

        // Survey mata_pelajaran data (SQL + Excel extras)
        $surveySubjects = [
            1  => 'Agama Katolik',
            2  => 'Bahasa Indonesia',
            3  => 'Bahasa Inggris',
            4  => 'Ilmu Pengetahuan Alam',
            5  => 'Ilmu Pengetahuan Sosial',
            6  => 'Matematika',
            7  => 'PJOK',
            8  => 'PKn',
            9  => 'Pend. Agama Islam',
            10 => 'Pend. Agama Kristen',
            11 => 'TIK',
            12 => 'Seni Budaya',
            13 => 'BK',
            14 => 'Molok',
            15 => 'Prakarya',
        ];

        // Clean up invalid subjects for this school (those with no name or '-')
        Subject::where('school_id', $this->schoolId)
            ->where(function($q) {
                $q->whereNull('name')->orWhere('name', '-')->orWhere('name', '');
            })->delete();

        $subjectMap = [];

        foreach ($surveySubjects as $surveyId => $name) {
            // Try to find existing subject by name (case-insensitive match)
            $existing = Subject::where('school_id', $this->schoolId)
                ->where(function ($q) use ($name) {
                    $q->where('subject_name', $name)
                      ->orWhere('name', $name)
                      ->orWhere('subject_name', 'LIKE', '%' . $name . '%');
                })
                ->first();

            if ($existing) {
                $subjectMap[$surveyId] = $existing->id;
            } else {
                // Create new subject
                $subject = Subject::create([
                    'school_id' => $this->schoolId,
                    'subject_name' => $name,
                    'name' => $name,
                    'is_active' => true,
                ]);
                $subjectMap[$surveyId] = $subject->id;
                $this->command->warn("  + Mapel baru: {$name} (ID: {$subject->id})");
            }
        }

        $this->command->info("  ✓ {$this->count($subjectMap)} mapel di-mapping");
        return $subjectMap;
    }

    /**
     * Step 2: Sync teachers (guru)
     * Returns mapping: survey_guru_id => pembdahub_teacher_id
     */
    private function syncTeachers(): array
    {
        $this->command->info('👨‍🏫 Syncing guru...');

        // Survey guru data: [survey_id => [nip, nama_lengkap]]
        $surveyTeachers = [
            1  => ['1451768669130080', 'YONATA TELAUMBANUA, S.PD'],
            2  => ['0056769670130083', 'DEDI PUTRA TELAUMBANUA, S.PD'],
            3  => ['1534746650300020', 'MARSELINA MASARIA NDRURU, S.AG'],
            4  => ['0549745647300052', 'DRA. KRISTIANI ZEBUA'],
            5  => ['1834757663200000', 'BEATUS NDRURU, S.PD'],
            6  => ['7247764666130150', 'ELIAMAN ZAI, S.PD'],
            7  => ['GTK001', 'NURIATI ZEGA, SH'],
            8  => ['4042772673130070', 'DEWI JULI SULASTRI ZEGA, S.E'],
            9  => ['2447764665230240', 'YARNIWATI SARUMAHA, S.PD.K'],
            10 => ['7647767668130260', 'SOLIDARMAN JAYA MENDROFA, S.PD'],
            11 => ['0447774675230053', 'CLARA NOVITA SABRINA, S.PD'],
            12 => ['GTK002', 'ERWIN JHOSEP CLARK  ZEBUA, A.MD.T'],
            13 => ['9735775676230130', 'BERTHA TELAUMBANUA, S.PD'],
            14 => ['2734777678230050', 'SRI RAHAYU TANJUNG, S.PD'],
            15 => ['5762777678230050', 'HENY APRILIA TELAUMBANUA, S.PD'],
            16 => ['GTK003', 'NIGUENTS FALDES HULU, S.PD'],
        ];

        $teacherMap = [];

        // Delete ALL existing SMP teachers, employees, and their user accounts
        $existingTeachers = Teacher::where('school_id', $this->schoolId)->get();
        $deletedCount = $existingTeachers->count();
        foreach ($existingTeachers as $t) {
            // Delete employee record
            if ($t->employee_id) {
                Employee::where('id', $t->employee_id)->delete();
            }
            $t->delete();
            // Delete user account
            if ($t->user_id) {
                User::where('id', $t->user_id)->delete();
            }
        }

        // Also clean up orphaned employees for this school (guru type)
        Employee::where('school_id', $this->schoolId)
            ->where('employee_type', 'guru')
            ->delete();

        // Create fresh teachers from survey
        foreach ($surveyTeachers as $surveyId => [$nip, $name]) {
            // Create user account
            $user = User::create([
                'name' => $name,
                'username' => $nip,
                'email' => strtolower(str_replace(' ', '', $nip)) . '@guru.pembdahub.sch.id',
                'password' => Hash::make($this->defaultTeacherPassword),
                'role' => 'guru',
                'school_id' => $this->schoolId,
                'is_active' => true,
                'must_change_password' => true,
            ]);

            // Create employee record
            $employee = Employee::create([
                'school_id' => $this->schoolId,
                'user_id' => $user->id,
                'employee_code' => $nip,
                'full_name' => $name,
                'employee_type' => 'guru',
                'employment_status' => 'yayasan',
                'tmt_date' => '2024-07-01',
                'is_active' => true,
            ]);

            // Create teacher record linked to employee
            $teacher = Teacher::create([
                'employee_id' => $employee->id,
                'user_id' => $user->id,
                'school_id' => $this->schoolId,
                'teacher_code' => $nip,
                'full_name' => $name,
                'is_active' => true,
            ]);

            $teacherMap[$surveyId] = $teacher->id;
        }

        $this->command->info("  ✓ {$deletedCount} guru lama dihapus, " . count($teacherMap) . " guru baru dibuat");
        return $teacherMap;
    }

    /**
     * Step 3: Sync classrooms (kelas)
     * Returns mapping: survey_kelas_id => pembdahub_classroom_id
     */
    private function syncClassrooms(int $academicYearId): array
    {
        $this->command->info('🏫 Syncing kelas...');

        // Survey kelas data (SMP only, skip id=11 X-Wahidin SMA)
        $surveyClasses = [
            1  => ['IX-Albert Einstein', 9],
            2  => ['IX-Aristoteles', 9],
            3  => ['IX-Pythagoras', 9],
            4  => ['VII-Alessandro Volta', 7],
            5  => ['VII-Archimedes', 7],
            6  => ['VII-Blaise Pascal', 7],
            7  => ['VII-Gregor Mendel', 7],
            8  => ['VIII-Alexsander Graham Bell', 8],
            9  => ['VIII-Isaac Newton', 8],
            10 => ['VIII-Thomas Alva Edison', 8],
        ];

        // Delete existing classrooms for this school
        $oldCount = Classroom::where('school_id', $this->schoolId)->count();
        
        // Remove student_classes associations first
        DB::table('student_classes')
            ->whereIn('classroom_id', Classroom::where('school_id', $this->schoolId)->pluck('id'))
            ->delete();
        
        Classroom::where('school_id', $this->schoolId)->delete();

        $classroomMap = [];

        foreach ($surveyClasses as $surveyId => [$name, $gradeLevel]) {
            $classCode = 'SMP-' . str_replace([' ', '-'], ['', '-'], $name);
            $classroom = Classroom::create([
                'school_id' => $this->schoolId,
                'academic_year_id' => $academicYearId,
                'class_code' => $classCode,
                'class_name' => $name,
                'grade_level' => $gradeLevel,
                'capacity' => 40,
                'is_active' => true,
            ]);

            $classroomMap[$surveyId] = $classroom->id;
        }

        $this->command->info("  ✓ {$oldCount} kelas lama dihapus, " . count($classroomMap) . " kelas baru dibuat");
        return $classroomMap;
    }

    /**
     * Step 4: Sync students (siswa)
     * Also assigns students to classrooms via student_classes pivot.
     */
    private function syncStudents(array $classroomMap, int $academicYearId): void
    {
        $this->command->info('👩‍🎓 Syncing siswa...');

        // Survey siswa data: [nis, nama_lengkap, survey_kelas_id]
        // Extracted from survey_kepuasan_guru.sql siswa table (SMP only, skip id=295 SMA test)
        $surveyStudents = [
            // Kelas VII-Alessandro Volta (survey kelas_id=4)
            ['6891', 'AGNIESKA PRISCILLA HAREFA', 4],
            ['6893', 'ALDO FERMANTIAN PUTRA HALAWA', 4],
            ['6895', 'ANDREAS WANDITO GEA', 4],
            ['6899', 'ARLANDO TELAUMBANUA', 4],
            ['6910', 'CHYNTIA EARLENE DAFIN HAREFA', 4],
            ['6922', 'DIEGO RAFAEL MENDROFA', 4],
            ['6927', 'ERNESTINE JULIAN MENDROFA', 4],
            ['6940', 'GRACELLA SURATRI LAOWO', 4],
            ['6944', 'HARVEY SENDRORO HULU', 4],
            ['6946', 'HENDY KRISTIAN LAWOLO', 4],
            ['6947', 'HIZKIA SEBASTIAN ZEBUA', 4],
            ['6955', 'IRVAN GIAWA', 4],
            ['6958', 'JEFRI KRISTIAN LAIA', 4],
            ['6964', 'JONATHAN EBENEZER MENDROFA', 4],
            ['6966', 'JULFAN TELAUMBANUA', 4],
            ['6967', 'JULIANUS HULU', 4],
            ['6968', 'JUSTIN LI LASE', 4],
            ['6969', 'KALEB SEPTIAN JECONIA', 4],
            ['6970', 'KEISHA FLORENCIA NDRAHA', 4],
            ['6971', 'KEZIA FOUR RETRI LAHAGU', 4],
            ['6972', 'LOVIS NEISA HURA', 4],
            ['6973', 'MAGDALENA TELAUMBANUA', 4],
            ['6974', 'MARCEL SAPUTRA TELAUMBANUA', 4],
            ['6975', 'MARCELL ANGANDROWA LASE', 4],
            ['6976', 'MARLON ANDREAS ZEGA', 4],
            ['6981', 'MYCHAELA PRIANI MENDROFA', 4],
            ['6983', 'NAOMI SONDRARA TELAUMBANUA', 4],
            ['6986', 'ONIN PASKAH DAELI', 4],
            ['6996', 'RAISA EVELYN LAOWO', 4],
            ['7009', 'TRIAS ANDREW MENDROFA', 4],
            ['7014', 'YARNI HIA', 4],
            ['7017', 'YUSUF RONALD SIUS HULU', 4],

            // Kelas VII-Archimedes (survey kelas_id=5)
            ['6897', 'ANGELLITA BAWAMENEWI', 5],
            ['6901', 'AUREL TRI ANGGELICA MENDROFA', 5],
            ['6909', 'CHRISTA JOVITA AYU GEA', 5],
            ['6915', 'DARREL BRYAN\'SO ELFEGE HALAWA', 5],
            ['6920', 'DEVAN TIMOTHY HAREFA', 5],
            ['6924', 'DOMINIC DEANGELO ARVAND ZALUKHU', 5],
            ['6926', 'ELLA SRI WAHYUNI ZEBUA', 5],
            ['6931', 'FIRMAN JAYA ZALUKHU', 5],
            ['6933', 'FRANSISKA MARCELLINA APRILIAN ZEBUA', 5],
            ['6939', 'GRACE PUTRI DWI CLARA HALAWA', 5],
            ['6941', 'GRACIA SHREEN STEVANYA MANAO', 5],
            ['6943', 'HANIEL ZENDRATO', 5],
            ['6945', 'HELEN FAGRI STEFANIE LAIA', 5],
            ['6948', 'HIZKIA ZIOLOISIUS ARJUNA SIMANJUNTAK', 5],
            ['6950', 'IMMANUEL STEFVEN SIHOMBING', 5],
            ['6951', 'INTALIAN BU\'ULOLO', 5],
            ['6953', 'IREN NOVITA LYNDA LASE', 5],
            ['6954', 'IRFAN SEJUMAN HAREFA', 5],
            ['6957', 'JEFRI ARON TEL', 5],
            ['6962', 'JOEL DATAFATI TELAUMBANUA', 5],
            ['6963', 'JOHAN PRASETTYO ZEBUA', 5],
            ['6980', 'MOSES IRCHAN HAREFA', 5],
            ['6990', 'PUTRA JUNI ZAI', 5],
            ['6993', 'RAFAEL SEBASTIAN DAELI', 5],
            ['6997', 'RAYMOND RIFALDO ZEBUA', 5],
            ['7001', 'SAMUEL ANDRIAN ZEBUA', 5],
            ['7006', 'TASYA INTAN NURANI GEA', 5],
            ['7007', 'THEOFILUS KARUNIA ZAI', 5],
            ['7010', 'VANSAMUEL TELAUMBANUA', 5],
            ['7011', 'WANNIEL PUTRA DAMAI LASE', 5],
            ['7013', 'YAKUB EMURI HALAWA', 5],
            ['7015', 'YOHANES MARTIN GEA', 5],
            ['7016', 'YUDIKA FERNANDO WARUWU', 5],

            // Kelas VII-Blaise Pascal (survey kelas_id=6)
            ['6888', 'ADELIA ANGELA LAOLI', 6],
            ['6890', 'AERILYN BELVANIA GULO', 6],
            ['6892', 'ALDEN CHRISTIAN ZEBUA', 6],
            ['6894', 'ALFADER LI EDONADO LASE', 6],
            ['6896', 'ANGEL ELPRIDA TELAUMBANUA', 6],
            ['6900', 'ARYAN PUTRA LAFAU', 6],
            ['6904', 'BENEDICTUS MARVEL TELAUMBANUA', 6],
            ['6906', 'BRIAN SAVARDO MENDROFA', 6],
            ['6907', 'CALISTA FELCIA LASE', 6],
            ['6908', 'CELIA THALITA ZEBUA', 6],
            ['6912', 'CLEARY GILBERT MOTARO GEA', 6],
            ['6913', 'CLEVERIA ELENA LASE', 6],
            ['6914', 'CONSTAN SION WARUWU', 6],
            ['6916', 'DAUD OCTO LOI', 6],
            ['6918', 'DEBORA LISTAFIANY', 6],
            ['6919', 'DERMAWAN LAOLI', 6],
            ['6921', 'DHAVID PUTRA SIHOMBING', 6],
            ['6923', 'DIRGA WAHYUNIS GIAWA', 6],
            ['6925', 'DOMINIKUS DANDHI PRIMUS NDRAHA', 6],
            ['6928', 'ETGAR JOACHIM LASE', 6],
            ['6929', 'EVIANA FAOMASI HALAWA', 6],
            ['6930', 'FELICITOUS GUNAWAN GEA', 6],
            ['6932', 'FLOREN BELIEF FOREVER GEA', 6],
            ['6935', 'GERSON JUNIUS ZEBUA', 6],
            ['6937', 'GIMEL AUDRELYA SIMANJUNTAK', 6],
            ['6938', 'GODELVA HOLY BENEDICTA GEA', 6],
            ['6960', 'JHOSUA MAYSARO LAOLI', 6],
            ['6961', 'JOEL ASADEL TELAUMBANUA', 6],
            ['6977', 'MARTA SELVIYANTI ZEBUA', 6],
            ['6978', 'MARVEL KARUNIA LAIA', 6],
            ['6987', 'OZORA FRODINE GONTARI HULU', 6],
            ['6994', 'RAFFAEL TERUNA TRI PUTRA TELAUMBANUA', 6],
            ['7004', 'SOLAI MIRACLE LASE', 6],

            // Kelas VII-Gregor Mendel (survey kelas_id=7)
            ['6889', 'AERILYN BELLVANIA LASE', 7],
            ['6898', 'ANJELINA WARUWU', 7],
            ['6902', 'AWAL SARO TELAUMBANUA', 7],
            ['6903', 'AYRA KIRANA GUCI', 7],
            ['6905', 'BERKAT HALAWA', 7],
            ['6911', 'CLARISSA LAURETHA TELAUMBANUA', 7],
            ['6917', 'DAVIDEZRA GAVRIEL ZEBUA', 7],
            ['6934', 'GANDRIEL ALFARO MADUWU', 7],
            ['6936', 'GILBERT RONEYVAN ZALUKHU', 7],
            ['6942', 'HADRIYAN RAUF ZEGA', 7],
            ['6949', 'IDA YANTI TELAUMBANUA', 7],
            ['6952', 'INTAN PERMATASARI SARUMAHA', 7],
            ['6956', 'JAN YUSTIN SOLOMASI TELAUMBANUA', 7],
            ['6959', 'JESSICA FAERI NATHANIELA JAWANIS TELAUMBANUA', 7],
            ['6965', 'JORDY ALEXANDER MATHEW ZAI', 7],
            ['6979', 'MASA DERITA NDRURU', 7],
            ['6982', 'NADIN EL CHASTIN GULO', 7],
            ['6984', 'NATHANIELLA KEREN HAPUKH GULO', 7],
            ['6985', 'NAYARA ADELIA', 7],
            ['6988', 'PENERUS NDRURU', 7],
            ['6989', 'PUTRA FURKAN GULO', 7],
            ['6991', 'RAFAEL CALEB SARO ZEBUA', 7],
            ['6992', 'RAFAEL IGNATIUS DAELY', 7],
            ['6995', 'RAIH JORDY SOLALA ZEBUA', 7],
            ['6998', 'RESPON PUSPA HOLY TELAUMBANUA', 7],
            ['6999', 'RESTU IMAN LASE', 7],
            ['7000', 'RUTH CLARA NDRURU', 7],
            ['7002', 'SMARTVEL FERNANDO TELAUMBANUA', 7],
            ['7003', 'SOGINOTO PUTRA HAREFA', 7],
            ['7005', 'SURFAMAN TELAUMBANUA', 7],
            ['7008', 'TRI DELA MAHARANI MENDROFA', 7],
            ['7012', 'WILLIAM ARCARD ZEBUA', 7],
            ['7018', 'ZESKIA NAYLA DINDA HULU', 7],

            // Kelas VIII-Alexsander Graham Bell (survey kelas_id=8)
            ['6807', 'ADLY EDHARLAN HAREFA', 8],
            ['6809', 'ALDEN GEVAREL HAREFA', 8],
            ['6887', 'ALEXIS STEVEN RAFAEL SITEPU', 8],
            ['6815', 'ARDIN NDRURU', 8],
            ['6817', 'ARSENIO HASARA GEA', 8],
            ['6818', 'ARYAN DIANTARA TELAUMBANUA', 8],
            ['6825', 'CHRISTINA JESICA DEWI GEA', 8],
            ['6827', 'DEAN ISMAEL TELAUMBANUA', 8],
            ['6828', 'DEBORA SELA FATEMALUO', 8],
            ['6831', 'ELVA GRETA NATHANIA ZEBUA', 8],
            ['6832', 'ERMON SARA ADVENTUS GEA', 8],
            ['6834', 'FAREL WILMAN GEA', 8],
            ['6838', 'GAVRA SATRIA TELAUMBANUA', 8],
            ['6842', 'IVAN ANUGERAH LAIA', 8],
            ['6846', 'JUANG EZRA AMEF DESIRE NDRURU', 8],
            ['6849', 'KHARISMAN TEGUH LAOWO', 8],
            ['6851', 'LAI FUTI SEHAO HULU', 8],
            ['7020', 'MAGNALIA DEI TELAUMBANUA', 8],
            ['6856', 'MOSES NOTATEMA MENDROFA', 8],
            ['6857', 'NALVADI PERJUANGAN ZEBUA', 8],
            ['6860', 'NISCAHYA ZEBUA', 8],
            ['6861', 'OCTOFOUR LASONI GEA', 8],
            ['6871', 'SANTONI DACHI', 8],
            ['6874', 'SYLVIA CHRISTABEL MENDROFA', 8],
            ['6876', 'VALDANIEL HAREFA', 8],
            ['6878', 'YOSEPH JEFRINTO GULO', 8],

            // Kelas VIII-Isaac Newton (survey kelas_id=9)
            ['6808', 'AHMAD SYAHRUL RAMADHAN TANJUNG', 9],
            ['6811', 'ALVARO JORDAN NOTATEMA HALAWA', 9],
            ['6813', 'ANDI ELMAN JAYA TELAUMBANUA', 9],
            ['6816', 'ARDIYANTO SOFIAN ZAMASI', 9],
            ['6819', 'ASNIN FITRI GULO', 9],
            ['6820', 'AXEL GRACIO PRATAMA HULU', 9],
            ['6821', 'BELINDA ANGELIA BU\'ULOLO', 9],
            ['6835', 'FEBRIANO LASE', 9],
            ['6839', 'GISELLE RAISENDORO TELAUMBANUA', 9],
            ['6840', 'HANSEL AURELIO BENAYA LAHAGU', 9],
            ['6844', 'JOAB FREMONT UMBU HAREFA', 9],
            ['6845', 'JORDAN NOTATEMA MENDROFA', 9],
            ['6848', 'KEZIA NOVTHREE NIBELOIM HULU', 9],
            ['6852', 'LI GRIZELDA ABIGAIL ZEBUA', 9],
            ['6853', 'LIN DESTRY SAFYAH ZEGA', 9],
            ['6854', 'MARIO TEGUH BATE\'E', 9],
            ['6858', 'NELSI EVELYN OKTAVIANI HIA', 9],
            ['6863', 'PRAYQUEEN GLORIN NIFILI TELAUMBANUA', 9],
            ['6865', 'RADITYA TRY JUNIUS LAOLI', 9],
            ['6866', 'RAINHEART DARNEL LASE', 9],
            ['6868', 'REVIVAL EFATRA ZENDRATO', 9],
            ['6869', 'RIZKY NOTATEMA GEA', 9],
            ['6875', 'TEDUH EPRAFASH HULU', 9],
            ['6877', 'YONKI PRATAMA ZEBUA', 9],
            ['6880', 'ZAKY PRIADI ZIAWA', 9],

            // Kelas VIII-Thomas Alva Edison (survey kelas_id=10)
            ['6806', 'ABDI SABARIL HAREFA', 10],
            ['6810', 'ALMESTA ANGELIN TELAUMBANUA', 10],
            ['6812', 'ANDEAS WILLKY BATEE', 10],
            ['6814', 'ANUGRAH CHRISNATAN ZEBUA', 10],
            ['6823', 'BRYAN TELAUMBANUA', 10],
            ['7019', 'CANDRA EMANUEL LAOWO', 10],
            ['6824', 'CHARSA TEGUH SOTUHENI MENDROFA', 10],
            ['6826', 'DAMAI MARNELY TELAUMBANUA', 10],
            ['6829', 'DELBERT RAMAELI ZEGA', 10],
            ['6833', 'FAREL ADIPUTRA HULU', 10],
            ['6836', 'FLOWER TALENTA NDRURU', 10],
            ['6837', 'FRENGKI HIA', 10],
            ['6841', 'HERIYANTO HALAWA', 10],
            ['6843', 'JESSICA SARA ESTER NDRURU', 10],
            ['6847', 'JUNIARMAN NOTATEMA LAOLI', 10],
            ['6855', 'MARVEL PASKALIS ZEBUA', 10],
            ['6859', 'NICO REYNARD ALFARO GULO', 10],
            ['6862', 'OTNIEL GLENN SAOTA', 10],
            ['6864', 'PRETTY ERTIN HAGA ZEBUA', 10],
            ['6867', 'RENDY TRISWAN LASE', 10],
            ['6870', 'RYFANDI YOSUA GEA', 10],
            ['6872', 'SONA ANDHYKA PUTRA ZEBUA', 10],
            ['6873', 'STEFANY GLORIA TAFONA\'O', 10],
            ['6879', 'YOSSUA TEGAR PRATAMA MENDROFA', 10],

            // Kelas IX-Albert Einstein (survey kelas_id=1)
            ['6710', 'AGNES GRACIA FANNY LAOLI', 1],
            ['6713', 'ALBEST PEBRYAN HAREFA', 1],
            ['6714', 'ALVINO YANUR DWA VUTRA', 1],
            ['6716', 'ANDIKA CRISTIAN TELAUMBANUA', 1],
            ['6718', 'AURA BLESS NDRAHA', 1],
            ['6886', 'BEATRICE HAREFA', 1],
            ['6722', 'BLESS CHRISTIAN FANEMAZISOKHI HALAWA', 1],
            ['6724', 'CLARA NATASHA MENDROFA', 1],
            ['6725', 'CLARENCE JEROLIN SANATA HAREFA', 1],
            ['6730', 'DELANIA HOWU HOWU HULU', 1],
            ['6736', 'GIOVANI PUTRI LOMBU', 1],
            ['6738', 'GLEN HOWU HOWU ZEBUA', 1],
            ['6739', 'GLORY CLAUDIA DARA LASE', 1],
            ['6742', 'HELGA GRACELDA MENDROFA', 1],
            ['6743', 'HIERONIMUS RIKHARD LENTERA NDRAHA', 1],
            ['6744', 'HIZKIA DELASARO LAOLI', 1],
            ['6748', 'JASTIN TEODALI HAREFA', 1],
            ['6750', 'JESSYCA MARTALIA LAOWO', 1],
            ['6751', 'JOCELYN TRIVINA MENDROFA', 1],
            ['6757', 'LIVIA ANGELICA HALAWA', 1],
            ['6762', 'NATLIE DELLA FAOMASI TELAUMBANUA', 1],
            ['6764', 'ODELA CAHYANI ZEBUA', 1],
            ['6768', 'PANDU ANUGERAH GEA', 1],
            ['6770', 'PRICILLIA ENGELICA LAIA', 1],
            ['6771', 'RAFLY ALFAZRI', 1],
            ['6773', 'RIESNA YISKA SOLAI HAREFA', 1],
            ['6775', 'RIWUNI GEOVANNI ZEBUA', 1],
            ['6778', 'ROLAN BRIAN GUNAWAN GEA', 1],
            ['6785', 'TEGAR IMANUEL TELAUMBANUA', 1],
            ['6789', 'TWONO NOBUALA NDRURU', 1],
            ['6791', 'WILLIE EXCEL SAMUEL BATE\'E', 1],

            // Kelas IX-Aristoteles (survey kelas_id=2)
            ['6712', 'ALBERT SANDIKA HAREFA', 2],
            ['6717', 'ANGEL ZEVANIA ZEGA', 2],
            ['6721', 'BINSAR WILLIAM HAREFA', 2],
            ['6881', 'CHASTINE FEDORA GULO', 2],
            ['6732', 'ELEAZAR MANDELA GEA', 2],
            ['6734', 'EZRA SOLASO ZEBUA', 2],
            ['6737', 'GITA DEBORA HAREFA', 2],
            ['6740', 'GRACE DIAN PUTRI LASE', 2],
            ['6803', 'JASWADI GEA', 2],
            ['6749', 'JESSLYN VELDA DEVONA GEA', 2],
            ['6753', 'JUSTIN YASARO GEA', 2],
            ['6755', 'KEZIA NONIFILI HAREFA', 2],
            ['6759', 'MIKHAEL HOWU-HOWU HAREFA', 2],
            ['6761', 'MODESTUS ARNOV SEJAHTERA TELAUMBANUA', 2],
            ['6765', 'OIN FADEL ZEBUA', 2],
            ['6767', 'OPISERTIKA ZAI', 2],
            ['6772', 'REVALINA JUITA GEA', 2],
            ['6776', 'RIZKI SANGOFULO SARUMAHA', 2],
            ['6779', 'ROSMAWATI TELAUMBANUA', 2],
            ['6781', 'SHANDYANI LAFAU', 2],
            ['6783', 'SRAVASTI DHAMIKA ZEBUA', 2],
            ['6784', 'STEPHAN EMMANUEL SAMOLALA LASE', 2],
            ['6787', 'TUTRI JUNIAR LAFAU', 2],
            ['6788', 'TWONO NITEHE NDRURU', 2],
            ['6792', 'WULAN ANGGREYANI SIAGIAN', 2],
            ['6793', 'YARIDA WARUWU', 2],
            ['6795', 'YARNI KASIH HIA', 2],

            // Kelas IX-Pythagoras (survey kelas_id=3)
            ['6711', 'AGNES SHERLY BERLIAN HULU', 3],
            ['6715', 'AMEL PERMATA SARI TELAUMBANUA', 3],
            ['6720', 'BEVAN NATHANIEL HAREFA', 3],
            ['6723', 'CALVIN PUTRA JAYA ZAI', 3],
            ['6726', 'DANIEL HENDRATA ZEBUA', 3],
            ['6728', 'DAVID SIMEON LAOLI', 3],
            ['6729', 'DEARNIFIKASIH GULO', 3],
            ['6731', 'DEVER SYAH PUTRA HAREFA', 3],
            ['6733', 'ERLAND LIONEL ALVARO HAREFA', 3],
            ['6735', 'FADIL MARSUPAGDION NDRURU', 3],
            ['6741', 'GUSRIDA LAWOLO', 3],
            ['6745', 'IMEL FORPIN SARI BU\'ULOLO', 3],
            ['6746', 'IRRA FIDELIA GIAWA', 3],
            ['6752', 'JOSEP DAELI', 3],
            ['7021', 'KARYAMAN TAFONAO', 3],
            ['6754', 'KESYA EUNIKE HAREFA', 3],
            ['6756', 'KRISNA LESTARI HALAWA', 3],
            ['6758', 'MAKMUR ZEBUA', 3],
            ['6760', 'MITCHELLIA GRYTA GEA', 3],
            ['6763', 'NOEL EUKHARISTON HAREFA', 3],
            ['6766', 'OKTAVIAN PURNAMA SARI NDRURU', 3],
            ['6769', 'PRETTY INGGRID MARTHALIA ZALUKHU', 3],
            ['6774', 'RINI JULYANA LASE', 3],
            ['6777', 'ROBIN SONGLIDER HAREFA', 3],
            ['6780', 'SERTA KRISTIAN JAYA GEA', 3],
            ['6782', 'SINTA MISIRIA ZAI', 3],
            ['6786', 'TRIMAN BENNY ZEBUA', 3],
            ['6790', 'VERACITY PUTRI LOPTY MADANI ZEBUA', 3],
            ['6794', 'YARNI JELITA ZEGA', 3],
            ['6796', 'YOLANDA GRACE NDRURU', 3],
        ];

        $created = 0;

        // Delete ALL existing SMP students and their user accounts
        $existingStudents = Student::where('school_id', $this->schoolId)->get();
        $deletedCount = $existingStudents->count();
        foreach ($existingStudents as $s) {
            $s->delete();
            if ($s->user_id) {
                User::where('id', $s->user_id)->delete();
            }
        }

        // Create fresh students from survey
        foreach ($surveyStudents as [$nis, $name, $surveyKelasId]) {
            if (!isset($classroomMap[$surveyKelasId])) {
                continue;
            }

            $classroomId = $classroomMap[$surveyKelasId];

            // Create user account
            $user = User::create([
                'name' => $name,
                'username' => $nis,
                'email' => $nis . '@siswa.pembdahub.sch.id',
                'password' => Hash::make($this->defaultStudentPassword),
                'role' => 'siswa',
                'school_id' => $this->schoolId,
                'is_active' => true,
                'must_change_password' => true,
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'school_id' => $this->schoolId,
                'nisn' => '0' . $nis,
                'nis' => $nis,
                'full_name' => $name,
                'status' => 'aktif',
                'entry_year' => 2025,
            ]);
            $created++;

            // Assign to classroom via student_classes pivot
            DB::table('student_classes')->insert([
                'student_id' => $student->id,
                'classroom_id' => $classroomId,
                'academic_year_id' => $academicYearId,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("  ✓ {$deletedCount} siswa lama dihapus, {$created} siswa baru dibuat");
        $this->command->info("  ✓ {$created} student_classes assignments dibuat");
    }

    /**
     * Step 5: Sync teacher competencies (subject_teacher)
     * Derived from survey penugasan table: unique guru_id + mata_pelajaran_id pairs
     */
    private function syncTeacherCompetencies(array $teacherMap, array $subjectMap): void
    {
        $this->command->info('🎯 Syncing kompetensi guru...');

        // Unique guru → mapel pairs from survey penugasan table
        // Derived by: SELECT DISTINCT guru_id, mata_pelajaran_id FROM penugasan
        // Detailed teaching assignments from Excel Guru sheet
        // Format: [NIP, Subject Name, Classes]
        $excelAssignments = [
            ['1451768669130080', 'Matematika', 'VII-1, VII-2, IX-1, IX-2, IX-3'],
            ['1451768669130080', 'Ilmu Pengetahuan Alam', 'VII-2, VII-3'],
            ['1451768669130080', 'PJOK', 'IX-1'],
            ['0056769670130083', 'Bahasa Inggris', 'VII-1, VII-2, VIII-1, IX-1, IX-2, IX-3'],
            ['0056769670130083', 'Seni Budaya', 'IX-1, IX-2, IX-3'],
            ['1534746650300020', 'Agama Katolik', 'VII-2, VII-3, VIII-2, IX-1, IX-2, IX-3'],
            ['0549745647300052', 'Matematika', 'VII-3, VII-4, VIII-1, VIII-2, VIII-3'],
            ['0549745647300052', 'BK', 'VIII-3, IX-2'],
            ['1834757663200000', 'Ilmu Pengetahuan Sosial', 'VII-3, IX-1, IX-2, IX-3'],
            ['1834757663200000', 'Molok', 'VII-1, VII-2, VII-3, VII-4, VIII-1, VIII-2, VIII-3, IX-1, IX-2'],
            ['7247764666130150', 'PKn', 'VII-1, VII-2, VII-3, VII-4, VIII-1, VIII-2, IX-2'],
            ['7247764666130150', 'Prakarya', 'VIII-1, VIII-2, VIII-3'],
            ['GTK001', 'PKn', 'VIII-3, IX-1, IX-3'],
            ['4042772673130070', 'TIK', 'VIII-2, VIII-3'],
            ['2447764665230240', 'Pend. Agama Kristen', 'VII-1, VII-2, VII-3, VII-4, VIII-1, VIII-2, VIII-3, IX-1, IX-2, IX-3'],
            ['2447764665230240', 'Prakarya', 'VII-3'],
            ['7647767668130260', 'PJOK', 'VII-1, VII-2, VII-3, VII-4, VIII-1, VIII-2, VIII-3, IX-1, IX-2, IX-3'],
            ['7647767668130260', 'Molok', 'IX-3'],
            ['0447774675230053', 'Ilmu Pengetahuan Sosial', 'VII-1, VII-2, VII-4, VIII-1, VIII-2, VIII-3'],
            ['0447774675230053', 'Prakarya', 'VII-1, VII-2, VII-4'],
            ['0447774675230053', 'Bahasa Indonesia', 'IX-2, IX-3'],
            ['GTK002', 'TIK', 'VII-1, VII-2, VII-3, VII-4, VIII-1, IX-1, IX-2, IX-3'],
            ['9735775676230130', 'Ilmu Pengetahuan Alam', 'VII-1, VII-4, VIII-1, VIII-2, VIII-3, IX-1, IX-2, IX-3'],
            ['2734777678230050', 'Pend. Agama Islam', 'VII-1, VIII-1, IX-1'],
            ['5762777678230050', 'Bahasa Indonesia', 'VII-1, VII-2, VII-3, VII-4, VIII-1, VIII-2, VIII-3, IX-1'],
            ['GTK003', 'Bahasa Inggris', 'VII-3, VII-4, VIII-2, VIII-3'],
        ];

        $teacherIds = array_values($teacherMap);
        DB::table('subject_teacher')->whereIn('teacher_id', $teacherIds)->delete();

        $count = 0;
        foreach ($excelAssignments as [$nip, $subjectName, $classes]) {
            $teacher = Teacher::where('school_id', $this->schoolId)->where('teacher_code', $nip)->first();
            $subject = Subject::where('school_id', $this->schoolId)
                ->where(function($q) use ($subjectName) {
                    $q->where('name', $subjectName)->orWhere('subject_name', $subjectName);
                })->first();

            if ($teacher && $subject) {
                DB::table('subject_teacher')->insert([
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }

        $this->command->info("  ✓ {$count} kompetensi guru di-sync");
    }

    private function count(array $arr): int
    {
        return count($arr);
    }
}
