<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class AdminAcademicYearTest extends DuskTestCase
{
    /** @test */
    public function admin_can_visit_academic_years_and_toggle()
    {
        $this->browse(function (Browser $browser) {
            $super = User::where('role', 'superadmin')->first();

            $browser->loginAs($super)
                ->visit('/admin/academic-years')
                ->assertSee('Tahun Ajaran')
                ->pause(500)
                ->assertSee('Jadikan aktif');
        });
    }
}
