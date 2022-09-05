<?php
declare(strict_types=1);

use Illuminate\Database\Seeder;
use App\Models\{UsersGroup, User};
use App\Models\Admin\Coaching\Portfolio;

class UsersGroupSeeder extends Seeder
{
    const SUPERVISOR = 'Regular Supervisor Access';
    public function run()
    {
        $loan_agents = User::all();

        $portfolios = Portfolio::select('id');

        foreach ($loan_agents as $loan_agent) {
            $supervisor = $this->getTeamLead($loan_agent->work_location_id);
            $portfolio = $portfolios->inRandomOrder()->first();
            factory(UsersGroup::class)->create([
                'employee_no' => $loan_agent->employee_no,
                'portfolio_id' => $portfolio->id,
                'teamlead_employee_no' => $supervisor[array_rand($supervisor)]
            ]);
        }
    }

    private function getTeamLead($location_id)
    {
        $results = [];
        $users = User::select('employee_no')
            ->with(['roles' => function ($query) {
                $query->where('name', self::SUPERVISOR);
            }])->where('work_location_id', $location_id)->get();

        foreach ($users as $user) {
            array_push($results, $user->employee_no);
        }

        return $results;
    }
}
