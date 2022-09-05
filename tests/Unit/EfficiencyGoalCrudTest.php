<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Admin\Coaching\EfficiencyGoal;

class EfficiencyGoalCrudTest extends TestCase
{
    const PORTFOLIO_NAME = "SAMPLE_PORTFOLIO";
    const NUMBER = "1";
    const PRIORITY = "SAMPLE_PRIORITY";

    use DatabaseTransactions;
    //add this so that DatabaseTransactions will work in this test
    protected $connectionsToTransact = ['mysql-coaching', 'mysql-customdb1'];

    private $user;
    private $efficiency_goal;
    private $data;

    protected function setup()
    {
        parent::setup();

        $this->user = factory(User::class)->create();
        $this->efficiency_goal = factory(EfficiencyGoal::class)->create();
        $this->data = [
            'params' => [
                'profile_name' => ['text'=>self::PORTFOLIO_NAME, 'value'=>self::NUMBER],
                'priority' => self::PRIORITY,
                'in_call' => self::NUMBER,
                'ready' => self::NUMBER,
                'wrap_up' => self::NUMBER,
                'not_ready' => self::NUMBER
            ]
        ];
    }

    public function testCreateEfficiencyGoal()
    {
        $old_efficiency_goal_count = EfficiencyGoal::count();
        $response = $this->actingAs($this->user)->json(
            'POST',
            route('create_efficiency_goal'),
            $this->data
        );
        $response->assertStatus(200);
        $new_efficiency_goal_count = EfficiencyGoal::count();
        $this->assertNotEquals($old_efficiency_goal_count, $new_efficiency_goal_count);
        $this->assertEquals($old_efficiency_goal_count, ($new_efficiency_goal_count-1));
    }

    public function testDeleteEfficiencyGoal()
    {
        $old_efficiency_goal_count = EfficiencyGoal::count();
        $response = $this->actingAs($this->user)->json(
            'POST',
            route('delete_efficiency_goal', $this->efficiency_goal->id)
        );
        $response->assertStatus(200);
        $new_efficiency_goal_count = EfficiencyGoal::count();
        $this->assertNotEquals($old_efficiency_goal_count, $new_efficiency_goal_count);
        $this->assertEquals($old_efficiency_goal_count, ($new_efficiency_goal_count+1));
    }

    public function testUpdateEfficiencyGoal()
    {
        $old_efficiency_goal = EfficiencyGoal::where('id', $this->efficiency_goal->id)->first();
        $response = $this->actingAs($this->user)->json(
            'POST',
            route('update_efficiency_goal', $this->efficiency_goal->id),
            $this->data
        );
        $response->assertStatus(200);
        $new_efficiency_goal = EfficiencyGoal::where('id', $this->efficiency_goal->id)->first();
        $this->assertNotEquals($old_efficiency_goal->portfolio_name, $new_efficiency_goal->portfolio_name);
        $this->assertEquals($new_efficiency_goal->portfolio_name, self::PORTFOLIO_NAME);
    }
}
