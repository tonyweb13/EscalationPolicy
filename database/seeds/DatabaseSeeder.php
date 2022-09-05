<?php
declare(strict_types=1);

use Illuminate\Database\Seeder;
use App\Utils\EnvironmentUtil;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        /*Role comes before User seeder here.*/
        // $this->call(RoleTableSeeder::class);

        /*User seeder will use the roles above created.*/
        // $this->call(UserTableSeeder::class);

        if (EnvironmentUtil::isTravis()) {
            $this->call(DbVPSSeederAndMigration::class);
            $this->call(PortfolioSeeder::class);
            $this->call(DesignationSeeder::class);
            $this->call(RoleTableSeeder::class);
            $this->call(DepartmentTableSeeder::class);
            $this->call(AclRulesSeeder::class); // customdb1
            $this->call(OfficeLocationTableSeeder::class);
            $this->call(UserTableSeeder::class);
            $this->call(RoleUserTableSeeder::class); // customdb1
            $this->call(RulesTableSeeder::class); // customdb1
            $this->call(UsersGroupSeeder::class);
        }
        $this->call([
            // CategoriesSeeder::class,
            // GravityOffensesSeeder::class,
            // OffensesSeeder::class,
            GravitiesSeeder::class,
            IncidentReportResolutionsSeeder::class,
            // HrbpClustersSeeder::class,
            EfficiencyGoalSeeder::class,
        ]);
    }
}
