<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Admin\Coaching\{DashboardRaw, DisputeDashboard};

class MonthlyCreateFormTest extends TestCase
{
    use DatabaseTransactions;
    //add this so that DatabaseTransactions will work in this test
    protected $connectionsToTransact = ['mysql-customdb2', 'mysql-customdb1'];

    private $user;
    private $dashboard_raw;

    protected function setup()
    {
        parent::setup();

        $this->user = factory(User::class)->create();
        $this->dashboard_raw = factory(DashboardRaw::class)->create();
    }

    public function testCreateMonthlyPerformanceForm()
    {
        $response = $this->actingAs($this->user)->json(
            'GET',
            route('create_monthly_form', ['employee_no' => $this->dashboard_raw->employee_no, 'month' => 5])
        );
        $response->assertStatus(200);
        $response->assertHeader("content-type", 'application/pdf');
    }
}
