<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\School;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PositionAssignmentFilterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test display name formatting with school suffix.
     */
    public function test_position_display_name_appends_suffix(): void
    {
        // 1. Create a school with type 'SMK'
        $schoolSMK = School::create([
            'name' => 'SMK Pembda Nias',
            'type' => 'SMK',
            'is_active' => true,
        ]);

        // Position name without suffix
        $position1 = new Position([
            'position_name' => 'Wakil Kepala Sekolah',
            'position_code' => 'WAKASEK',
        ]);
        $position1->school()->associate($schoolSMK);

        // Should append 'SMK'
        $this->assertEquals('Wakil Kepala Sekolah SMK', $position1->display_name);

        // Position name already containing suffix
        $position2 = new Position([
            'position_name' => 'Wali Kelas SMK',
            'position_code' => 'WALIKELAS-SMK',
        ]);
        $position2->school()->associate($schoolSMK);

        // Should not duplicate 'SMK'
        $this->assertEquals('Wali Kelas SMK', $position2->display_name);

        // Global position (no school)
        $position3 = new Position([
            'position_name' => 'Satpam',
            'position_code' => 'SATPAM',
        ]);

        // Should remain unchanged
        $this->assertEquals('Satpam', $position3->display_name);
    }
}
