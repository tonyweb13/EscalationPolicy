<?php
declare(strict_types=1);

use Faker\Generator;
use App\Models\User;
use App\Models\Lookups\Role;

$factory->define(User::class, function (Generator $faker) {
    $universities = ['Camiling Colleges', 'De Paul College', 'FEATI University', 'KCI Colleges'];
    $courses = ['BS Accountancy', 'BS Nursing', 'BSIT', 'BA Psychology'];
    $genders = ['Male', 'Female'];

    static $order = 1;
    static $year = 2018;

    return [
        'username' => $faker->userName,
        'password' => Hash::make('123456'),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'middle_name' => $faker->lastName,
        'email_address' => $faker->email,
        'birthday' => $faker->date,
        'employee_no' => $year.sprintf("%04d", $order++),
        'date_hired' => $faker->date,
        'year_graduated' => $faker->year,
        'regularization_date' => $faker->date,
        'bank_account_no' => $faker->ean8,
        'educational_institution' => $faker->randomElement($universities),
        'course' => $faker->randomElement($courses),
        'work_location_id' => rand(1, 3),
        // we'll just random an ID from designations table since its for testing only
        'designation_id' => rand(1, 100),
        'gender' => $faker->randomElement($genders)
    ];
});
