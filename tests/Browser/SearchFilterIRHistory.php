<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\{User, Respondent, Complainant};
use App\Models\Admin\IncidentReport;
use App\Models\Admin\Settings\Coc\IncidentReportResolution;
use Tests\SharedBrowserTest;


class SearchFilterIRHistory extends SharedBrowserTest
{
    const IR_ID = '777777777';
    const EMPLOYEE_NO = '20189999';
    const DESIGNATION_ID = '37';

    private $user;
    private $respondent;
    private $complainant;
    private $irr;
    private $incident_report;

    public function tearDown()
    {
        parent::tearDown();
        $this->deleteUserLoggingIn();
    }

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'employee_no' => self::EMPLOYEE_NO,
            'designation_id' => self::DESIGNATION_ID
        ]);
        $this->irr = factory(IncidentReportResolution::class)->create();
        $this->complainant = factory(Complainant::class)->create([
            'complainant_user_id' => $this->user->id
        ]);
        $this->incident_report = factory(IncidentReport::class)->create([
            'complainant_id' => $this->complainant->id,
            'is_generate_nte_invalid_ir' => 2,
            'irr_id' => $this->irr->id,
            'hr_user_id' => $this->user->id,
        ]);
        $this->respondent = factory(Respondent::class)->create([
            'complainant_id' => $this->complainant->id,
            'ir_id' => $this->incident_report->id,
            'respondent_user_id' => $this->user->id,
        ]);
    }

    public function testSearchFilter()
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->loginAs($this->user)
                ->visit('/irhistory')
                ->assertSee('Incident Report History')
                ->waitFor('.VueTables__search .VueTables__search-field input')
                ->keys('.VueTables__search .VueTables__search-field input', self::IR_ID)
                ->waitForText(self::IR_ID, 20);
        });
    }
}
