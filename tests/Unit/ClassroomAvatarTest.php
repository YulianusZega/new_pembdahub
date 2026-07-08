<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Classroom;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClassroomAvatarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test avatar config matching logic.
     */
    public function test_classroom_avatar_config_matching(): void
    {
        // 1. Sutomo should match predefined hero Sutomo, NOT "TO" (Teknik Otomotif)
        $classroomSutomo = new Classroom([
            'class_name' => 'X - Sutomo',
        ]);
        $configSutomo = $classroomSutomo->getAvatarConfig();
        $this->assertTrue($configSutomo['is_predefined']);
        $this->assertEquals('Sutomo', $configSutomo['name']);
        $this->assertNull($configSutomo['initials']);

        // 2. Class with "TO" word should match "TO"
        $classroomTO = new Classroom([
            'class_name' => 'X TO REGULER',
        ]);
        $configTO = $classroomTO->getAvatarConfig();
        $this->assertTrue($configTO['is_predefined']);
        $this->assertEquals('Teknik Otomotif (TO)', $configTO['name']);
        $this->assertEquals('TO', $configTO['initials']);

        // 3. Class with "TKJ/ACP" should match "TKJ" due to precedence in order
        $classroomTKJ = new Classroom([
            'class_name' => 'XII TKJ/ACP',
        ]);
        $configTKJ = $classroomTKJ->getAvatarConfig();
        $this->assertTrue($configTKJ['is_predefined']);
        $this->assertEquals('Teknik Komputer & Jaringan (TKJ)', $configTKJ['name']);
        $this->assertEquals('TKJ', $configTKJ['initials']);

        // 4. Gregor Mendel should match predefined scientist Gregor Mendel
        $classroomMendel = new Classroom([
            'class_name' => 'VII-Gregor Mendel',
        ]);
        $configMendel = $classroomMendel->getAvatarConfig();
        $this->assertTrue($configMendel['is_predefined']);
        $this->assertEquals('Gregor Mendel', $configMendel['name']);
        $this->assertNull($configMendel['initials']);
    }
}
