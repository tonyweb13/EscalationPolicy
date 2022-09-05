<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Admin\Coaching\CoachingLog;

class CoachingLogCreationTest extends TestCase
{
    const FINDINGS = "Sample findings";
    const POINT_OF_DISSCUSSION = "Sample point of disscussion";
    const ACTION_PLANS = "Sample action plans";
    const AGENTS_COMMITMENT = "Sample agents commitment";
    const SUPERVISORS_COMMITMENT = "Sample supervisors commitment";
    const CREATED_BY = "SAMPLE_USER";
    const DATE_CREATED = "2019-09-09 10:10:10";
    const EMPLOYEE_NO = "20000000";
    const PRIORITY = "SAMPLE_PRIORITY";

    use DatabaseTransactions;
    //add this so that DatabaseTransactions will work in this test
    protected $connectionsToTransact = ['mysql-customdb2', 'mysql-customdb1'];

    private $user;
    private $efficiency_goal;
    private $data;

    protected function setup()
    {
        parent::setup();

        $this->user = factory(User::class)->create();
        $this->data = [
            'params' => [
                'employee_no' => self::EMPLOYEE_NO,
                'findings' => self::FINDINGS,
                'point_of_disscussion' => self::POINT_OF_DISSCUSSION,
                'action_plans' => self::ACTION_PLANS,
                'agents_commitment' => self::AGENTS_COMMITMENT,
                'supervisors_commitment' => $request->params['supervisors_commitment'],
                'created_by' => self::CREATED_BY,
                'date_created' => self::DATE_CREATED,
            ]
        ];
    }

    public function testCreateCoachingLog()
    {
        $old_coaching_log_count = CoachingLog::count();
        $response = $this->actingAs($this->user)->json(
            'POST',
            route('create_coaching_log'),
            $this->data
        );
        $response->assertStatus(200);
        $new_coaching_log_count = CoachingLog::count();
        $this->assertNotEquals($old_coaching_log_count, $new_coaching_log_count);
        $this->assertEquals($old_coaching_log_count, ($new_coaching_log_count-1));
    }
}
