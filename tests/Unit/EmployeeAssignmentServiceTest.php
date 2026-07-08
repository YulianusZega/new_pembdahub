<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\EmployeeAssignmentService;

class EmployeeAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;
    private EmployeeAssignmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EmployeeAssignmentService();
    }

    // ==========================================
    // TEACHING HONOR CALCULATION TESTS
    // ==========================================

    public function test_teaching_honor_pns_above_wajib(): void
    {
        // PNS with 26 hours, jam_wajib = 22, honor_per_jam SMA = 70K
        $result = $this->service->calculateTeachingHonor(26, 'PNS', 'SMA');

        $this->assertEquals(26, $result['jam_mengajar']);
        $this->assertEquals(22, $result['jam_wajib']);
        $this->assertEquals(4, $result['jam_honor']); // 26 - 22 = 4
        $this->assertEquals(70000, $result['honor_per_jam']);
        $this->assertEquals(280000, $result['honor_total']); // 4 × 70000
    }

    public function test_teaching_honor_pns_below_wajib(): void
    {
        // PNS with 18 hours (below wajib 22), no honor
        $result = $this->service->calculateTeachingHonor(18, 'PNS', 'SMA');

        $this->assertEquals(22, $result['jam_wajib']);
        $this->assertEquals(0, $result['jam_honor']); // max(0, 18-22) = 0
        $this->assertEquals(0, $result['honor_total']);
    }

    public function test_teaching_honor_gty_smp(): void
    {
        // GTY at SMP, 25 hours, jam_wajib=22, honor_tetap=65K
        $result = $this->service->calculateTeachingHonor(25, 'GTY', 'SMP');

        $this->assertEquals(22, $result['jam_wajib']);
        $this->assertEquals(3, $result['jam_honor']); // 25 - 22
        $this->assertEquals(65000, $result['honor_per_jam']);
        $this->assertEquals(195000, $result['honor_total']); // 3 × 65K
    }

    public function test_teaching_honor_honorer_no_jam_wajib(): void
    {
        // Honorer has jam_wajib = 0, all hours earn honor at honorer rate
        $result = $this->service->calculateTeachingHonor(24, 'Honorer', 'SMA');

        $this->assertEquals(0, $result['jam_wajib']); // honor has 0 wajib
        $this->assertEquals(24, $result['jam_honor']); // all hours
        $this->assertEquals(50000, $result['honor_per_jam']); // honorer rate
        $this->assertEquals(1200000, $result['honor_total']); // 24 × 50K
    }

    public function test_teaching_honor_pty_uses_tetap_rate(): void
    {
        // PTY uses honor_tetap rate but jam_wajib_honor = 0
        $result = $this->service->calculateTeachingHonor(20, 'PTY', 'SMK');

        $this->assertEquals(0, $result['jam_wajib']); // PTY uses jam_wajib_honor
        $this->assertEquals(20, $result['jam_honor']);
        $this->assertEquals(70000, $result['honor_per_jam']); // PTY gets tetap rate
        $this->assertEquals(1400000, $result['honor_total']);
    }

    public function test_teaching_honor_zero_hours(): void
    {
        $result = $this->service->calculateTeachingHonor(0, 'GTY', 'SMA');

        $this->assertEquals(0, $result['jam_honor']);
        $this->assertEquals(0, $result['honor_total']);
    }

    // ==========================================
    // CONSTANTS & FORMULA TESTS
    // ==========================================

    public function test_default_formulas_values(): void
    {
        $formulas = EmployeeAssignmentService::DEFAULT_FORMULAS;

        $this->assertEquals(10.0, $formulas['tunjangan_keluarga_persen']);
        $this->assertEquals(5.0, $formulas['tunjangan_anak_persen']);
        $this->assertEquals(2, $formulas['tunjangan_anak_max']);
        $this->assertEquals(50000, $formulas['tunjangan_beras']);
    }

    public function test_jam_honor_rules_per_level(): void
    {
        $smp = EmployeeAssignmentService::DEFAULT_JAM_HONOR['SMP'];
        $sma = EmployeeAssignmentService::DEFAULT_JAM_HONOR['SMA'];
        $smk = EmployeeAssignmentService::DEFAULT_JAM_HONOR['SMK'];

        $this->assertEquals(22, $smp['jam_wajib_tetap']);
        $this->assertEquals(65000, $smp['honor_tetap']);
        $this->assertEquals(65000, $smp['honor_honorer']);

        $this->assertEquals(70000, $sma['honor_tetap']);
        $this->assertEquals(50000, $sma['honor_honorer']);

        $this->assertEquals($sma, $smk); // SMA & SMK same rules
    }

    public function test_tunjangan_eligible_statuses(): void
    {
        $this->assertContains('GTY', EmployeeAssignmentService::TUNJANGAN_ELIGIBLE);
        $this->assertContains('PTY', EmployeeAssignmentService::TUNJANGAN_ELIGIBLE);
        $this->assertNotContains('PNS', EmployeeAssignmentService::TUNJANGAN_ELIGIBLE);
        $this->assertNotContains('Honorer', EmployeeAssignmentService::TUNJANGAN_ELIGIBLE);
    }

    public function test_no_gaji_pokok_statuses(): void
    {
        $this->assertContains('PNS', EmployeeAssignmentService::NO_GAJI_POKOK);
        $this->assertContains('Kontrak', EmployeeAssignmentService::NO_GAJI_POKOK);
        $this->assertNotContains('GTY', EmployeeAssignmentService::NO_GAJI_POKOK);
    }

    public function test_get_formulas_returns_defaults(): void
    {
        $formulas = $this->service->getFormulas();

        $this->assertArrayHasKey('tunjangan_keluarga_persen', $formulas);
        $this->assertArrayHasKey('tunjangan_anak_persen', $formulas);
        $this->assertArrayHasKey('tunjangan_anak_max', $formulas);
        $this->assertArrayHasKey('tunjangan_beras', $formulas);
    }

    public function test_get_jam_honor_rules_defaults_to_sma(): void
    {
        $rules = $this->service->getJamHonorRules();
        $smaRules = EmployeeAssignmentService::DEFAULT_JAM_HONOR['SMA'];

        $this->assertEquals($smaRules, $rules);
    }

    public function test_get_jam_honor_rules_for_specific_level(): void
    {
        $rules = $this->service->getJamHonorRules('SMP');
        $this->assertEquals(65000, $rules['honor_tetap']);
    }
}
