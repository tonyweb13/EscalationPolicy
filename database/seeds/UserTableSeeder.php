<?php
declare(strict_types=1);

use Illuminate\Database\Seeder;
use App\Models\{User, Designation, Department, OfficeLocation};

class UserTableSeeder extends Seeder
{
    const OPERATION_MANAGER = 'Operations Manager';
    const LOAN_AGENT = 'Loan Agent';
    
    public function run()
    {
        $departments = Department::select('id');
        $designations = Designation::select('id', 'name');
        $work_location = OfficeLocation::select('id');
        $operations = Department::where('name', self::OPERATION_MANAGER)->select('id')->first();

        foreach ($designations->get() as $designation) {
            if ($designation->name === self::LOAN_AGENT) {
                $department_id = $operations;
            } else {
                $department_id = $departments->inRandomOrder()->first();
            }
            //TODO: can we use ->get() in variable declaration
            //and just use variable in foreach?
            foreach ($work_location->get() as $location) {
                factory(User::class, 3)->create([
                    'tax_status_id' => 1,
                    'address_id' => 1,
                    'civil_status' => 'Single',
                    'employment_type_id' => 1,
                    'department_id' => $department_id,
                    'cost_center_id' => 1,
                    'designation_id' => $designation->id,
                    'work_location_id' => $location,
                    'profile_pic' => null,
                    'government_credential_id' => 1,
                    'remember_token' => null,
                    'is_default_password' => true,
                    'is_active' => true,
                    'bank_type' => "Savings",
                    'wave' => null,
                    'person_to_contact_in_case_of_emergency' => null,
                    'relationship' => 'Sister',
                    'contact_person_no' => null,
                    'mobile_no' => null
                ]);
            }
        }
    }
}
