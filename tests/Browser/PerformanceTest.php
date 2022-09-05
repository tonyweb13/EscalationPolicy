<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\SharedBrowserTest;
use App\Models\User;

class PerformanceTest extends SharedBrowserTest
{
    const DASHBOARD = 'sample-upload-dashboard.csv';
    const DISPUTE_DASHBOARD = 'sample-upload-dashboard-dispute.csv';
    const DEFAULT_MONTH = "May";
    const DEFAULT_WEEK = "Week 2";
    const EMPLOYEE_NO = '20189999';

    private $user;

    public function tearDown()
    {
        parent::tearDown();
        $this->deleteUser();
    }

    public function setUp()
    {
        parent::setUp();

        $this->user = $this->createUser();
    }

    public function testUploadDashboardRaw()
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->loginAs($this->user)
                ->visit('/employee/coachinglog')
                ->assertSee('Coaching Log')
                ->attach('dashboard', base_path("tests/csv/".self::DASHBOARD))
                ->press('Upload')
                ->waitForText('Upload successfully', 20)
                ->assertSee('Upload successfully');
        });
    }

    public function testUploadDashboardRawDispute()
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->loginAs($this->user)
                ->visit('/employee/coachinglog')
                ->click('ul > li > .tab-dashboard_dispute')
                ->assertSee('Coaching Log')
                ->attach('dispute_dashboard', base_path("tests/csv/".self::DISPUTE_DASHBOARD))
                ->press('Upload')
                ->waitForText('Upload successfully', 20);
        });
    }

    public function testCreateMonthlyPerformanceForm()
    {
        $this->browse(function (Browser $browser) {
            // $browser->driver->manage()->deleteAllCookies();
            $browser->loginAs($this->user)
                ->visit('/employee/coachinglog')
                ->assertSee('Coaching Log')
                ->click('ul > li > .tab-monthly_form')
                ->assertSee('Employee Name')
                ->keys('#month', self::DEFAULT_MONTH, '{enter}')
                ->keys('#employee_name', self::EMPLOYEE_NO, '{enter}')
                ->press('Create Monthly Performance Form')
                ->pause(10000)
                ->visit('/employee/coachinglog')
                ->assertSee('Coaching Log')
                ->click('.tab-monthly_form');
        });
    }

    public function testCreateWeeklyPerformanceForm()
    {
        $this->browse(function (Browser $browser) {
            // $browser->driver->manage()->deleteAllCookies();
            $browser->loginAs($this->user)
                ->visit('/employee/coachinglog')
                ->assertSee('Create Weekly Form')
                ->click('ul > li > .tab-create_weekly')
                ->keys('#month_week_form', self::DEFAULT_MONTH, '{enter}')
                ->keys('#employee_name_week_form', self::EMPLOYEE_NO, '{enter}')
                ->keys('#week_number_week_form', self::DEFAULT_WEEK, '{enter}')
                ->press('Create Weekly Performance Form')
                ->pause(5000);
        });
    }
}
