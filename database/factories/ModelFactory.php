<?php
declare(strict_types=1);

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
// use App\Models\{
//     User,
//     Portfolio
// };

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {

    return [
        'employee_no' => $faker->ean8,
        'password' => Hash::make('123456'),
        'username' => $faker->userName,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'middle_name' => $faker->lastName,
        'email_address' => $faker->email,
        'birthday' => $faker->date,
        'gender' => $faker->randomElement($array = array ('M', 'F')),
        'role_id' => $faker->randomElement($array = array (1, 2, 3, 4)),
        'date_hired' => $faker->date,
        'year_graduated' => $faker->year,
        'regularization_date' => $faker->date,
        'bank_account_no' => $faker->ean8,
    ];
});

// $factory->define(Portfolio::class, function (Faker $faker) {
//     $portfolios = ['Inbox Loan', 'Comet Loans', 'Send Loan'];
//     return [
//         'name' => $faker->unique()->randomElement($portfolios),
//         'description' => $faker->realText(100),
//     ];
// });
