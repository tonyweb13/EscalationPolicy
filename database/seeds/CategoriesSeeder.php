<?php

use Illuminate\Database\Seeder;
use App\Models\Admin\Settings\Coc\Category;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        Category::truncate();

        DB::table('categories')->insert([
            'name' => 'Attendance',
        ]);

        DB::table('categories')->insert([
            'name' => 'CWD Policy',
        ]);

        DB::table('categories')->insert([
            'name' => 'Negligence',
        ]);

        DB::table('categories')->insert([
            'name' => 'General House Rules',
        ]);

        DB::table('categories')->insert([
            'name' => 'Fraud',
        ]);

        DB::table('categories')->insert([
            'name' => 'Misconduct',
        ]);

        DB::table('categories')->insert([
            'name' => 'Poor Work Ethics',
        ]);

        DB::table('categories')->insert([
            'name' => 'Information Security',
        ]);

        DB::table('categories')->insert([
            'name' => 'Zero Tolerance Policy',
        ]);

        DB::table('categories')->insert([
            'name' => 'Compliance Breach',
        ]);

        DB::table('categories')->insert([
            'name' => 'General Company Rules and Regulations',
        ]);

        DB::table('categories')->insert([
            'name' => 'Negligence PIP',
        ]);

        DB::table('categories')->insert([
            'name' => 'LMS Guidelines',
        ]);

        DB::table('categories')->insert([
            'name' => 'Corrective Policy',
        ]);

        DB::table('categories')->insert([
            'name' => 'Attendance With Points',
        ]);

        DB::table('categories')->insert([
            'name' => 'Hard Return',
        ]);

        DB::table('categories')->insert([
            'name' => 'Warranted Escalation',
        ]);

        DB::table('categories')->insert([
            'name' => 'Attendance Supervisor and Manager',
        ]);

        DB::table('categories')->insert([
            'name' => 'Attendance Trainer',
        ]);

        DB::table('categories')->insert([
            'name' => 'Credit Repair Offer',
        ]);

        DB::table('categories')->insert([
            'name' => 'Credit Repair Offer Supervisor',
        ]);
    }
}
