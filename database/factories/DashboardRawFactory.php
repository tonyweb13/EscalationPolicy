<?php
declare(strict_types=1);

use Faker\Generator;
use App\Models\Admin\Coaching\DashboardRaw;
use Carbon\Carbon;

$factory->define(DashboardRaw::class, function (Generator $faker) {
    $prio = ['Prio 1', 'Prio 2'];
    $designation = ['Loan Originator', 'Loan'];
    $portfolio = ['Inbox Loan', 'Solid Oak Finance'];
    $site = ['Market-Market', 'BGC'];
    $attendance = ['Present', 'Off/Leave'];
    $week = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
    $tenurship = ['Tenured', 'Probitionary'];

    static $employee_id = 20171004;
    static $employee_name = "Garcia, John Rouen";
    static $report_date = "2019-05-13";

    return [
        'employee_no' => $employee_id,
        'tenureship' => $faker->randomElement($tenurship),
        'site_and_portfolio' => $faker->randomElement($portfolio),
        'site_location' => $faker->randomElement($site),
        'days' => '-',
        'coverage' => Carbon::now(),
        'scg' => rand(1, 10000),
        'mtdg' => rand(1, 10000),
        'team' => $faker->name,
        'cluster' => $faker->name,
        'loan_amount' => rand(1, 100),
        'attendance' => $faker->randomElement($attendance),
        'in_call' => rand(1, 30),
        'ready' => rand(1, 30),
        'wrap_up' => rand(1, 30),
        'not_ready' => rand(1, 30),
        "coverage_total_loan_amount" => rand(1, 10000),
        "mtd_total_loan_amount" => rand(1, 10000),
        "attendance_reliability" => rand(1, 10000),
        'major' => rand(0, 1),
        'minor' => rand(0, 1),
        'update_status' => 0,
        "report_date" => $report_date,
        'week_number' => $faker->randomElement($week),
        'running_week' => Carbon::now()
    ];
});
